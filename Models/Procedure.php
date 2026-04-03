<?php

namespace Wikibots\Models;

class Procedure
{
    private array $allowedRunGroups;
    private array $procedureConfig;

    public function __construct(private string $name, private string $url, private array $allowedConfigGroups, private array $allowedLogGroups) {}

    public function getName() : string
    {
        return $this->name;
    }
    public function getUrl() : string
    {
        return $this->url;
    }

    public function getAllowedConfigGroups() : array
    {
        return $this->allowedConfigGroups;
    }

    public function getAllowedConfigGroupsAsString() : string
    {
        $result = [];
        foreach ($this->allowedConfigGroups as $allowedGroup) {
            $result[] = $allowedGroup->value;
        }
        return implode(', ', $result);
    }

    public function getAllowedRunGroups() : array
    {
        if (!isset($this->allowedRunGroups)) {
            $this->allowedRunGroups = [];
            $this->loadCondig();
            foreach ($this->procedureConfig['AllowedGroups'] as $group) {
                $this->allowedRunGroups[] = UserGroup::getCaseFromValue($group);
            }
        }
        return $this->allowedRunGroups;
    }

    public function getAllowedRunGroupsAsString() : string
    {
        $this->loadCondig();
        return implode(', ', $this->procedureConfig['AllowedGroups']);
    }

    public function getAllowedLogGroups() : array
    {
        return $this->allowedLogGroups;
    }

    public function getAllowedLogGroupsAsString() : string
    {
        $result = [];
        foreach ($this->allowedLogGroups as $allowedGroup) {
            $result[] = $allowedGroup->value;
        }
        return implode(', ', $result);
    }

    private function loadCondig() : void
    {
        if (!isset($this->procedureConfig)) {
            $this->procedureConfig = IniProcessor::readConfig('Procedures'.DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'ProcedureConfig.ini');
        }
    }

    public function getDescription() : string
    {
        $this->loadCondig();
        return $this->procedureConfig['Description'];
    }

    public function getDocumentation() : string
    {
        $this->loadCondig();
        return $this->procedureConfig['Documentation'];
    }

    public function getCooldown() : int
    {
        $this->loadCondig();
        return $this->procedureConfig['Cooldown'];
    }

    public function getLastRunTimestamp() : int
    {
        return trim(file_get_contents($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'lastrun'));
    }

    private function updateLastRunTimestamp() : void
    {
        file_put_contents($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'lastrun', time());
    }

    public function getLastRunNumber() : int
    {
        return trim(file_get_contents($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'runcount'));
    }

    public function incrementLastRunNumber() : void
    {
        file_put_contents($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'runcount', $this->getLastRunNumber() + 1);
    }

    public function run($POSTdata, $comment) : string|false
    {
        if (time() < $this->getLastRunTimestamp() + $this->getCooldown() * 60) {
            return false;
        }
        $this->updateLastRunTimestamp();

        setlocale(LC_CTYPE, "en_US.UTF-8");
        set_time_limit(0);
        ignore_user_abort(true);

        $supportedParameters = array_keys(IniProcessor::readConfig('Procedures'.DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'Parameters.ini'));
        $parameterString = ' -dir '.$_ENV['APP_ROOT'].DIRECTORY_SEPARATOR.$_ENV['PYWIKIBOT_DIR'];

        foreach (array_intersect_key($POSTdata, array_flip($supportedParameters)) as $parameterName => $parameterValue) {
            $parameterString .= ' --'.$parameterName.' '.escapeshellarg($parameterValue);
        }

        $command = escapeshellcmd($_ENV['procedure_interpreter_cmd'].' '.$_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'procedure.py'.$parameterString);

        $this->incrementLastRunNumber();
        $startTime = time();
        $runId = $this->getLastRunNumber();

        $outputFilePath = $_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.date('Y-m-d_H-i-s').'_run-'.$runId.'_output.log';

        file_put_contents($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'Usage.tsv',
            $runId."\t".date('Y-m-d H:i:s')."\t".(new UserManager)->getUserName()."\t".$comment."\t".trim($parameterString).PHP_EOL,
            FILE_APPEND
        );

        $process = \proc_open($command, [["pipe", "r"],["pipe", "w"],["pipe", "w"]], $pipes);
        if (is_resource($process)) {
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            file_put_contents($outputFilePath, $output);
        } else {
            $error = 'Failed to open process: '.$command;
        }
        foreach (explode("\n", $error) as $errLine) {
            file_put_contents($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'Errors.tsv', $runId."\t".date('Y-m-d_H-i-s')."\t".trim($errLine).PHP_EOL, FILE_APPEND);
        }

        file_put_contents($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['PROCEDURES_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'Usage.tsv',
            $runId."\t".date('Y-m-d H:i:s')."\tCONSOLE\tTask finished, time: ".time() - $startTime." seconds\tN/A".PHP_EOL,
            FILE_APPEND
        );
        return $outputFilePath;
    }
}

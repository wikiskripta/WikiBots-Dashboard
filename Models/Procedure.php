<?php

namespace Wikibots\Models;

class Procedure
{
    private array $allowedRunGroups;

    public function __construct(private string $name, private string $url, private array $allowedConfigGroups, private array $allowedLogGroups) {}

    public function getName()
    {
        return $this->name;
    }
    public function getUrl()
    {
        return $this->url;
    }

    public function getAllowedConfigGroups()
    {
        return $this->allowedConfigGroups;
    }

    public function getAllowedConfigGroupsAsString()
    {
        $result = [];
        foreach ($this->allowedConfigGroups as $allowedGroup) {
            $result[] = $allowedGroup->value;
        }
        return implode(', ', $result);
    }

    public function getAllowedRunGroups()
    {
        if (!isset($this->allowedRunGroups)) {
            $this->allowedRunGroups = [];
            foreach (IniProcessor::readConfig('Procedures'.DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'ProcedureConfig.ini')['AllowedGroups'] as $group) {
                $this->allowedRunGroups[] = UserGroup::getCaseFromValue($group);
            }
        }
        return $this->allowedRunGroups;
    }

    public function getAllowedRunGroupsAsString()
    {
        return implode(', ', IniProcessor::readConfig('Procedures'.DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'ProcedureConfig.ini')['AllowedGroups']);
    }

    public function getAllowedLogGroups()
    {
        return $this->allowedLogGroups;
    }

    public function getAllowedLogGroupsAsString()
    {
        $result = [];
        foreach ($this->allowedLogGroups as $allowedGroup) {
            $result[] = $allowedGroup->value;
        }
        return implode(', ', $result);
    }

    public function run($POSTdata)
    {
        $supportedParameters = array_keys(IniProcessor::readConfig('Procedures'.DIRECTORY_SEPARATOR.$this->url().DIRECTORY_SEPARATOR.'Parameters.ini'));
        foreach (array_intersect_key($POSTdata, array_flip($supportedParameters)) as $parameterName => $parameterValue) {
            //TODO
        }
        //TODO
        return $outputFilePath;
    }
}

<?php

namespace Wikibots\Models;

class LogManager
{
    public function getLogList(string $logDir) : array
    {
        $logDir = Settings::LOG_DIR.DIRECTORY_SEPARATOR.$logDir;

        $normalLogs = array_merge(glob($logDir.'*.log'), glob($logDir.'TriggerHistory.tsv'));
        $errorLogs = glob($logDir.'Errors.tsv');

        sort($normalLogs);
        sort($errorLogs);

        return [
            'normal' => $normalLogs,
            'error' => $errorLogs
        ];
    }

    public function getLogContent(string $logPath) : string
    {
        return file_get_contents(Settings::LOG_DIR.DIRECTORY_SEPARATOR.$logPath);
    }
}
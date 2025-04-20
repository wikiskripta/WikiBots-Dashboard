<?php

namespace Wikibots\Models;

class LogManager
{
    public function getLogList(string $logDir) : array
    {
        $logDir = Settings::LOG_DIR.DIRECTORY_SEPARATOR.$logDir;

        $normalLogs = glob($logDir.'*.log');
        $usageLogs = glob($logDir.'Usage.tsv');
        $errorLogs = glob($logDir.'Errors.tsv');

        sort($normalLogs);
        sort($usageLogs);
        sort($errorLogs);

        return [
            'normal' => $normalLogs,
            'usage' => $usageLogs,
            'error' => $errorLogs
        ];
    }

    public function getLogContent(string $logPath) : string
    {
        return file_get_contents(Settings::LOG_DIR.DIRECTORY_SEPARATOR.$logPath);
    }
}
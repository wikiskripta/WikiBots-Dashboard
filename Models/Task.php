<?php

namespace Wikibots\Models;

class Task
{
    private array $taskConfig;
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
        if (!isset($this->taskConfig)) {
            $this->taskConfig = IniProcessor::readConfig('Tasks'.DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'TaskConfig.ini');
        }
    }

    public function getDescription() : string
    {
        $this->loadCondig();
        return $this->taskConfig['Description'];
    }

    public function getDocumentation() : string
    {
        $this->loadCondig();
        return $this->taskConfig['Documentation'];
    }
}
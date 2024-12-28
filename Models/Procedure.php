<?php

namespace Wikibots\Models;

class Procedure
{

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
        return IniProcessor::readConfig('Procedures'.DIRECTORY_SEPARATOR.$this->url.DIRECTORY_SEPARATOR.'ProcedureConfig.ini')['AllowedGroups'];
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
}
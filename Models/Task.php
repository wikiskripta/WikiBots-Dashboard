<?php

namespace Wikibots\Models;

class Task
{
    public function __construct(private string $name, private string $url, private array $allowedGroups) {}

    public function getName()
    {
        return $this->name;
    }
    public function getUrl()
    {
        return $this->url;
    }

    public function getAllowedGroups()
    {
        return $this->allowedGroups;
    }

    public function getAllowedGroupsAsString()
    {
        $result = [];
        foreach ($this->allowedGroups as $allowedGroup) {
            $result[] = $allowedGroup->value;
        }
        return implode(', ', $result);
    }
}
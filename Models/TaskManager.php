<?php

namespace Wikibots\Models;

class TaskManager
{

    public function getTasks()
    {
        $result = [];
        $pm = new PermissionManager();
        $tasksDict = IniProcessor::readConfig('Tasks.ini');
        foreach ($tasksDict as $taskUrl => $taskName)
        {
            $result[] = new Task($taskName, $taskUrl, $pm->getAllowedGroups(IniType::TASK_CONFIG, $taskUrl));
        }
        return $result;
    }
}
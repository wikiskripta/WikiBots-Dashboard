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
            $result[] = new Task($taskName, $taskUrl, $pm->getAllowedConfigGroups(IniType::TASK_CONFIG, $taskUrl), $pm->getAllowedLogGroups(IniType::TASK_LOG, $taskUrl));
        }
        return $result;
    }

    public function getTaskObject(string $taskId)
    {
        $pm = new PermissionManager();
        $tasksDict = IniProcessor::readConfig('Tasks.ini');
        return new Task($tasksDict[$taskId], $taskId, $pm->getAllowedConfigGroups(IniType::TASK_CONFIG, $taskId), $pm->getAllowedLogGroups(IniType::TASK_LOG, $taskId));
    }
}
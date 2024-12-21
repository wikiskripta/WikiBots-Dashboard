<?php

namespace Wikibots\Models;

class PermissionManager
{
    private array $rootFilesPermissions = [];
    private array $taskConfigsEdits = [];
    private array $proceduresConfigsEdits = [];

    public function __construct()
    {
        $data = IniProcessor::readConfig('Permissions.ini');
        foreach ($data['RootFilesEdits'] as $file => $groups)
        {
            foreach ($groups as $group) {
                $this->rootFilesPermissions[$file][] = UserGroup::getCaseFromValue($group);
            }
        }

        foreach ($data['TaskConfigsEdits'] as $file => $groups)
        {
            foreach ($groups as $group) {
                $this->taskConfigsEdits[$file][] = UserGroup::getCaseFromValue($group);
            }
        }

        foreach ($data['ProceduresConfigsEdits'] as $file => $groups)
        {
            foreach ($groups as $group) {
                $this->proceduresConfigsEdits[$file][] = UserGroup::getCaseFromValue($group);
            }
        }
    }

    public function getAllowedGroups(IniType $type, string $object)
    {
        switch ($type) {
            case IniType::ROOT_FILE:
                return $this->rootFilesPermissions[$object];
            case IniType::TASK_CONFIG:
                return $this->taskConfigsEdits[$object];
            case IniType::PROCEDURE_CONFIG:
                return $this->proceduresConfigsEdits[$object];
        }
    }
}
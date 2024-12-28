<?php

namespace Wikibots\Models;

class PermissionManager
{
    private array $rootFilesPermissions = [];
    private array $taskConfigsEdits = [];
    private array $proceduresConfigsEdits = [];
    private array $taskLogsViews = [];
    private array $procedureLogsViews = [];

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

        foreach ($data['TaskLogsViews'] as $file => $groups)
        {
            foreach ($groups as $group) {
                $this->taskLogsViews[$file][] = UserGroup::getCaseFromValue($group);
            }
        }

        foreach ($data['ProcedureLogsViews'] as $file => $groups)
        {
            foreach ($groups as $group) {
                $this->procedureLogsViews[$file][] = UserGroup::getCaseFromValue($group);
            }
        }
    }

    public function getAllowedConfigGroups(IniType $type, string $object) : array
    {
        switch ($type) {
            case IniType::ROOT_FILE:
                return $this->rootFilesPermissions[$object];
            case IniType::TASK_CONFIG:
                return $this->taskConfigsEdits[$object];
            case IniType::PROCEDURE_CONFIG:
                return $this->proceduresConfigsEdits[$object];
        }
        throw new \InvalidArgumentException('Invalid INI file type provided for Permissionmanager::getAllowedGroups().');
    }

    public function getAllowedLogGroups(IniType $type, string $object) : array
    {
        switch ($type) {
            case IniType::TASK_LOG:
                return $this->taskLogsViews[$object];
            case IniType::PROCEDURE_LOG:
                return $this->procedureLogsViews[$object];
        }
        throw new \InvalidArgumentException('Invalid INI file type provided for Permissionmanager::getAllowedLogGroups().');
    }
}
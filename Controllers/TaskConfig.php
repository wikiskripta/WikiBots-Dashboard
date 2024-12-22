<?php

namespace Wikibots\Controllers;

use Wikibots\Controllers\Controller;
use Wikibots\Models\FormCreator;
use Wikibots\Models\IniProcessor;
use Wikibots\Models\IniType;
use Wikibots\Models\PermissionManager;
use Wikibots\Models\TaskManager;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class TaskConfig extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $tm = new TaskManager();
        $task = $tm->getTaskObject(array_shift($args));

        $pm = new PermissionManager();
        $allowedGroups = $pm->getAllowedGroups(IniType::TASK_CONFIG, $task->getUrl());

        $um = new UserManager();
        if (!$um->isUserLoggedIn()){
            self::$views[] = 'loginrequired';
            self::$data['layout']['title'] = 'Je vyžadováno přihlášení';
            self::$data['loginrequired']['allowedgroups'] = array_map(function (UserGroup $g) { return $g->value; }, $allowedGroups);
            return 401;
        } else if (!$um->checkUserGroups($allowedGroups)) {
            self::$views[] = 'insufficientpermissions';
            self::$data['layout']['title'] = 'Nedostatečná oprávnění';
            self::$data['insufficientpermissions']['allowedgroups'] = array_map(function (UserGroup $g) { return $g->value; }, $allowedGroups);
            return 403;
        } else {
            self::$data['layout']['title'] = 'Správa úkonu '.$task->getUrl();
            self::$views[] = 'iniconfig';

            $configFilePath = 'Tasks'.DIRECTORY_SEPARATOR.$task->getUrl().DIRECTORY_SEPARATOR.'TaskConfig.ini';

            if (!empty($_POST)) {
                IniProcessor::writeConfig($configFilePath, $_POST);
            }

            $fc = new FormCreator();
            self::$data['iniconfig']['documentation'] = IniProcessor::readConfig($configFilePath)['Documentation'];
            self::$data['iniconfig']['formcontrols'] = $fc->generateControlsFromIni(IniProcessor::readConfig($configFilePath));
            return 200;
        }
    }
}
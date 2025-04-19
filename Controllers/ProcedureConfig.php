<?php

namespace Wikibots\Controllers;

use Wikibots\Controllers\Controller;
use Wikibots\Models\FormCreator;
use Wikibots\Models\IniProcessor;
use Wikibots\Models\IniType;
use Wikibots\Models\PermissionManager;
use Wikibots\Models\ProcedureManager;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class ProcedureConfig extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $tm = new ProcedureManager();
        $procedure = $tm->getProcedureObject(array_shift($args));

        $pm = new PermissionManager();
        $allowedGroups = $pm->getAllowedConfigGroups(IniType::PROCEDURE_CONFIG, $procedure->getUrl());

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
            self::$data['layout']['title'] = 'Správa procedury '.$procedure->getUrl();
            self::$views[] = 'iniconfig';

            $configFilePath = 'Procedures'.DIRECTORY_SEPARATOR.$procedure->getUrl().DIRECTORY_SEPARATOR.'ProcedureConfig.ini';

            if (!empty($_POST)) {
                IniProcessor::writeConfig($configFilePath, $_POST);
            }

            $fc = new FormCreator();
            self::$data['iniconfig']['documentation'] = IniProcessor::readConfig($configFilePath)['Documentation'];
            self::$data['iniconfig']['formcontrols'] = $fc->generateControlsFromConfigIni(IniProcessor::readConfig($configFilePath));
            return 200;
        }
    }
}

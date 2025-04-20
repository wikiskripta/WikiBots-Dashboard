<?php

namespace Wikibots\Controllers;

use Wikibots\Models\FormCreator;
use Wikibots\Models\IniProcessor;
use Wikibots\Models\IniType;
use Wikibots\Models\PermissionManager;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class MasterConfig extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $pm = new PermissionManager();
        $allowedGroups = $pm->getAllowedConfigGroups(IniType::ROOT_FILE, 'MasterConfig.ini');

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
            self::$data['layout']['title'] = 'Hlavní nastavení';
            self::$views[] = 'iniconfig';

            if (!empty($_POST))
            {
                IniProcessor::writeConfig('MasterConfig.ini', $_POST);
            }

            $fc = new FormCreator();
            self::$data['iniconfig']['documentation'] = 'Uživatel:Sunny/Dokumentace/MasterConfig';
            self::$data['iniconfig']['formcontrols'] = $fc->generateControlsFromConfigIni(IniProcessor::readConfig('MasterConfig.ini'));
            return 200;
        }
    }
}
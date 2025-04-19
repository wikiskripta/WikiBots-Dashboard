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

class Procedure extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $pm = new ProcedureManager();
        $procedure = $pm->getProcedureObject(array_shift($args));
        
        $allowedGroups = $procedure->getAllowedRunGroups();
        
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
            self::$data['layout']['title'] = 'Spušení procedury '.$procedure->getUrl();
            self::$views[] = 'procedurerun';

            $configFilePath = 'Procedures'.DIRECTORY_SEPARATOR.$procedure->getUrl().DIRECTORY_SEPARATOR.'ProcedureConfig.ini';
            $parametersFilePath = 'Procedures'.DIRECTORY_SEPARATOR.$procedure->getUrl().DIRECTORY_SEPARATOR.'Parameters.ini';

            if (!empty($_POST)) {
                $outputFilePath = $procedure->run($_POST);
                self::$data['procedurerun']['output'] = file_get_contents($outputFilePath);
            }

            $fc = new FormCreator();
            self::$data['procedurerun']['documentation'] = IniProcessor::readConfig($configFilePath)['Documentation'];
            self::$data['procedurerun']['formcontrols'] = $fc->generateControlsFromParametersIni(IniProcessor::readConfig($parametersFilePath));
            return 200;
        }
    }
}

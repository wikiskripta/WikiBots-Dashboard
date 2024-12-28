<?php

namespace Wikibots\Controllers;

use Wikibots\Controllers\Controller;
use Wikibots\Models\FormCreator;
use Wikibots\Models\IniProcessor;
use Wikibots\Models\IniType;
use Wikibots\Models\LogManager;
use Wikibots\Models\PermissionManager;
use Wikibots\Models\ProcedureManager;
use Wikibots\Models\TaskManager;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class ProcedureLog extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $prm = new ProcedureManager();
        $procedure = $prm->getProcedureObject(array_shift($args));

        $pm = new PermissionManager();
        $allowedGroups = $pm->getAllowedLogGroups(IniType::PROCEDURE_LOG, $procedure->getUrl());

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
            if (empty($args)) {
                self::$data['layout']['title'] = 'Záznamy spuštění procedury '.$procedure->getUrl();
                self::$views[] = 'logs';

                $logDir = 'Procedures'.DIRECTORY_SEPARATOR.$procedure->getUrl().DIRECTORY_SEPARATOR;

                $lm = new LogManager();
                self::$data['logs']['processname'] = $procedure->getUrl();
                self::$data['logs']['filelist'] = $lm->getLogList($logDir);
                return 200;
            } else if ($args[0] === 'download' || $args[0] === 'view') {
                $action = array_shift($args);
                $logfile = array_shift($args);

                self::$views = ['blank'];

                $log = 'Procedures'.DIRECTORY_SEPARATOR.$procedure->getUrl().DIRECTORY_SEPARATOR.$logfile;

                $lm = new LogManager();
                self::$data['blank']['response'] = ($action == 'view') ? '<pre>'.$lm->getLogContent($log).'</pre>' : $lm->getLogContent($log);
                return 200;
            } else {
                return 400;
            }
        }
    }
}
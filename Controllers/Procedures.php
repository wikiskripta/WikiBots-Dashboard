<?php

namespace Wikibots\Controllers;

use Wikibots\Models\IniType;
use Wikibots\Models\PermissionManager;
use Wikibots\Models\ProcedureManager;
use Wikibots\Models\TaskManager;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

/**
 * @see Controller
 */
class Procedures extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $allowedGroups = [UserGroup::MECHANIC, UserGroup::ADMINISTRATOR, UserGroup::MODERATOR, UserGroup::EDITOR];

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
            self::$data['layout']['title'] = 'Správa procedur';
            $tm = new ProcedureManager();
            self::$data['procedures']['procedures'] = $tm->getProcedures();
            self::$views[] = 'procedures';
            return 200;
        }
    }
}


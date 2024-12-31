<?php

namespace Wikibots\Controllers;

use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class Dashboard extends Controller
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
            self::$data['layout']['title'] = 'Ovládací panely';
            self::$views[] = 'dashboard';
            return 200;
        }
    }
}
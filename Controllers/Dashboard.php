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
        $um = new UserManager();
        if (!$um->isUserLoggedIn()){
            self::$views[] = 'loginrequired';
            self::$data['layout']['title'] = 'Je vyžadováno přihlášení';
            self::$data['loginrequired']['allowedgroups'] = [UserGroup::EDITOR->value];
            return 401;
        } else if (!$um->checkUserGroup(UserGroup::EDITOR)) {
            self::$views[] = 'insufficientpermissions';
            self::$data['layout']['title'] = 'Nedostatečná oprávnění';
            self::$data['insufficientpermissions']['allowedgroups'] = [UserGroup::EDITOR->value];
            return 403;
        } else {
            self::$data['layout']['title'] = 'Ovládací panely';
            self::$views[] = 'dashboard';
            return 200;
        }
    }
}
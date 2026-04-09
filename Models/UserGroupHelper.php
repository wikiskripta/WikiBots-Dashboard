<?php

namespace Wikibots\Models;

class UserGroupHelper
{

    public static function getGroupIcon(UserGroup|string $group)
    {
        if ($group instanceof UserGroup) {
            $case = $group;
        } else {
            $case = UserGroup::getCaseFromValue($group);
        }

        //I have to do this shit manually, because no matter what one-liner I try, I keep getting nonsense syntax errors
        foreach (UserGroupIcons::cases() as $currentSearchedCace) {
            if ($currentSearchedCace->name == $case->name) {
                $iconUrl = $currentSearchedCace->value;
                return '<img class="usergroup-icon" src="'.$iconUrl.'" title="'.$case->value.'" />';
            }
        }
    }
}
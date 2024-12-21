<?php

namespace Wikibots\Models;

enum UserGroup: string
{
    case ROBOT = 'bot';
    case MECHANIC = 'mechanic';
    case ADMINISTRATOR = 'bureaucrat';
    case MODERATOR = 'sysop';
    case EDITOR = 'editor';
    case PATROL = 'patrol';
    case AUTOPATROL = 'autopatrol';
    case TEACHER = 'pedagogue';
    case INTERFACE_EDITOR = 'interface-admin';
    case INSPECTOR = 'checkuser';
    case CENSOR = 'suppress';
    case REPLACE_TEXT = 'replacetext';
    case WIDGET_EDITOR = 'widgeteditor';
    case PUSH_NOTIFICATION_MANAGER = 'push-subscription-manager';

    public static function getCaseFromValue(string $groupName): UserGroup
    {
        foreach (self::cases() as $group) {
            if ($groupName === $group->value) {
                return $group;
            }
        }
        throw new \ValueError("$groupName is not a valid group name.");
    }
}

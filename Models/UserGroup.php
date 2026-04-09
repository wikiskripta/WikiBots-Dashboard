<?php

namespace Wikibots\Models;

enum UserGroup: string
{
    case MECHANIC = 'mechanic';
    case ADMINISTRATOR = 'bureaucrat';
    case MODERATOR = 'sysop';
    case EDITOR = 'editor';
    case PATROL = 'patrol';
    case AUTOPATROL = 'autopatrol';
    case TEACHER = 'pedagogue';
    case ROBOT = 'bot';
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

    public static function getGroupValue(self $group) {
        return match($group) {
            self::MECHANIC => 73,
            self::ADMINISTRATOR => 70,
            self::PUSH_NOTIFICATION_MANAGER => 60,
            self::INTERFACE_EDITOR => 52,
            self::WIDGET_EDITOR => 51,
            self::REPLACE_TEXT => 50,
            self::CENSOR => 42,
            self::INSPECTOR => 41,
            self::MODERATOR => 40,
            self::PATROL => 30,
            self::ROBOT => 22,
            self::TEACHER => 21,
            self::EDITOR => 20,
            self::AUTOPATROL => 10
        };
    }
}

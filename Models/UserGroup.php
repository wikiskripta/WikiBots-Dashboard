<?php

namespace Wikibots\Models;

enum UserGroup: string
{
    case ROBOT = 'bot';
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
}

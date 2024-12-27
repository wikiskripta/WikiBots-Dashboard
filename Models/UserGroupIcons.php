<?php

namespace Wikibots\Models;

enum UserGroupIcons: string
{
    case MECHANIC = 'https://www.wikiskripta.eu/sites/www.wikiskripta.eu/images/3/3d/Rank_Mechanik.svg';
    case ADMINISTRATOR = 'https://www.wikiskripta.eu/sites/www.wikiskripta.eu/images/f/f7/Rank_Administr%C3%A1tor.svg';
    case MODERATOR = 'https://www.wikiskripta.eu/sites/www.wikiskripta.eu/images/e/e8/Rank_Spr%C3%A1vce.svg';
    case EDITOR = 'https://www.wikiskripta.eu/sites/www.wikiskripta.eu/images/9/90/Rank_Redaktor.svg';
    case PATROL = 'https://upload.wikimedia.org/wikipedia/commons/6/67/OpenMoji-color_1F46E.svg';
    case AUTOPATROL = 'https://upload.wikimedia.org/wikipedia/commons/8/83/OpenMoji-color_15.0.0_1F9D1-200D-1F393.svg';
    case TEACHER = 'https://www.wikiskripta.eu/sites/www.wikiskripta.eu/images/a/a2/Rank_U%C4%8Ditel.svg';
    case ROBOT = 'https://www.wikiskripta.eu/sites/www.wikiskripta.eu/images/0/08/Rank_Robot.svg';
    case INTERFACE_EDITOR = 'https://upload.wikimedia.org/wikipedia/commons/e/e1/OpenMoji-color_15.0.0_1F9DA.svg';
    case INSPECTOR = 'https://upload.wikimedia.org/wikipedia/commons/6/65/OpenMoji-color_1F575.svg';
    case CENSOR = 'https://upload.wikimedia.org/wikipedia/commons/d/da/OpenMoji-color_15.0.0_1F9D9.svg';
    case REPLACE_TEXT = 'https://upload.wikimedia.org/wikipedia/commons/0/02/OpenMoji-color_15.0.0_1F9D1-200D-1F4BC.svg';
    case WIDGET_EDITOR = 'https://upload.wikimedia.org/wikipedia/commons/e/eb/OpenMoji-color_15.0.0_1F9D1-200D-1F527.svg';
    case PUSH_NOTIFICATION_MANAGER = 'https://upload.wikimedia.org/wikipedia/commons/3/3f/OpenMoji-color_1F477.svg';
}

<?php

namespace Wikibots\Models;

enum IniType
{
    case ROOT_FILE;
    case TASK_CONFIG;
    case PROCEDURE_CONFIG;
}

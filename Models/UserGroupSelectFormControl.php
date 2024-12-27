<?php

namespace Wikibots\Models;

use Wikibots\Models\FormControl;

class UserGroupSelectFormControl extends FormControl
{
    public function __construct(?string $iniKey, array $value)
    {
        $groupCount = count(UserGroup::cases());
        parent::__construct('select', true, ['size' => $groupCount, 'multiple' => true], $iniKey, $value);
    }

    public function render(): string
    {
        $groups = UserGroup::cases();
        $selectedGroups = $this->value;
        $result = '<select size="'.count($groups).'" multiple id="'.$this->iniKey.'" name="'.$this->iniKey.'[]" required>';

        foreach ($groups as $group)
        {
            $result .= "\n".'<option value="'.$group->value.'"'.(in_array($group->value, $selectedGroups) ? ' selected' : '').'>'.$group->value.'</option>';
        }

        $result .= "\n</select>";
        return $result;
    }
}


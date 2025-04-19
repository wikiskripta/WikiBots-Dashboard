<?php

namespace Wikibots\Models;

class FormCreator
{
    public function generateControlsFromConfigIni(array $iniData) : array
    {
        $result = [];
        foreach ($iniData as $iniRecordKey => $iniRecordValue) {
            $result[] = new FormControl('label', true, ['for' => $iniRecordKey], null, $iniRecordKey);
            switch (gettype($iniRecordValue)) {
                case 'boolean':
                    $result[] = new FormControl('label', true, ['for' => $iniRecordKey.'1'], null, 'TRUE');

                    $attributes = ['type' => 'radio'];
                    if ($iniRecordValue) {
                        $attributes['checked'] = '';
                    }
                    $result[] = new FormControl('input', false, $attributes, $iniRecordKey, 'true');
                    unset($attributes['checked']);

                    $result[] = new FormControl('label', true, ['for' => $iniRecordKey.'0'], null, 'FALSE');
                    if (!$iniRecordValue) {
                        $attributes['checked'] = '';
                    }
                    $result[] = new FormControl('input', false, $attributes, $iniRecordKey, 'false');
                    break;
                case 'integer':
                case 'double':
                    $result[] = new FormControl('input', false, ['type' => 'number'], $iniRecordKey, $iniRecordValue);
                    break;
                case 'array':
                    //In case INI arrays are used for more than user group selection, add some branching here
                    $result[] = new UserGroupSelectFormControl($iniRecordKey, $iniRecordValue);
                    break;
                default:
                    $result[] = new FormControl('input', false, ['type' => 'text'], $iniRecordKey, $iniRecordValue);
            }
        }
        return $result;
    }

    public function generateControlsFromParametersIni(array $iniData) : array
    {
        $result = [];
        foreach ($iniData as $parameter => $paramData) {
            $result[] = new FormControl('label', true, ['for' => $parameter], null, $paramData['label']);
            switch ($paramData['type']) {
                case 'boolean':
                    $result[] = new FormControl('label', true, ['for' => $parameter.'1'], null, 'TRUE');

                    $attributes = ['type' => 'radio'];
                    if ($paramData['default'] === 'true') {
                        $attributes['checked'] = '';
                    }
                    $result[] = new FormControl('input', false, $attributes, $parameter, 'true');
                    unset($attributes['checked']);

                    $result[] = new FormControl('label', true, ['for' => $parameter.'0'], null, 'FALSE');
                    if ($paramData['default'] === 'false') {
                        $attributes['checked'] = '';
                    }
                    $result[] = new FormControl('input', false, $attributes, $parameter, 'false');
                    break;
                case 'integer':
                case 'double':
                    $result[] = new FormControl('input', false, ['type' => 'number'], $parameter, $paramData['default']);
                    break;
                case 'array':
                    //In case INI arrays are used for more than user group selection, add some branching here
                    $result[] = new UserGroupSelectFormControl($parameter, $iniRecordValue);
                    break;
                default:
                    $result[] = new FormControl('input', false, ['type' => 'text'], $parameter, $paramData['default']);
            }
        }
        return $result;
    }
}

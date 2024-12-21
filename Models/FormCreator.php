<?php

namespace Wikibots\Models;

class FormCreator
{
    public function generateControlsFromIni(array $iniData) : array
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
                default:
                    $result[] = new FormControl('input', false, ['type' => 'text'], $iniRecordKey, $iniRecordValue);
            }
            $result[] = new FormControl('br', false, [], null, null);
        }
        return $result;
    }
}
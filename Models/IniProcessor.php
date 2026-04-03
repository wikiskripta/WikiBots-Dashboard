<?php

namespace Wikibots\Models;

use RuntimeException;

class IniProcessor
{
    public static function readConfig(string $filename)
    {
        return parse_ini_file($_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$filename, true, INI_SCANNER_TYPED);
    }

    public static function writeConfig(string $filename, array $content, bool $writeSectionsInsteadOfArrays = false)
    {
        $res = [];
        foreach($content as $key => $val)
        {
            if(is_array($val)) {
                if ($writeSectionsInsteadOfArrays) {
                    $res[] = "[$key]";
                    foreach($val as $sectionKey => $sectionValue) {
                        if(is_array($val)) {
                            $arrayKey = $sectionKey.'[]';
                            foreach($sectionValue as $arrayValue) {
                                $res[] = $arrayKey.' = '.self::getIniValue($arrayValue);
                            }
                        } else {
                            $res[] = $arrayKey.' = '.self::getIniValue($arrayValue);
                        }
                    }
                } else {
                    $key = $key.'[]';
                    foreach($val as $arrayValue) {
                        $res[] = $key.' = '.self::getIniValue($arrayValue);
                    }
                }
            }
            else {
                $res[] = $key.' = '.$val;
            }
        }
        file_put_contents($_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$filename, implode("\r\n", $res));
    }

    private static function getIniValue($value)
    {
        switch (gettype($value))
        {
            case 'integer':
            case 'double':
            case 'string':
                return $value;
            case 'NULL':
                return 'null';
            case 'boolean':
                if ($value === true)
                {
                    return 'true';
                }
                return 'false';
            default:
                throw new RuntimeException('Invalid data type: ' . gettype($value) . ' cannot be saved to INI.');
        }
    }
}
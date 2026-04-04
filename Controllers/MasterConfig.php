<?php

namespace Wikibots\Controllers;

use Wikibots\Models\FileEditor;
use Wikibots\Models\FormCreator;
use Wikibots\Models\IniProcessor;
use Wikibots\Models\IniType;
use Wikibots\Models\PermissionManager;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class MasterConfig extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $pm = new PermissionManager();
        $allowedGroups = $pm->getAllowedConfigGroups(IniType::ROOT_FILE, 'MasterConfig.ini');

        $um = new UserManager();
        if (!$um->isUserLoggedIn()){
            self::$views[] = 'loginrequired';
            self::$data['layout']['title'] = 'Je vyžadováno přihlášení';
            self::$data['loginrequired']['allowedgroups'] = array_map(function (UserGroup $g) { return $g->value; }, $allowedGroups);
            return 401;
        } else if (!$um->checkUserGroups($allowedGroups)) {
            self::$views[] = 'insufficientpermissions';
            self::$data['layout']['title'] = 'Nedostatečná oprávnění';
            self::$data['insufficientpermissions']['allowedgroups'] = array_map(function (UserGroup $g) { return $g->value; }, $allowedGroups);
            return 403;
        } else {
            self::$data['layout']['title'] = 'Hlavní nastavení';
            self::$views[] = 'iniconfig';

            if (!empty($_POST))
            {
                //Save values to Bot/user-config.py and Bot/user-password.py
                $fe = new FileEditor();
                $configLine = "family = '".$_POST['PywikibotFamily']."'";
                $result = $fe->replaceMarkedLine($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.'user-config.py', '# PHP-MARK family', $configLine);
                if (!$result) {
                    throw new \RuntimeException('Failed to save master config values to user-config.py file.');
                }
                $configLine = "('".$_POST['BotAccountUsername']."', BotPassword('".$_POST['BotName']."', '".$_POST['BotPassword']."'))";
                $result = $fe->replaceMarkedLine($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.'user-password.py', '# PHP-MARK credentials', $configLine);
                if (!$result) {
                    throw new \RuntimeException('Failed to save master config values to user-password.py file.');
                }

                //Redact sensitive info
                $_POST['BotPassword'] = '';
                //Save the rest (so we can prefill the form next time)
                IniProcessor::writeConfig('MasterConfig.ini', $_POST);
            }

            $fc = new FormCreator();
            self::$data['iniconfig']['documentation'] = $_ENV['ROOT_CONFIGS_DOCUMENTATION_PAGE_PREFIX'].'MasterConfig';
            self::$data['iniconfig']['formcontrols'] = $fc->generateControlsFromConfigIni(IniProcessor::readConfig('MasterConfig.ini'));
            return 200;
        }
    }
}
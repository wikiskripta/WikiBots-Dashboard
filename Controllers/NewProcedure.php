<?php

namespace Wikibots\Controllers;

use Wikibots\Models\IniProcessor;
use Wikibots\Models\Settings;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class Newprocedure extends Controller
{

    /**
     * @inheritDoc
     */
    public function process(array $args = []): int
    {
        $allowedGroups = [UserGroup::MECHANIC];

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
            self::$data['newprocedure']['errors'] = [];
            if (!empty($_POST)) {
                //Form just submitted
                $procedureId = $_POST['procedure-id'];
                $procedureName = $_POST['procedure-name'];
                $configGroups = $_POST['allow-config-groups'];
                $logGroups = $_POST['allow-log-groups'];

                if (!preg_match('/[^A-Za-z]/', $procedureId)) {
                    self::$data['newprocedure']['errors'][] = 'ID procedury nesmí obsahovat jiné znaky než velká a malá písmena anglické abecedy.';
                }
                if (
                    is_dir(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId) ||
                    is_dir(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId) ||
                    isset(IniProcessor::readConfig('Procedures.ini')[$procedureId])
                ) {
                    self::$data['newprocedure']['errors'][] = 'Procedura s tímto ID již existuje (nebo nebyla v minulosti úplně odstraněna).';
                }
                $availableGroups = array_map(function (UserGroup $case) { return $case->value; }, UserGroup::cases());
                foreach (array_merge($configGroups, $logGroups) as $group)
                {
                    if (!in_array($group, $availableGroups)) {
                        self::$data['newprocedure']['errors'][] .= 'Skupina '.$group.' je neznámá.';
                    }
                }

                if (!is_dir(Settings::BOT_SCRIPTS_DIR.DIRECTORY_SEPARATOR.$procedureId)) {
                    self::$data['newprocedure']['errors'][] .= 'Adresář <code>'.Settings::BOT_SCRIPTS_DIR.DIRECTORY_SEPARATOR.$procedureId.'</code> nebyl nalezen. Nejprve jej vytvořte a nahrajte do něj skripty robota pro danou proceduru.';
                }

                if (!empty(self::$data['newprocedure']['errors'])) {
                    self::$data['layout']['title'] = 'Registrace nové procedury';
                    self::$data['newprocedure']['botscriptspath'] = Settings::BOT_SCRIPTS_DIR;
                    self::$data['newprocedure']['taskid'] = $procedureId;
                    self::$data['newprocedure']['taskname'] = $procedureName;
                    self::$data['newprocedure']['configgroups'] = $configGroups;
                    self::$data['newprocedure']['loggroups'] = $logGroups;
                    self::$views[] = 'newprocedure';
                    return 200;
                }

                //Data validation OK
                mkdir(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId, 0755);
                chmod(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId, 0755);
                file_put_contents(
                    Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId.DIRECTORY_SEPARATOR.'ProcedureConfig.ini',
"Description = 
Documentation = WikiSkripta:Sunny/Dokumentace/Procedury/$procedureId
Cooldown = 0
AllowedGroups[] = mechanic"
                );
                chmod(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId.DIRECTORY_SEPARATOR.'ProcedureConfig.ini', 0644);

                mkdir(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId, 0755);
                chmod(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId, 0755);
                file_put_contents(
                    Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId.DIRECTORY_SEPARATOR.'Errors.tsv',
"ERROR NUMBER	DATE, TIME	CONTENT
"
                );
                chmod(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId.DIRECTORY_SEPARATOR.'Errors.tsv', 0644);
                file_put_contents(
                    Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId.DIRECTORY_SEPARATOR.'TriggerHistory.tsv',
                    "RUN NUMBER	DATE, TIME	USER	COMMENT
"
                );
                chmod(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Procedures'.DIRECTORY_SEPARATOR.$procedureId.DIRECTORY_SEPARATOR.'TriggerHistory.tsv', 0644);

                $currentProcedureList = IniProcessor::readConfig('Procedures.ini');
                $currentProcedureList[$procedureId] = $procedureName;
                IniProcessor::writeConfig('Procedures.ini', $currentProcedureList);

                $currentPermissions = IniProcessor::readConfig('Permissions.ini');
                $currentPermissions['ProcedureConfigsEdits'][$procedureId] = $configGroups;
                $currentPermissions['ProcedureLogsViews'][$procedureId] = $logGroups;
                IniProcessor::writeConfig('Permissions.ini', $currentPermissions, true);
                $this->redirect('/controls/procedures/'.$procedureId.'/config');
            } else {
                //Form not submitted yet
                self::$data['layout']['title'] = 'Registrace nové procedury';
                self::$data['newprocedure']['botscriptspath'] = Settings::BOT_SCRIPTS_DIR;
                self::$data['newprocedure']['procedureid'] = '';
                self::$data['newprocedure']['proceduraname'] = '';
                self::$data['newprocedure']['configgroups'] = '';
                self::$data['newprocedure']['loggroups'] = '';
                self::$views[] = 'newprocedure';
                return 200;
            }
        }
    }
}
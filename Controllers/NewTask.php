<?php

namespace Wikibots\Controllers;

use Wikibots\Models\IniProcessor;
use Wikibots\Models\Settings;
use Wikibots\Models\UserGroup;
use Wikibots\Models\UserManager;

class NewTask extends Controller
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
            self::$data['newtask']['errors'] = [];
            if (!empty($_POST)) {
                //Form just submitted
                $taskId = $_POST['task-id'];
                $taskName = $_POST['task-name'];
                $configGroups = $_POST['allow-config-groups'];
                $logGroups = $_POST['allow-log-groups'];

                if (!preg_match('/[^A-Za-z]/', $taskId)) {
                    self::$data['newtask']['errors'][] = 'ID úkonu nesmí obsahovat jiné znaky než velká a malá písmena anglické abecedy.';
                }
                if (
                    is_dir(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId) ||
                    is_dir(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId) ||
                    isset(IniProcessor::readConfig('Tasks.ini')[$taskId])
                ) {
                    self::$data['newtask']['errors'][] = 'Úkon s tímto ID již existuje (nebo nebyl v minulosti úplně odstraněn).';
                }
                $availableGroups = array_map(function (UserGroup $case) { return $case->value; }, UserGroup::cases());
                foreach (array_merge($configGroups, $logGroups) as $group)
                {
                    if (!in_array($group, $availableGroups)) {
                        self::$data['newtask']['errors'][] .= 'Skupina '.$group.' je neznámá.';
                    }
                }

                if (!is_dir(Settings::BOT_TASKS_SCRIPTS_DIR.DIRECTORY_SEPARATOR.$taskId)) {
                    self::$data['newtask']['errors'][] .= 'Adresář <code>'.Settings::BOT_TASKS_SCRIPTS_DIR.DIRECTORY_SEPARATOR.$taskId.'</code> nebyl nalezen. Nejprve jej vytvořte a nahrajte do něj skripty robota pro daný úkon.';
                }

                if (!empty(self::$data['newtask']['errors'])) {
                    self::$data['layout']['title'] = 'Registrace nového úkonu';
                    self::$data['newtask']['botscriptspath'] = Settings::BOT_TASKS_SCRIPTS_DIR;
                    self::$data['newtask']['taskid'] = $taskId;
                    self::$data['newtask']['taskname'] = $taskName;
                    self::$data['newtask']['configgroups'] = $configGroups;
                    self::$data['newtask']['loggroups'] = $logGroups;
                    self::$views[] = 'newtask';
                    return 200;
                }

                //Data validation OK
                mkdir(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId, 0755);
                chmod(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId, 0755);
                file_put_contents(
                    Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'TaskConfig.ini',
"Description = 
Documentation = WikiSkripta:Sunny/Dokumentace/Úkony/$taskId
QueueFillingEnabled = false
QueueProcessingEnabled = false
Interval = 86400"
                );
                chmod(Settings::CONFIG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'TaskConfig.ini', 0644);

                mkdir(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId, 0755);
                chmod(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId, 0755);
                file_put_contents(
                    Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'Errors.tsv',
"RUN NUMBER	DATETIME	CONTENT
"
                );
                chmod(Settings::LOG_DIR.DIRECTORY_SEPARATOR.'Tasks'.DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'Errors.tsv', 0644);

                $currentTaskList = IniProcessor::readConfig('Tasks.ini');
                $currentTaskList[$taskId] = $taskName;
                IniProcessor::writeConfig('Tasks.ini', $currentTaskList);

                $currentPermissions = IniProcessor::readConfig('Permissions.ini');
                $currentPermissions['TaskConfigsEdits'][$taskId] = $configGroups;
                $currentPermissions['TaskLogsViews'][$taskId] = $logGroups;
                IniProcessor::writeConfig('Permissions.ini', $currentPermissions, true);
                $this->redirect('/controls/tasks/'.$taskId.'/config');
            } else {
                //Form not submitted yet
                self::$data['layout']['title'] = 'Registrace nového úkonu';
                self::$data['newtask']['botscriptspath'] = Settings::BOT_TASKS_SCRIPTS_DIR;
                self::$data['newtask']['taskid'] = '';
                self::$data['newtask']['taskname'] = '';
                self::$data['newtask']['configgroups'] = '';
                self::$data['newtask']['loggroups'] = '';
                self::$views[] = 'newtask';
                return 200;
            }
        }
    }
}
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
                    is_dir($_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId) ||
                    is_dir($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId) ||
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

                if (!is_dir($_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId)) {
                    self::$data['newtask']['errors'][] .= 'Adresář <code>'.$_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId.'</code> nebyl nalezen. Nejprve jej vytvořte a nahrajte do něj skripty robota pro daný úkon.';
                }

                if (!empty(self::$data['newtask']['errors'])) {
                    self::$data['layout']['title'] = 'Registrace nového úkonu';
                    self::$data['newtask']['botscriptspath'] = $_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'];
                    self::$data['newtask']['taskid'] = $taskId;
                    self::$data['newtask']['taskname'] = $taskName;
                    self::$data['newtask']['configgroups'] = $configGroups;
                    self::$data['newtask']['loggroups'] = $logGroups;
                    self::$views[] = 'newtask';
                    return 200;
                }

                //Data validation OK
                mkdir($_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId, 0755);
                chmod($_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId, 0755);
                file_put_contents(
                    $_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'TaskConfig.ini',
"Description = 
Documentation = ".$_ENV['TASKS_DOCUMENTATION_PAGE_PREFIX']."$taskId
QueueFillingEnabled = false
QueueProcessingEnabled = false
Interval = 86400"
                );
                chmod($_ENV['CONFIG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'TaskConfig.ini', 0644);

                mkdir($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId, 0755);
                chmod($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId, 0755);
                file_put_contents(
                    $_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'Errors.tsv',
"RUN NUMBER	DATETIME	CONTENT
"
                );
                chmod($_ENV['LOG_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'].DIRECTORY_SEPARATOR.$taskId.DIRECTORY_SEPARATOR.'Errors.tsv', 0644);

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
                self::$data['newtask']['botscriptspath'] = $_ENV['PYWIKIBOT_DIR'].DIRECTORY_SEPARATOR.$_ENV['TASKS_SUBDIR_NAME'];
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
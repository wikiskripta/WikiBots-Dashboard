<?php
session_start();
//setcookie('wsdb_session', '41dtb0g3glqisalpjmtj941b7gdmgskf');
//setcookie('wsdbUserName', 'ShadyMedic');
//setcookie('wsdbUserID', '18059');
//setcookie('wsdbToken', '9dbd47145d292d9b2c312557f9cc475e');
$_SESSION['user']['userName'] = 'Tester';
$groups = [$_GET['group']];
if ($_GET['group'] === 'bureaucrat')
{
    $groups[] = 'sysop';
    $groups[] = 'editor';
    $groups[] = 'autopatrol';
}
if ($_GET['group'] === 'sysop')
{
    $groups[] = 'editor';
    $groups[] = 'autopatrol';
}
if ($_GET['group'] === 'editor')
{
    $groups[] = 'autopatrol';
}
$_SESSION['user']['userGroups'] = $groups;

header('Location: http://wikibots.local');
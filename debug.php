<?php
session_start();
$_SESSION['user']['userName'] = 'Tester';
$groups = [$_GET['group']];
if ($_GET['group'] === 'mechanic')
{
    $groups[] = 'bureaucrat';
    $groups[] = 'sysop';
    $groups[] = 'editor';
    $groups[] = 'autopatrol';

    $groups[] = 'patrol';
    $groups[] = 'bot';
    $groups[] = 'interface-admin';
    $groups[] = 'checkuser';
    $groups[] = 'suppress';
    $groups[] = 'replacetext';
    $groups[] = 'widgeteditor';
    $groups[] = 'push-subscription-manager';
}
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
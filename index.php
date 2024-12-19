<?php
namespace Wikibots;

use Wikibots\Controllers\Controller;
use Wikibots\Controllers\Router;
use Wikibots\Models\ErrorProcessor;
use \Throwable;

//Renew session and set encoding
session_start();
mb_internal_encoding('UTF-8');

//Define and set the autoloader
function autoloader(string $name): void
{
    //Replace backslashes (used in namespace path) with forward slashes (used in directory path)
    $name = str_replace('\\', '/', $name);
    //Remove root directory (in which this file is located)
    if (strpos($name, '/') !== false) {
        $folders = explode('/', $name);
        unset($folders[0]);
        $name = implode('/', $folders);
    }
    $name .= '.php';
    require $name;
}
spl_autoload_register('Wikibots\\autoloader');

//Define and set uncaught exceptions handler
function fatalExceptionHandler(Throwable $e) : void
{
    $errorCode = $e->getCode();
    $errorMsg = $e->getMessage();
    $errProc = new ErrorProcessor();
    if (!$e instanceof \PDOException) {
        $errorFound = $errProc->processError($errorCode, $errorMsg);
    } else {
        $errorFound = false;
    }

    if (!$errorFound) {
        //Unknown error – just display it hard TURN THIS OFF ON PRODUCTION
        throw $e;
    }

    $headerCode = $errProc->httpHeaderCode;
    $headerMessage = $errProc->httpHeaderMessage;
    $errorView = $errProc->errorWebpageView;
    extract($errProc->errorWebpageData);

    header("HTTP/1.0 $headerCode $headerMessage");
    require Controller::VIEWS_DIRECTORY.'/'.$errorView.'.phtml';
}
set_exception_handler('Wikibots\\fatalExceptionHandler');

//Load the URL and process the request (get data for the views)
$rooter = new Router();
$rooter->process(array($_SERVER['REQUEST_URI']));

//Load the views
$rooter->generate();

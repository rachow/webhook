<?php
/**
 *  @author: $rachow
 *
 *  Bootstrap the application!
 *
 */

use TicketTailor\Webhook\Exceptions\ExceptionHandler;

define('APP_START', microtime(true));
define('APP_DEBUG', true);
define('DS', DIRECTORY_SEPARATOR);

if (file_exists($autoloader = __DIR__ . DS . 'autoload.php')) {
    require $autoloader;
}

if (file_exists($vendor = __DIR__ . DS . 'vendor' . DS . 'autoload.php')) {
    require $vendor;
}

$exceptionHandler = new ExceptionHandler();
set_exception_handler([$exceptionHandler, 'handleException']);
set_error_handler([$exceptionHandler, 'handleError']);

if (is_dir($inc = __DIR__ . DS . 'inc')) { // PSR - can hook files to composer.json
    $dh = opendir($inc);
    while ($dh && (false !== ($file = readdir($dh)))) {
        if ($file == '.' || $file == '..') {
            continue; // skip
        }
        if (($ext = substr(strrchr($file, "."), 1)) == 'php') {
            require $inc . DS . $file;
        }
    }
}

// jolly good! - continue.
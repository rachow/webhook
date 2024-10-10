<?php
/**
 *  @author: $rachow
 *
 *  Follow PSR standards to autoload classes.
 *  Not needed when autoloader is used.
 *
 */

 /*
spl_autoload_register(function ($class) {

    // namespace to class path
    $file = str_replace('\\', '/', $class) . '.php';
    $path = str_replace(basename(__DIR__), '', __DIR__);

    if (file_exists($path . '/' . $file)) {
        require $path . '/' . $file;
    }
});
*/
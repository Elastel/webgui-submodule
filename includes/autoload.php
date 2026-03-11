<?php
spl_autoload_register(function ($class) {

    // base directory where all class files are stored
    $base_dir = __DIR__ . '/../src/';

    // convert the fully qualified class name into a file path
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    // require the file if it exists
    if (file_exists($file)) {
        require $file;
    }
});


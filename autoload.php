<?php
spl_autoload_register(function ($className) {
    // All base folders that contain PHP classes
    $directories = [
        __DIR__ . '/src/Presentation/controllers/',
        __DIR__ . '/src/Presentation/views/',
        __DIR__ . '/src/Business/',
        __DIR__ . '/src/Data/',
        __DIR__ . '/src/Infrastructure/',
        __DIR__ . '/src/Models/'
    ];

    // Requires all off the .php files in the base folders
    foreach ($directories as $dir) {
        $file = $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
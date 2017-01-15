<?php

require __DIR__ . '/../vendor/autoload.php';

//autoload classes
/*
spl_autoload_register(function ($classname) {
    require ("../classes/" . $classname . ".php");
});
*/

//settings
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

//dependencies
require __DIR__ . '/../src/dependencies.php';

//middleware
//require __DIR__ . '/../src/middleware.php';

//routes
require __DIR__ . '/../src/routes.php';

$app->run();
<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        //'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // PHP Renderer settings
        'view' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        /*
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        */

        //database
        'db' => [
            'host' => "localhost",
            'user' => "user",
            'password' => "password",
            'dbname' => "guesttrack"
        ]
    ],
];
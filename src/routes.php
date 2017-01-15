<?php
/*
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
*/

//example render
//$app->get('/hello/{variable}', function (Request $request, Response $response) {
$app->get('/hello/{variable}', function ($request, $response) {
    $variable = $request->getAttribute('variable');
    $response = $this->view->render($response, "test.phtml", ["variable" => $variable]);
    return $response;
});

//ex: http://betate.ch/imugl8
$app->get('/{shorturl}', function ($request, $response) {
    $shorturl = $request->getAttribute('shorturl');
    
    //$host = $_SERVER['HTTP_HOST'];
    $host = $request->getUri()->getHost();
    $response->getBody()->write("List: $shorturl<br>Host: $host");

    //Monolog example
    //$this->logger->addInfo("Something interesting happened");

    return $response;
});



$app->get('/', function ($request, $response, $args) {
    // Sample log message
    //$this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->view->render($response, 'index.phtml', $args);
});
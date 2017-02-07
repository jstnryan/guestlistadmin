<?php
/*
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
*/

//List or list shortlink (six characters, alphanumeric)
//  ex: http://betate.ch/i8vb6k
$app->get('/{shorturl:[A-z0-9]{6}}', function ($request, $response){
    $shorturl = $request->getAttribute('shorturl');
    
    $uri = $request->getUri();
    //$host = $_SERVER['HTTP_HOST'];
    $host = $uri->getHost();
    $response->getBody()->write("ResourceURI: $uri<br><br>List/page: $shorturl<br>Host: $host");

    //Monolog example
    //$this->logger->addInfo("Something interesting happened");

    return $response;
});

//Direct list link
$app->get('/list/{list}[/{name:.*}]', function ($request, $response){
    $list = $request->getAttribute('list');
    
    $uri = $request->getUri();
    //$host = $_SERVER['HTTP_HOST'];
    $host = $uri->getHost();
    $response->getBody()->write("ResourceURI: $uri<br><br>List (id): $list<br>Host: $host");

    //Monolog example
    //$this->logger->addInfo("Something interesting happened");

    return $response;
});

/* **************************************************************************************************
   ************************************************************************************************** */

if($container->get('auth')->isLogged()){
    //user is logged in
    
    //example render
    //$app->get('/hello/{variable}', function (Request $request, Response $response) {
    $app->get('/hello/{variable}', function ($request, $response) {
        $variable = $request->getAttribute('variable');
        $response = $this->view->render($response, "test.phtml", ["variable" => $variable]);
        return $response;
    });
    
    //reset password action
    $app->post('/login/reset', function ($request, $respoinse, $args) {

    });

    //User clicked on 'reset my password' link
    $app->get('/login/reset', function ($request, $response, $args) {

    });

    $app->post('/login', function ($request, $response, $args) {
        //Login action
    });

    $app->get('/[login]', function ($request, $response, $args) {
        // Sample log message
        //$this->logger->info("Slim-Skeleton '/' route");
        return $this->view->render($response, 'index.phtml', $args);
    });     
} else {
    //user isn't logged in
    $app->get('/[{uri:.*}]', function ($request, $response, $args){
        return $this->view->render($response, 'login.phtml', $args);
    });
};
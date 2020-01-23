<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->post('user/auth', "UserController@auth");

$router->post('user/check', "UserController@checkLogin");
$router->post('user/register', "UserController@register");
$router->get('user/{limit}/{offset}', "UserController@getAll");
$router->post('user/{limit}/{offset}', "UserController@find");
$router->post('user/p', "UserController@decrypt");
$router->delete('user/delete/{id}', "UserController@delete");
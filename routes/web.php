<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');

    $router->group(['middleware' => 'auth'], function () use ($router) {
        $router->get('/user', 'AuthController@user');
        $router->post('/logout', 'AuthController@logout');
        $router->post('/password/change', 'AuthController@passwordChange');
    });

});

$router->group(['prefix' => '/api'], function () use ($router) {
    $router->group(['prefix' => 'purchases'], function () use ($router) {

        $router->delete('/{id}', 'PurchaseController@destroy');
        $router->get('/{id}', 'PurchaseController@show');
        $router->put('/{id}', 'PurchaseController@update');

        $router->post('/', 'PurchaseController@store');
        $router->get('/', 'PurchaseController@index');
    });

    $router->group(['prefix' => 'receives'], function () use ($router) {

        $router->delete('/{id}', 'ReceiveController@destroy');
        $router->get('/{id}', 'ReceiveController@show');
        $router->put('/{id}', 'ReceiveController@update');

        $router->post('/', 'ReceiveController@store');
        $router->get('/', 'ReceiveController@index');
    });
});


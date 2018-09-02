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

$app->get('/', function () {
    return null;
});

$app->group(['prefix' => 'members'], function () use ($app) {

     $app->get('/', 'MemberController@index');
    $app->get('/change-status/{id}', 'MemberController@changeStatus');
    $app->post('/', 'MemberController@store');
    $app->get('/{id}', 'MemberController@show');
    $app->put('/{id}', 'MemberController@update');
    $app->delete('/{id}', 'MemberController@destroy');

	

    // Authentication related routes
    $app->group(['prefix' => 'auth'], function () use ($app) {

        $app->post('/register', 'AuthController@register');
        $app->post('/login', 'AuthController@login');

        #email confirmation Routes
        $app->get('email/verify', 'AuthController@checkVerificationToken');
        $app->post('email/verify', 'AuthController@verifyEmail');

        // Password Reset Routes...
        $app->get('password/reset', 'AuthController@checkResetToken');
        $app->post('password/email', 'AuthController@sendResetLinkEmail');
        $app->post('password/reset', 'AuthController@resetPassword');
    });

});

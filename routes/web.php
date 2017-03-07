<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'api'], function () {
    //登录相关
    Route::post('login.json', 'Auth\LoginController@postLogin');

    //用户相关
    Route::group(['prefix' => 'user', 'middleware' => ['jwt.auth', 'jwt.refresh']], function () {
        Route::get('list.json', 'User\UserController@getUserList');
    });

    //机房相关
    Route::group(['prefix' => 'room', 'middleware' => ['jwt.auth', 'jwt.refresh']], function () {
        Route::get('cluster.json', 'Room\ClusterController@getCluster');
        Route::post('cluster.json', 'Room\ClusterController@createCluster');
        Route::put('cluster/{clusterId}.json', 'Room\ClusterController@updateCluster');
        Route::delete('cluster/{clusterId}.json', 'Room\ClusterController@deleteCluster');
    });
});
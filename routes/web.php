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
//    Route::group(['prefix' => 'room', 'middleware' => ['jwt.auth', 'jwt.refresh']], function () {
    Route::group(['prefix' => 'room', 'middleware' => ['jwt.auth']], function () {
        //中心
        Route::get('cluster.json', 'Room\ClusterController@getCluster');
        Route::post('cluster.json', 'Room\ClusterController@createCluster');
        Route::put('cluster/{clusterId}.json', 'Room\ClusterController@updateCluster')->where('clusterId', '[0-9]+');
        Route::delete('cluster/{clusterId}.json', 'Room\ClusterController@deleteCluster')->where('clusterId', '[0-9]+');

        //网点
        Route::get('cluster/dot/{clusterId}.json', 'Room\DotController@getDotList')->where('clusterId', '[0-9]+');
        Route::get('dot/{dotId}.json', 'Room\DotController@getDot')->where('dotId', '[0-9]+');
        Route::post('dot.json', 'Room\DotController@createDot');
    });
});
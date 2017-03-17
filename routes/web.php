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

Route::get('/test', function () {
    return json_encode(['test' => 123]);
})->name('test');

Route::group(['prefix' => 'api'], function () {
    //登录相关
    Route::post('login.json', 'Auth\LoginController@postLogin');

    //用户相关
    Route::group(['prefix' => 'user', 'middleware' => ['jwt.auth', 'jwt.response']], function () {
        Route::get('list.json', 'User\UserController@getUserList');
        Route::get('info/{userId}.json', 'User\UserController@getUser')->where('userId', '[0-9]+');
        Route::post('doAdd.json', 'User\UserController@createUser');
        Route::put('doEdit/{userId}.json', 'User\UserController@updateUser')->where('userId', '[0-9]+');
        Route::put('doPassword/{userId}.json', 'User\UserController@updatePassword')->where('userId', '[0-9]+');
        Route::delete('doDelete/{userId}.json', 'User\UserController@deleteUser')->where('userId', '[0-9]+');
    });

    //机房相关
    Route::group(['prefix' => 'room', 'middleware' => ['jwt.auth', 'jwt.response']], function () {
        //中心
        Route::get('cluster.json', 'Room\ClusterController@getCluster');
        Route::post('cluster.json', 'Room\ClusterController@createCluster');
        Route::put('cluster/{clusterId}.json', 'Room\ClusterController@updateCluster')->where('clusterId', '[0-9]+');
        Route::delete('cluster/{clusterId}.json', 'Room\ClusterController@deleteCluster')->where('clusterId', '[0-9]+');

        //网点
        Route::get('cluster/dot/{clusterId}.json', 'Room\DotController@getDotList')->where('clusterId', '[0-9]+');
        Route::get('dot/{dotId}.json', 'Room\DotController@getDot')->where('dotId', '[0-9]+');
        Route::post('dot.json', 'Room\DotController@createDot');
        Route::put('dot/{dotId}.json', 'Room\DotController@updateDot')->where('dotId', '[0-9]+');
        Route::delete('dot/{dotId}.json', 'Room\DotController@deleteDot')->where('dotId', '[0-9]+');

        //机柜
        Route::get('dot/cabinet/{dotId}.json', 'Room\CabinetController@getCabinetList')->where('dotId', '[0-9]+');
        Route::get('cabinet/{cabinetId}.json', 'Room\CabinetController@getCabinet')->where('cabinetId', '[0-9]+');
        Route::post('cabinet.json', 'Room\CabinetController@createCabinet');
        Route::put('cabinet/{cabinetId}.json', 'Room\CabinetController@updateCabinet')->where('cabinetId', '[0-9]+');
        Route::delete('cabinet/{cabinetId}.json', 'Room\CabinetController@deleteCabinet')->where('cabinetId', '[0-9]+');

        //设备
        Route::get('cabinet/equipment/{cabinetId}.json', 'Room\EquipmentController@getEquipmentList')->where('cabinetId', '[0-9]+');
        Route::get('equipment/{equipmentId}.json', 'Room\EquipmentController@getEquipment')->where('equipmentId', '[0-9]+');
        Route::post('equipment.json', 'Room\EquipmentController@createEquipment');
        Route::put('equipment/{equipmentId}.json', 'Room\EquipmentController@updateEquipment')->where('equipmentId', '[0-9]+');
        Route::delete('equipment/{equipmentId}.json', 'Room\EquipmentController@deleteEquipment')->where('equipmentId', '[0-9]+');
    });
});
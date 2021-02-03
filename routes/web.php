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



// 后台
$router->group([
    'prefix' => 'admin',
    'middleware' => ['center_menu_auth', 'admin_request_log', 'access_control_allow_origin']
], function () use ($router) {
    // 菜单
    $router->group(['prefix' => 'menu'], function () use ($router) {
        $router->post('create', 'Admin\MenuController@create');
        $router->get('sync', 'Admin\MenuController@sync');
        $router->post('update', 'Admin\MenuController@update');
        $router->post('select', 'Admin\MenuController@select');
        $router->post('get', 'Admin\MenuController@get');
        $router->post('read', 'Admin\MenuController@read');
        $router->post('delete', 'Admin\MenuController@delete');
        $router->post('enable', 'Admin\MenuController@enable');
        $router->post('disable', 'Admin\MenuController@disable');
    });

    // 平台账户
    $router->group(['prefix' => 'cp_account'], function () use ($router) {
        $router->post('create', 'Admin\CpAccountController@create');
        $router->post('update', 'Admin\CpAccountController@update');
        $router->post('select', 'Admin\CpAccountController@select');
        $router->post('get', 'Admin\CpAccountController@get');
        $router->post('read', 'Admin\CpAccountController@read');
        $router->post('enable', 'Admin\CpAccountController@enable');
        $router->post('disable', 'Admin\CpAccountController@disable');
    });

    // 产品
    $router->group(['prefix' => 'product'], function () use ($router) {
        $router->post('create', 'Admin\ProductController@create');
        $router->post('update', 'Admin\ProductController@update');
        $router->post('select', 'Admin\ProductController@select');
        $router->post('get', 'Admin\ProductController@get');
        $router->post('read', 'Admin\ProductController@read');
        $router->post('enable', 'Admin\ProductController@enable');
        $router->post('disable', 'Admin\ProductController@disable');
    });

    // 队列数据
    $router->group(['prefix' => 'failed_queue'], function () use ($router) {
        $router->post('select', '\App\Common\Controllers\Admin\FailedQueueController@select');
        $router->post('read', '\App\Common\Controllers\Admin\FailedQueueController@read');
        $router->post('re_push', '\App\Common\Controllers\Admin\FailedQueueController@rePush');
    });

});


// 前台接口
$router->group([
    // 路由前缀
    'prefix' => 'front',
    // 路由中间件
    'middleware' => ['api_sign_valid', 'access_control_allow_origin']
], function () use ($router) {

    // 产品
    $router->group(['prefix' => 'product'], function () use ($router) {
        $router->post('get', 'Front\ProductController@get');
    });
});

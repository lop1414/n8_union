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

// 公开接口
$router->group([
    'prefix' => 'open',
    'middleware' => ['access_control_allow_origin','open_api_sign_valid']
], function () use ($router) {

    //行为上报
    $router->group(['prefix' => 'action_report'], function () use ($router) {
        $router->post('read', 'Open\UserController@read');// 阅读行为
        $router->post('login', 'Open\UserController@login');// 登陆行为
        $router->post('add_shortcut', 'Open\UserController@addShortcut');// 加桌行为
        $router->post('follow', 'Open\UserController@follow');// 关注行为
        $router->post('reg', 'Open\UserController@reg');// 注册
        $router->post('order', 'Open\OrderController@order');// 下单
        $router->post('complete_order', 'Open\OrderController@complete');// 完成订单
    });
});

// 后台
$router->group([
    'prefix' => 'admin',
    'middleware' => ['center_menu_auth', 'admin_request_log', 'access_control_allow_origin']
], function () use ($router) {
    // 菜单
    $router->group(['prefix' => 'menu'], function () use ($router) {
        $router->post('create', 'Admin\MenuController@create');
        $router->post('sync', 'Admin\MenuController@sync');
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
        $router->post('delete', 'Admin\CpAccountController@delete');
        $router->post('batch_enable', 'Admin\CpAccountController@batchEnable');
        $router->post('batch_disable', 'Admin\CpAccountController@batchDisable');
        $router->post('sync_product', 'Admin\CpAccountController@syncProduct');
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

    // 用户
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('select', 'Admin\UserController@select');
        $router->post('read', 'Admin\UserController@read');
    });

    // 订单
    $router->group(['prefix' => 'order'], function () use ($router) {
        $router->post('select', 'Admin\OrderController@select');
        $router->post('read', 'Admin\OrderController@read');
    });


    // union 用户
    $router->group(['prefix' => 'union_user'], function () use ($router) {
        $router->post('select', 'Admin\N8UnionUserController@select');
        $router->post('get', 'Admin\N8UnionUserController@get');
        $router->post('read', 'Admin\N8UnionUserController@read');
    });


    // 渠道
    $router->group(['prefix' => 'channel'], function () use ($router) {
        $router->post('select', 'Admin\ChannelController@select');
        $router->post('get', 'Admin\ChannelController@get');
        $router->post('read', 'Admin\ChannelController@read');
        $router->post('create', 'Admin\ChannelController@create');
        $router->post('update', 'Admin\ChannelController@update');
        $router->post('enable', 'Admin\ProductController@enable');
        $router->post('disable', 'Admin\ProductController@disable');
    });


    // CP渠道
    $router->group(['prefix' => 'cp_channel'], function () use ($router) {
        $router->post('select', 'Admin\CpChannelController@select');
        $router->post('get', 'Admin\CpChannelController@get');
        $router->post('read', 'Admin\CpChannelController@read');
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

    // CP账户
    $router->group(['prefix' => 'cp_account'], function () use ($router) {
        $router->post('read', 'Front\CpAccountController@read');
    });

});

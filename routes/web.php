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
        $router->post('sync_account', 'Admin\CpAccountController@syncAccount');
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

    // open api 签名验证
    $router->group(['prefix' => 'auth'], function () use ($router) {
        $router->post('signAuth', 'Front\OpenApiController@auth');
    });

    $router->group(['prefix' => 'n8_global_user'], function () use ($router) {
        $router->post('read', 'Front\N8GlobalUserController@read');
        $router->post('make', 'Front\N8GlobalUserController@make');
        $router->post('del', 'Front\N8GlobalUserController@del');
    });

    $router->group(['prefix' => 'n8_global_order'], function () use ($router) {
        $router->post('read', 'Front\N8GlobalOrderController@read');
        $router->post('make', 'Front\N8GlobalOrderController@make');
    });

    // 联运用户
    $router->group(['prefix' => 'n8_union_user'], function () use ($router) {
        $router->post('create', 'Front\N8UnionUserController@create');
    });


    // 渠道
    $router->group(['prefix' => 'n8_channel'], function () use ($router) {
        $router->post('read', 'Front\N8ChannelController@read');
        $router->post('readByCpChannel', 'Front\N8ChannelController@readByCpChannel');
    });
});

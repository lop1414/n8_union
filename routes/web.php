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


    // 用户行为
    $router->group(['prefix' => 'user_action'], function () use ($router) {
        // union 用户
        $router->group(['prefix' => 'union_user'], function () use ($router) {
            $router->post('select', 'Admin\N8UnionUserController@select');
            $router->post('read', 'Admin\N8UnionUserController@read');
        });
        // 加桌
        $router->group(['prefix' => 'add_shortcut'], function () use ($router) {
            $router->post('select', 'Admin\UserShortcutActionController@select');
            $router->post('read', 'Admin\UserShortcutActionController@read');
        });
        // 关注
        $router->group(['prefix' => 'follow'], function () use ($router) {
            $router->post('select', 'Admin\UserFollowActionController@select');
            $router->post('read', 'Admin\UserFollowActionController@read');
        });
    });



    // 多平台渠道
    $router->group(['prefix' => 'multi_plat_form_channel'], function () use ($router) {
        $router->post('select', 'Admin\MultiPlatFormChannelController@select');
        $router->post('get', 'Admin\MultiPlatFormChannelController@get');
        $router->post('read', 'Admin\MultiPlatFormChannelController@read');
        $router->post('create', 'Admin\MultiPlatFormChannelController@create');
        $router->post('update', 'Admin\MultiPlatFormChannelController@update');
        $router->post('enable', 'Admin\MultiPlatFormChannelController@enable');
        $router->post('disable', 'Admin\MultiPlatFormChannelController@disable');
    });
    // 渠道
    $router->group(['prefix' => 'channel'], function () use ($router) {
        $router->post('select', 'Admin\ChannelController@select');
        $router->post('get', 'Admin\ChannelController@get');
        $router->post('read', 'Admin\ChannelController@read');
        $router->post('sync', 'Admin\ChannelController@sync');
    });
    $router->group(['prefix' => 'channel_extend'], function () use ($router) {
        $router->post('batch_save', 'Admin\ChannelExtendController@batchSave');
        $router->post('create', 'Admin\ChannelExtendController@create');
        $router->post('update', 'Admin\ChannelExtendController@update');
        $router->post('enable', 'Admin\ChannelExtendController@enable');
        $router->post('disable', 'Admin\ChannelExtendController@disable');
    });


    // 抽奖
    $router->group(['prefix' => 'lottery'], function () use ($router) {
        $router->post('select', 'Admin\LotteryController@select');
        $router->post('read', 'Admin\LotteryController@read');
        $router->post('create', 'Admin\LotteryController@create');
        $router->post('update', 'Admin\LotteryController@update');
        $router->post('enable', 'Admin\LotteryController@enable');
        $router->post('disable', 'Admin\LotteryController@disable');
    });


    // 抽奖奖品
    $router->group(['prefix' => 'lottery_prize'], function () use ($router) {
        $router->post('select', 'Admin\LotteryPrizeController@select');
        $router->post('read', 'Admin\LotteryPrizeController@read');
        $router->post('create', 'Admin\LotteryPrizeController@create');
        $router->post('update', 'Admin\LotteryPrizeController@update');
        $router->post('enable', 'Admin\LotteryController@enable');
        $router->post('disable', 'Admin\LotteryController@disable');
    });


    // 队列数据
    $router->group(['prefix' => 'failed_queue'], function () use ($router) {
        $router->post('select', '\App\Common\Controllers\Admin\FailedQueueController@select');
        $router->post('read', '\App\Common\Controllers\Admin\FailedQueueController@read');
        $router->post('re_push', '\App\Common\Controllers\Admin\FailedQueueController@rePush');
        $router->post('re_push_by_type', '\App\Common\Controllers\Admin\FailedQueueController@rePushAllByType');
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

    // 书籍
    $router->group(['prefix' => 'book'], function () use ($router) {
        $router->post('create', 'Front\BookController@create');
    });

    // 章节
    $router->group(['prefix' => 'chapter'], function () use ($router) {
        $router->post('create', 'Front\ChapterController@create');
    });

    // 渠道
    $router->group(['prefix' => 'channel'], function () use ($router) {
        $router->post('create', 'Front\ChannelController@create');
    });

    // 渠道扩展
    $router->group(['prefix' => 'channel_extend'], function () use ($router) {
        $router->post('create', 'Front\ChannelExtendController@create');
    });

    //用户行为
    $router->group(['prefix' => 'user_action'], function () use ($router) {
        //订单
        $router->group(['prefix' => 'order'], function () use ($router) {
            $router->post('get', 'Front\UserAction\OrderActionController@get');
        });
        //加桌
        $router->group(['prefix' => 'add_shortcut'], function () use ($router) {
            $router->post('get', 'Front\UserAction\AddShortcutActionController@get');
        });
        //关注
        $router->group(['prefix' => 'follow'], function () use ($router) {
            $router->post('get', 'Front\UserAction\FollowActionController@get');
        });
        //注册
        $router->group(['prefix' => 'reg'], function () use ($router) {
            $router->post('get', 'Front\UserAction\RegActionController@get');
        });
    });
});

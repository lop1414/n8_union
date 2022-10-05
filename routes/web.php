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
    'middleware' => ['access_control_allow_origin']
], function () use ($router) {

    //行为上报
    $router->group([
        'prefix' => 'action_report',
        'middleware' => 'open_api_sign_valid'
    ], function () use ($router) {
        $router->post('read', 'Open\UserController@read');// 阅读行为
        $router->post('login', 'Open\UserController@login');// 登陆行为
        $router->post('add_shortcut', 'Open\UserController@addShortcut');// 加桌行为
        $router->post('follow', 'Open\UserController@follow');// 关注行为
        $router->post('reg', 'Open\UserController@reg');// 注册
        $router->post('order', 'Open\OrderController@order');// 下单
        $router->post('complete_order', 'Open\OrderController@complete');// 完成订单
    });

    $router->post('yw/action_report/read', 'Open\YwController@read');// 阅文-阅读行为
});


// 后台
$router->group([
    'prefix' => 'admin',
    'middleware' => ['center_menu_auth', 'admin_request_log', 'access_control_allow_origin']
], function () use ($router) {
    // 枚举
    $router->group(['prefix' => 'enum'], function () use ($router) {
        $router->post('get', 'Admin\EnumController@get');
    });

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

    // 平台分成
    $router->group(['prefix' => 'cp_commission'], function () use ($router) {
        $router->post('update', 'Admin\CpCommissionController@update');
        $router->post('select', 'Admin\CpCommissionController@select');
    });

    // 书籍分成
    $router->group(['prefix' => 'book_commission'], function () use ($router) {
        $router->post('update', 'Admin\BookCommissionController@update');
        $router->post('create', 'Admin\BookCommissionController@create');
        $router->post('select', 'Admin\BookCommissionController@select');
    });

    // 书籍标签
    $router->group(['prefix' => 'book_label'], function () use ($router) {
        $router->post('update', 'Admin\BookLabelController@update');
        $router->post('create', 'Admin\BookLabelController@create');
        $router->post('select', 'Admin\BookLabelController@select');
        $router->post('get', 'Admin\BookLabelController@get');
        $router->post('assign_book', 'Admin\BookLabelController@assignBook');
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
        $router->post('distribution', 'Admin\ProductController@distribution');
    });

    $router->group(['prefix' => 'product_weixin_mini_program'], function () use ($router) {
        $router->post('save', 'Admin\ProductWeixinMiniProgramController@save');
        $router->post('read', 'Admin\ProductWeixinMiniProgramController@read');
    });

    // 用户
    $router->group(['prefix' => 'user'], function () use ($router) {
        $router->post('select', 'Admin\UserController@select');
        $router->post('read', 'Admin\UserController@read');
    });


    // 用户行为
    $router->group(['prefix' => 'user_action'], function () use ($router) {
        // union 用户
        $router->group(['prefix' => 'union_user'], function () use ($router) {
            $router->post('select', 'Admin\UserAction\N8UnionUserController@select');
            $router->post('read', 'Admin\UserAction\N8UnionUserController@read');
        });
        // 加桌
        $router->group(['prefix' => 'add_shortcut'], function () use ($router) {
            $router->post('select', 'Admin\UserAction\UserShortcutActionController@select');
            $router->post('read', 'Admin\UserAction\UserShortcutActionController@read');
        });
        // 关注
        $router->group(['prefix' => 'follow'], function () use ($router) {
            $router->post('select', 'Admin\UserAction\UserFollowActionController@select');
            $router->post('read', 'Admin\UserAction\UserFollowActionController@read');
        });
    });

    // 用户书籍阅读
    $router->group(['prefix' => 'user_book_read'], function () use ($router) {
        $router->post('select', 'Admin\UserAction\UserBookReadController@select');
    });

    // 订单
    $router->group(['prefix' => 'order'], function () use ($router) {
        $router->post('select', 'Admin\UserAction\OrderController@select');
        $router->post('read', 'Admin\UserAction\OrderController@read');
    });


    // 渠道
    $router->group(['prefix' => 'channel'], function () use ($router) {
        $router->post('select', 'Admin\ChannelController@select');
        $router->post('get', 'Admin\ChannelController@get');
        $router->post('read', 'Admin\ChannelController@read');
        $router->post('sync', 'Admin\ChannelController@sync');
        $router->post('copy', 'Admin\ChannelController@copy');
        $router->post('create', 'Admin\ChannelController@create');
        $router->post('create_with_sync', 'Admin\ChannelController@createWithSync');
        $router->post('get_not_bind_channel', 'Admin\ChannelController@getNotBindChannel');
    });
    $router->group(['prefix' => 'channel_extend'], function () use ($router) {
        $router->post('batch_save', 'Admin\ChannelExtendController@batchSave');
        $router->post('create', 'Admin\ChannelExtendController@create');
        $router->post('update', 'Admin\ChannelExtendController@update');
        $router->post('enable', 'Admin\ChannelExtendController@enable');
        $router->post('disable', 'Admin\ChannelExtendController@disable');
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

    // 书籍
    $router->group(['prefix' => 'book'], function () use ($router) {
        $router->post('select', 'Admin\BookController@select');
        $router->post('get', 'Admin\BookController@get');
        $router->post('read', 'Admin\BookController@read');
    });
    // 章节
    $router->group(['prefix' => 'chapter'], function () use ($router) {
        $router->post('select', 'Admin\ChapterController@select');
    });
    // 测试书籍
    $router->group(['prefix' => 'test_book'], function () use ($router) {
        $router->post('select', 'Admin\TestBookController@select');
        $router->post('read', 'Admin\TestBookController@read');
        $router->post('create', 'Admin\TestBookController@create');
        $router->post('update', 'Admin\TestBookController@update');
        $router->post('enable', 'Admin\TestBookController@enable');
        $router->post('disable', 'Admin\TestBookController@disable');
        $router->post('assign_test_book_group', 'Admin\TestBookController@assignTestBookGroup');
    });
    // 测试书籍组
    $router->group(['prefix' => 'test_book_group'], function () use ($router) {
        $router->post('select', 'Admin\TestBookGroupController@select');
        $router->post('read', 'Admin\TestBookGroupController@read');
        $router->post('create', 'Admin\TestBookGroupController@create');
        $router->post('update', 'Admin\TestBookGroupController@update');
        $router->post('enable', 'Admin\TestBookGroupController@enable');
        $router->post('disable', 'Admin\TestBookGroupController@disable');
        $router->post('assign_admin_user', 'Admin\TestBookGroupController@assignAdminUser');

    });


    // ua设备信息
    $router->group(['prefix' => 'ua_device'], function () use ($router) {
        $router->post('select', 'Admin\UaDeviceController@select');
        $router->post('update', 'Admin\UaDeviceController@update');
    });



    // 抽奖
    $router->group(['prefix' => 'lottery'], function () use ($router) {
        $router->post('select', 'Admin\LotteryController@select');
        $router->post('read', 'Admin\LotteryController@read');
        $router->post('create', 'Admin\LotteryController@create');
        $router->post('update', 'Admin\LotteryController@update');
        $router->post('enable', 'Admin\LotteryController@enable');
        $router->post('disable', 'Admin\LotteryController@disable');
        $router->post('release', 'Admin\LotteryController@release');
    });


    // 抽奖奖品
    $router->group(['prefix' => 'lottery_prize'], function () use ($router) {
        $router->post('select', 'Admin\LotteryPrizeController@select');
        $router->post('read', 'Admin\LotteryPrizeController@read');
        $router->post('create', 'Admin\LotteryPrizeController@create');
        $router->post('update', 'Admin\LotteryPrizeController@update');
        $router->post('enable', 'Admin\LotteryPrizeController@enable');
        $router->post('disable', 'Admin\LotteryPrizeController@disable');
        $router->post('update_order', 'Admin\LotteryPrizeController@updateOrder');
    });


    // 获奖记录
    $router->group(['prefix' => 'lottery_prize_log'], function () use ($router) {
        $router->post('select', 'Admin\LotteryPrizeLogController@select');
        $router->post('read', 'Admin\LotteryPrizeLogController@read');
    });


    // 微信小程序
    $router->group(['prefix' => 'weixin_mini_program'], function () use ($router) {
        $router->post('select', 'Admin\WeixinMiniProgramController@select');
        $router->post('get', 'Admin\WeixinMiniProgramController@get');
        $router->post('read', 'Admin\WeixinMiniProgramController@read');
        $router->post('create', 'Admin\WeixinMiniProgramController@create');
        $router->post('update', 'Admin\WeixinMiniProgramController@update');
    });


    // 队列数据
    $router->group(['prefix' => 'failed_queue'], function () use ($router) {
        $router->post('select', '\App\Common\Controllers\Admin\FailedQueueController@select');
        $router->post('read', '\App\Common\Controllers\Admin\FailedQueueController@read');
        $router->post('enable', '\App\Common\Controllers\Admin\FailedQueueController@enable');
        $router->post('disable', '\App\Common\Controllers\Admin\FailedQueueController@disable');
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


    // 多平台渠道
    $router->group(['prefix' => 'multi_platform_channel'], function () use ($router) {
        $router->post('read', 'Front\MultiPlatChannelController@read');
    });

    // 渠道
    $router->group(['prefix' => 'channel'], function () use ($router) {
        $router->post('get', 'Front\ChannelController@get');
        $router->post('read', 'Front\ChannelController@read');
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

// 前端接口（签名）
$router->group([
    'prefix' => 'front',
    'middleware' => ['simple_sign_valid']
], function () use ($router) {
    $router->post('lottery/read', 'Front\LotteryController@read');
    $router->post('lottery/draw', 'Front\LotteryController@draw');
    $router->post('lottery/prize_log', 'Front\LotteryController@prizeLog');

    $router->post('open_user/bind', 'Front\OpenUserController@bind');
    $router->post('open_user/info', 'Front\OpenUserController@info');
});

// 前端接口（无签名）
$router->group([
    'prefix' => 'front',
    'middleware' => ['access_control_allow_origin']
], function () use ($router) {
    $router->get('qywx/echo_str', 'Front\QywxController@echoStr');
    $router->post('qywx/echo_str', 'Front\QywxController@echoStr');
});


$router->group([
    // 路由前缀
    'prefix' => 'front',
    // 路由中间件
    'middleware' => ['access_control_allow_origin','simple_sign_valid']
], function () use ($router) {
    $router->post('url_link/make', 'Front\MiniProgramUrlLinkController@make');
});


// 测试
$router->post('test', 'TestController@test');


//解决options跨域问题
$router->options('front/url_link/make', '\\App\Common\Controllers\Front\CrossController@index');

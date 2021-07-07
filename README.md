###切换开发分支 并提交到远程仓库
```
git checkout -b develop
git push
```
###composer 安装 lumen 6.0
 composer create-project --prefer-dist laravel/lumen n8_union 6.0

###.gitignore 添加忽略
```
composer.lock
/app/Common
```

###命令行生成 .env APP_KEY
```
 php -r "echo md5(uniqid());";
```

###配置 .env
```
APP_NAME=Feishu
APP_ENV=local
APP_KEY=2396a061841571c7a34010e41bfe7b87
APP_DEBUG=true
APP_URL=http://feishu.niuyue.test
APP_TIMEZONE=PRC
APP_LOCALE=zh-CN

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=feishu
DB_USERNAME=root
DB_PASSWORD=
DB_TIMEZONE=+08:00

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

\\ 添加
APP_PREFIX=n8
APP_SYSTEM_ALIAS=UNION
APP_REDIS_PREFIX=n8_union
 ```
#修改 bootstrap/app.php
```
$app->withFacades(true, [
// 解决与php event扩展冲突
'Illuminate\Support\Facades\Event' => 'LumenEvent',
]);

//开启ORM
$app->withEloquent();
// 请求时间戳
defined('TIMESTAMP') || define('TIMESTAMP', time());

```
###bootstrap/app.php 
```
//修改异常处理
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Common\Exceptions\CustomHandler::class
);

//中注册路由中间件
$app->routeMiddleware([
    'access_control_allow_origin' => \App\Common\Middleware\AccessControlAllowOrigin::class,
    'api_sign_valid' => \App\Common\Middleware\ApiSignValid::class,
    'center_login_auth' => \App\Common\Middleware\CenterLoginAuth::class,
    'center_menu_auth' => \App\Common\Middleware\CenterMenuAuth::class,
    'admin_request_log' => \App\Common\Middleware\AdminRequestLog::class,
]);
```
 
###composer.json 添加依赖库
```
 "require": {
        "php": "^7.2",
        "ext-json": "*",
        "ext-openssl": "*",
        "guzzlehttp/guzzle": "^7.0",
        "illuminate/redis": "^6.0",
        "jenssegers/agent": "^2.6",
        "laravel/lumen-framework": "^6.0",
        "predis/predis": "1.1.*"
    },
```

###添加n8_common公共sql

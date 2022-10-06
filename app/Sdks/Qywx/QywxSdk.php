<?php

namespace App\Sdks\Qywx;

use App\Sdks\Qywx\Traits\AccessToken;
use App\Sdks\Qywx\Traits\Msg;
use App\Sdks\Qywx\Traits\Request;

class QywxSdk
{
    use Request;
    use AccessToken;
    use Msg;

    /**
     * 公共接口地址
     */
    const BASE_URL = 'https://qyapi.weixin.qq.com/cgi-bin';

    /**
     * @param $uri
     * @return string
     * 获取请求地址
     */
    public function getUrl($uri){
        return self::BASE_URL .'/'. ltrim($uri, '/');
    }
}

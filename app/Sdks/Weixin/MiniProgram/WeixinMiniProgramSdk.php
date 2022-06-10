<?php

namespace App\Sdks\Weixin\MiniProgram;

use App\Sdks\Weixin\MiniProgram\Traits\Auth;
use App\Sdks\Weixin\MiniProgram\Traits\Request;
use App\Sdks\Weixin\MiniProgram\Traits\UrlLink;

class WeixinMiniProgramSdk
{
    use Request;
    use Auth;
    use UrlLink;

    /**
     * 公共接口地址
     */
    const BASE_URL = 'https://api.weixin.qq.com';


    /**
     * @param $uri
     * @return string
     * 获取请求地址
     */
    public function getUrl($uri){
        return self::BASE_URL .'/'. ltrim($uri, '/');
    }
}

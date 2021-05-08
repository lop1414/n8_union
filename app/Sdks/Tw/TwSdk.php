<?php

namespace App\Sdks\Tw;


use App\Sdks\Tw\Traits\CpChannel;
use App\Sdks\Tw\Traits\Request;

class TwSdk
{

    use Request;
    use CpChannel;


    /**
     * @var
     * 密钥
     */
    protected $secret;

    /**
     * @var
     * CP产品ID
     */
    protected $appId;



    /**
     * 公共接口地址
     */
    const BASE_URL = 'https://api.tengwen018.com/package';


    public function __construct($appId,$secret){
        $this->appId = $appId;
        $this->secret = $secret;
    }

    /**
     * @param $uri
     * @return string
     * 获取请求地址
     */
    public function getUrl($uri){
        return self::BASE_URL .'/'. ltrim($uri, '/');
    }

    /**
     * @param string $path
     * @return string
     * 获取 sdk 路径
     */
    public function getSdkPath($path = ''){
        $path = rtrim($path, '/');
        $sdkPath = rtrim(__DIR__ .'/'. $path, '/');
        return $sdkPath;
    }


    public function getSign($time = TIMESTAMP){
        return md5($this->appId.$this->secret.$time);
    }

}

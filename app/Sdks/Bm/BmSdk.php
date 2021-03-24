<?php

namespace App\Sdks\Bm;


use App\Sdks\Bm\Traits\CpChannel;
use App\Sdks\Bm\Traits\Request;

class BmSdk
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
     * 产品ID
     */
    protected $appId;



    /**
     * 公共接口地址
     */
    const BASE_URL = 'https://api_admin.quick.bimo8.com';


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

}

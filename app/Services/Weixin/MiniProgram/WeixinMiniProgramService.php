<?php

namespace App\Services\Weixin;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Sdks\Weixin\MiniProgram\WeixinMiniProgramSdk;

class WeixinMiniProgramService extends BaseService
{
    /**
     * @var WeixinMiniProgramSdk
     */
    protected $sdk;

    /**
     * @var
     * app id
     */
    protected $appId;

    /**
     * @var
     * app 密钥
     */
    protected $appSecret;

    /**
     * constructor.
     */
    public function __construct(){
        $this->sdk = new WeixinMiniProgramSdk();
    }

    /**
     * @param $appId
     * @return bool
     * @throws CustomException
     * 设置 app
     */
    public function setApp($appId){
        $this->setAppId($appId);

        $map = [
            'wx132d925a72107fc8' => [
                'app_id' => 'wx132d925a72107fc8',
                'app_secret' => 'f5094fbeb72068fb51753529798c9127',
            ],
        ];

        #TODO:库中读取app

        if(!isset($map[$this->appId])){
            throw new CustomException([
                'code' => 'NOT_FOUND_APP',
                'message' => '找不到对应app',
                'data' => [
                    'app_id' => $this->appId,
                ],
                'log' => true,
            ]);
        }

        $this->appSecret = $map[$this->appId]['app_secret'];

        return true;
    }

    protected function setAppId($appId){
        $this->appId = $appId;
        return true;
    }

    protected function getAppId(){
        if(empty($this->appId)){
            throw new CustomException([
                'code' => 'APP_ID_IS_EMPTY',
                'message' => 'app_id尚未设置'
            ]);
        }
        return $this->appId;
    }

    /**
     * @param $jsCode
     * @return mixed
     * @throws CustomException
     * 凭借 jscode 获取 openid
     */
    public function getOpenIdByJsCode22($jsCode){
        $result = $this->sdk->getOpenIdByJsCode($this->getAppId(), $this->appSecret, $jsCode);
        return $result['openid'];
    }
}

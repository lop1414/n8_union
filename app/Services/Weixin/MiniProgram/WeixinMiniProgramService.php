<?php

namespace App\Services\Weixin\MiniProgram;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\WeixinMiniProgramModel;
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
     * @var
     * token
     */
    protected $accessToken;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();
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

        // 获取小程序密钥
        $weixinMiniProgramModel = new WeixinMiniProgramModel();
        $map = $weixinMiniProgramModel->get()->makeVisible('app_secret')->keyBy('app_id')->toArray();
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
        $this->accessToken = $map[$this->appId]['access_token'];
        return true;
    }

    protected function setAppId($appId){
        $this->appId = $appId;
        return true;
    }

    /**
     * @return mixed
     * @throws CustomException
     * 获取 app id
     */
    protected function getAppId(){
        if(empty($this->appId)){
            throw new CustomException([
                'code' => 'APP_ID_IS_EMPTY',
                'message' => 'app_id尚未设置'
            ]);
        }
        return $this->appId;
    }
}

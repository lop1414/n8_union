<?php

namespace App\Services\Weixin\MiniProgram;

use App\Common\Tools\CustomException;
use App\Datas\WeixinMiniProgramData;
use App\Models\WeixinMiniProgramModel;

class WeixinMiniProgramAuthService extends WeixinMiniProgramService
{
    /**
     * @param $jsCode
     * @return mixed
     * @throws CustomException
     * 凭借 jscode 获取 openid
     */
    public function getOpenIdByJsCode($jsCode){
        $result = $this->sdk->getOpenIdByJsCode($this->getAppId(), $this->appSecret, $jsCode);
        return $result['openid'];
    }

    /**
     * @return mixed
     * @throws CustomException
     * 刷新token
     */
    public function refreshAccessToken(){
        $res =  $this->sdk->getAccessToken($this->getAppId(), $this->appSecret);
        $weiXinMiniProgramInfo = (new WeixinMiniProgramModel())
            ->where('app_id',$this->getAppId())
            ->first();
        $weiXinMiniProgramInfo->access_token  = $res['access_token'];
        $weiXinMiniProgramInfo->expired_at  = date('Y-m-d H:i:s',time() + $res['expires_in'] - 300);
        return (new WeixinMiniProgramData())->save($weiXinMiniProgramInfo->toArray());
    }
}

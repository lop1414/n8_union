<?php

namespace App\Services\Weixin;

use App\Common\Tools\CustomException;

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
}

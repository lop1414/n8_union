<?php

namespace App\Sdks\Weixin\MiniProgram\Traits;


trait Auth
{
    /**
     * @param $appid
     * @param $secret
     * @param $jsCode
     * @param string $grantType
     * @return mixed
     * 凭借 js_code 获取 open_id
     */
    public function getOpenIdByJsCode($appid, $secret, $jsCode, $grantType = 'authorization_code'){
        $url = $this->getUrl('sns/jscode2session');

        $param = [
            'appid' => $appid,
            'secret' => $secret,
            'js_code' => $jsCode,
            'grant_type' => $grantType,
        ];

        return $this->publicRequest($url, $param);
    }
}

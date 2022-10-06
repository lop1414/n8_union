<?php

namespace App\Sdks\Qywx\Traits;


trait AccessToken
{
    /**
     * @param $corpId
     * @param $corpSecret
     * @return mixed
     * 获取 access token
     */
    public function getAccessToken($corpId, $corpSecret){
        $url = $this->getUrl('gettoken');

        $param = [
            'corpid' => $corpId,
            'corpsecret' => $corpSecret,
        ];

        return $this->publicRequest($url, $param, 'GET');
    }
}

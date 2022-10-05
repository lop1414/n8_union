<?php

namespace App\Sdks\Qywx\Traits;


trait Msg
{
    /**
     * @param $accessToken
     * @param $token
     * @param string $cursor
     * @return mixed
     * 同步消息
     */
    public function syncMsg($accessToken, $token, $cursor = ''){
        $url = $this->getUrl('kf/sync_msg');
        $url .= '?access_token='.$accessToken;

        $param = [
            'token' => $token,
        ];

        if(!empty($cursor)){
            $param['cursor'] = $cursor;
        }

        return $this->publicRequest($url, $param,'POST');
    }
}

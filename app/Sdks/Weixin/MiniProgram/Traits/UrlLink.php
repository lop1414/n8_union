<?php

namespace App\Sdks\Weixin\MiniProgram\Traits;


trait UrlLink
{

    /**
     * 生产URL Link
     * @param $accessToken
     * @param $path
     * @param $query
     * @param int $expireInterval
     * @return bool|mixed|string
     */
    public function generateUrlLink($accessToken, $path,$query,$expireInterval = 30){
        $url = $this->getUrl('wxa/generate_urllink');
        $url .= '?access_token='.$accessToken;

        $param = [
            'path' => $path,
            'query' => $query,
            'expire_type' => 1,
            'expire_interval'   => $expireInterval
        ];

        return $this->publicRequest($url, $param,'POST');
    }
}

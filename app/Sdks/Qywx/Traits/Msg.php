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

    /**
     * @param $accessToken
     * @param $welcomeCode
     * @param $content
     * @return mixed
     * 发送文本欢迎语
     */
    public function sendTextWelcomeMsg($accessToken, $welcomeCode, $content){
        $url = $this->getUrl('kf/send_msg_on_event');
        $url .= '?access_token='.$accessToken;

        $param = [
            'code' => $welcomeCode,
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
            ],
        ];

        return $this->publicRequest($url, $param,'POST');
    }

    /**
     * @param $accessToken
     * @param $externalUserid
     * @param $openKfid
     * @param $content
     * @return mixed
     * 发送文本消息
     */
    public function sendTextMsg($accessToken, $externalUserid, $openKfid, $content){
        $url = $this->getUrl('kf/send_msg');
        $url .= '?access_token='.$accessToken;

        $param = [
            'touser' => $externalUserid,
            'open_kfid' => $openKfid,
            'msgtype' => 'text',
            'text' => [
                'content' => $content,
            ],
        ];

        return $this->publicRequest($url, $param,'POST');
    }
}

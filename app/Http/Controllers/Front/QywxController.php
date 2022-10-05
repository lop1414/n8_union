<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ExceptionTypeEnum;
use App\Common\Services\ErrorLogService;
use App\Sdks\Qywx\QywxSdk;
use Illuminate\Http\Request;
use App\Sdks\Qywx\Callback\WXBizMsgCrypt;

class QywxController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function echoStr(Request $request){
        $requestData = $request->all();

        $corpMap = [
            'wwb628aa48d41017d0' => [
                'token' => 'l0dqs7YuuEPPDmAE',
                'aes_key' => 'PkUCGGlJvL8Bx3uZoXgYyHaNhrNtpRmvVGlAPHMqTgd',
                'access_token' => 'fE8KgHVzVoSPcTSDIHRgBtunYDP4H02BpoM-0zj7XifQg_XyJI-fUG-n7nLlSkmH4eFNsXVIMYr5KbnGJSWZPNma-McwCSffUG53oF0-e7NNlu2TvTcz8P1Le0hhd7tfscAV36eA5_-kPdUek-Y9NRwNlLCdFhZkmAeXeGeTA8i8kf7oAeqyAMjZ-ky4dCUQZJE-Ls6OSjZ0O6WvdL6uPw',
            ],
        ];

        $corpId = $requestData['corp_id'] ?? '';
        $encodingAesKey = $corpMap[$corpId]['aes_key'];
        $token = $corpMap[$corpId]['token'];

        $sVerifyMsgSig = $requestData['msg_signature'];
        $sVerifyTimeStamp = $requestData['timestamp'];
        $sVerifyNonce = $requestData['nonce'];
        $sVerifyEchoStr = $requestData['echostr'] ?? '';
        $sEchoStr = "";

        if(!empty($sVerifyEchoStr)){
            $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
            $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
            if ($errCode == 0) {
                // success
            } else {
                print("ERR: " . $errCode . "\n\n");
            }

            return response($sEchoStr);
        }else{
            $input = file_get_contents("php://input");

            $xml = simplexml_load_string($input);
            $xmlData = [];
            foreach ($xml as $k => $v) {
                $xmlData[(string) $k] = (string) $v;
            }


            $errorLogService = new ErrorLogService();
            $errorLogService->create('QYWX_MSG_LOG', '企业微信消息日志', [
                'request_data' => $requestData,
                'input' => $input,
                'xml_data' => $xmlData,
            ], ExceptionTypeEnum::CUSTOM);

            $sReqMsgSig = $requestData['msg_signature'];
            $sReqTimeStamp = $requestData['timestamp'];
            $sReqNonce = $requestData['nonce'];
            $sReqData = $input;
            $sMsg = "";

            $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
            $errCode = $wxcpt->DecryptMsg($sReqMsgSig, $sReqTimeStamp, $sReqNonce, $sReqData, $sMsg);
            if ($errCode == 0) {
                // success
                $msgXml = simplexml_load_string($sMsg);
                $msgXmlData = [];
                foreach ($msgXml as $k => $v) {
                    $msgXmlData[(string) $k] = (string) $v;
                }

                $qywxSdk = new QywxSdk();
                $data = $qywxSdk->syncMsg($corpMap[$corpId]['access_token'], $msgXmlData['Token']);
                $firstMsg = $data['msg_list'][0];
                $userId = '';
                if(isset($firstMsg['external_userid'])){
                    $userId = $firstMsg['external_userid'];
                }elseif(isset($firstMsg['event']['external_userid'])){
                    $userId = $firstMsg['event']['external_userid'];
                }

                $timestamp = TIMESTAMP;
                $url = "https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzUxNjA2MjEwNg==&scene=124#wechat_redirect";
                $picUrl = "https://storage-n8-page.zengzhizongni.com/file/image/0b/0b58f2d96f8e0a215f92edef14c4021e.png";
                $title = "关注公众号";
                $description = "关注公众号继续阅读";

                $callbackXml = "<xml>
<ToUserName><![CDATA[{$userId}]]></ToUserName>
<FromUserName><![CDATA[{$msgXmlData['ToUserName']}]]></FromUserName>
<CreateTime>{$timestamp}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>1</ArticleCount>
<Articles>
<item>
<Title><![CDATA[{$title}]]></Title> 
<Description><![CDATA[{$description}]]></Description>
<PicUrl><![CDATA[{$picUrl}]]></PicUrl>
<Url><![CDATA[{$url}]]></Url>
</item>
</Articles>
</xml>";
                return response($callbackXml);
            } else {
                print("ERR: " . $errCode . "\n\n");
            }

            return $this->success();
        }
    }
}

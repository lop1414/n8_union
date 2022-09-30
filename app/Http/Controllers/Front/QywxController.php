<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ExceptionTypeEnum;
use App\Common\Services\ErrorLogService;
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
            ],
        ];

        $corpId = $requestData['corp_id'] ?? '';
        $encodingAesKey = $corpMap[$corpId]['aes_key'];
        $token = $corpMap[$corpId]['token'];

        $sVerifyMsgSig = $requestData['msg_signature'];
        $sVerifyTimeStamp = $requestData['timestamp'];
        $sVerifyNonce = $requestData['nonce'];
        $sVerifyEchoStr = $requestData['echostr'];
        $sEchoStr = "";

        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $errCode = $wxcpt->VerifyURL($sVerifyMsgSig, $sVerifyTimeStamp, $sVerifyNonce, $sVerifyEchoStr, $sEchoStr);
        if ($errCode == 0) {
            // success
        } else {
            print("ERR: " . $errCode . "\n\n");
        }

        return response($sEchoStr);
    }

    /**
     * @param Request $request
     * @return mixed
     * 消息
     */
    public function msg(Request $request){
        $requestData = $request->all();

        $input = file_get_contents("php://input");

//        $xml = simplexml_load_string($input);
//        $xmlData = [];
//        foreach ($xml as $k => $v) {
//            $xmlData[(string) $k] = (string) $v;
//        }

        $errorLogService = new ErrorLogService();
        $errorLogService->create('QYWX_MSG_LOG', '企业微信消息日志', [
            'request_data' => $requestData,
            'input' => $input,
            //'xml_data' => $xmlData,
        ], ExceptionTypeEnum::CUSTOM);

        return $this->success();
    }
}

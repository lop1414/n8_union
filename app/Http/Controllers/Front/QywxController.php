<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ExceptionTypeEnum;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Models\QywxCorpModel;
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

        $corpId = $requestData['corp_id'] ?? '';

        $qywxCorpModel = new QywxCorpModel();
        $qywxCorp = $qywxCorpModel->where('id', $corpId)->first();
        if(empty($qywxCorp)){
            throw new CustomException([
                'code' => 'NOT_FOUND_CORP',
                'message' => '找不到该主体',
                'data' => [
                    'corp_id' => $corpId,
                ],
            ]);
        }

        $encodingAesKey = $qywxCorp->aes_key;
        $token = $qywxCorp->token;

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
                $data = $qywxSdk->syncMsg($qywxCorp->access_token, $msgXmlData['Token']);

                $welcomeMsg = [];
                foreach($data['msg_list'] as $msg){
                    if($msg['msgtype'] == 'event' && !empty($msg['event']['welcome_code'])){
                        $msg['send_time_format'] = date('Y-m-d H:i:s', $msg['send_time']);
                        $welcomeMsg[] = $msg;
                    }
                }

                $lastWelcomeMsg = end($welcomeMsg);
                $welcomeCode = $lastWelcomeMsg['event']['welcome_code'] ?? '';
                if(!empty($welcomeCode) && (TIMESTAMP - $lastWelcomeMsg['send_time'] < 20)){
                    $qywxSdk->sendTextWelcomeMsg($qywxCorp->access_token, $welcomeCode, '来了老铁');
                }

                return $this->success();
            } else {
                print("ERR: " . $errCode . "\n\n");
            }

        }
    }
}

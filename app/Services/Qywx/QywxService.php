<?php

namespace App\Services\Qywx;

use App\Common\Services\BaseService;
use App\Models\Qywx\QywxCorpModel;
use App\Sdks\Qywx\QywxSdk;

class QywxService extends BaseService
{
    /**
     * @var
     */
    protected $sdk;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->sdk = new QywxSdk();
    }

    /**
     * @return bool
     * 刷新 access token
     */
    public function refreshAccessToken(){
        $datetime = date('Y-m-d H:i:s', strtotime('+ 15 minutes'));

        $qywxCorpModel = new QywxCorpModel();
        $qywxCorps = $qywxCorpModel->where('expired_at', '<=', $datetime)->get();

        foreach($qywxCorps as $qywxCorp){
            $data = $this->sdk->getAccessToken($qywxCorp->id, $qywxCorp->secret);
            $qywxCorp->access_token = $data['access_token'];
            $qywxCorp->expired_at = date('Y-m-d H:i:s', TIMESTAMP + $data['expires_in'] - 200);
            $qywxCorp->save();
        }

        return true;
    }
}

<?php

namespace App\Services\Check;

use App\Common\Enums\DepartmentEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Services\SystemApi\NoticeApiService;
use App\Common\Tools\CustomLock;


class CheckService extends BaseService
{

    private $sendMode;

    public $sendTitle;

    public $sendAdminIds;

    public $sendContent;

    /**
     * @var string
     * 锁前缀
     */
    private $lockPrefix = 'check';

    /**
     * @var float|int
     * 锁过期时长 单位：秒
     */
    public $lockExpire = 60 * 60;

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();
        $this->setSendMode('feishu');
    }


    public function setSendMode($sendMode){
        $this->sendMode = $sendMode;
    }


    public function sendMessage(){
        $sendMode = $this->sendMode;
        $this->$sendMode();
    }


    public function setMarketAllAdmin(){
        $list = (new CenterApiService())->apiGetAdminUsers([
            'department' =>  DepartmentEnum::MARKET,
            'status'     =>  StatusEnum::ENABLE
        ]);
        $this->sendAdminIds = array_column($list,'id');
    }



    protected function feishu(){
        (new NoticeApiService())->apiSendFeishuMessage($this->sendTitle, $this->sendContent, $this->sendAdminIds);
    }


    /**
     * @param $key
     * @return bool
     * @throws \App\Common\Tools\CustomException
     * 是否需要发送
     */
    public function isNeedSend($key){

        $isLock = (new CustomLock($this->getLockKey($key)))->isLock();
        return !$isLock;
    }


    /**
     * @param $key
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 记录发送
     */
    public function recordSendLog($key){
        return (new CustomLock($this->getLockKey($key)))->set($this->lockExpire);
    }


    protected function getLockKey($key){
        return $this->lockPrefix.':'.$key;
    }

}

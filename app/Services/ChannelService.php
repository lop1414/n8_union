<?php

namespace App\Services;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\N8UnionUserData;
use App\Datas\UserLoginActionData;
use App\Datas\UserReadActionData;
use App\Datas\UserShortcutActionData;

class ChannelService extends BaseService
{


    /**
     * @var
     * 渠道ID
     */
    protected $channelId;


    /**
     * @var
     * 用户信息
     */
    protected $user;


    /**
     * @var int
     * 渠道保护期
     */
    protected $protectTime;


    public function __construct(){
        parent::__construct();
        $this->setProtectTime();
    }



    public function setProtectTime(){
        $day = env('PROTECT_DATE');
        $this->protectTime =  60*60*24*$day;
    }



    public function setChannelId($id){
        $this->channelId = $id;
        return $this;
    }



    public function getChannelId(){
        return $this->channelId;
    }



    public function setUser($info){
        $this->user = $info;
    }



    public function getUser(){
        return $this->user;
    }



    /**
     * @param string $dateTime 截止时间
     * @return bool
     * @throws CustomException
     * 渠道有效变更
     */
    public function isValidChange($dateTime){

        if (empty($this->user)){
            throw new CustomException([
                'code' => 'NOT_SET_USER',
                'message' => '未设置用户信息',
            ]);
        }

        // 变更渠 且 保护期内不活跃
        if($this->user['channel_id'] != $this->channelId && !$this->isActiveUser($dateTime)){

            return true;

        }else{

            return false;
        }
    }



    /**
     * @param $actionTime
     * @return bool
     * @throws CustomException
     * 是否为保护期内活跃用户
     */
    public function isActiveUser($actionTime){

        $n8Guid = $this->user['n8_guid'];
        $actionTimestamp = strtotime($actionTime);
        $tmpTimestamp = $actionTimestamp  - $this->protectTime;

        $timeRange = [
            'startTime' => date('Y-m-d H:i:s',$tmpTimestamp),
            'endTime'   => $actionTime
        ];
        $dateRange = [
            date('Y-m-d',$tmpTimestamp),
            date('Y-m-d',$actionTimestamp),
        ];
            //激活数据
        $unionUserInfo = (new N8UnionUserData())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('created_time',$timeRange)
            ->first();
        if(!empty($unionUserInfo)) return true;


        //阅读活跃
        $readInfo = (new UserReadActionData())->readLastDataByRange($n8Guid,$dateRange);
        if(!empty($readInfo)) return true;

        //登陆活跃
        $loginInfo = (new UserLoginActionData())->readLastDataByRange($n8Guid,$dateRange);
        if(!empty($loginInfo)) return true;

        //加桌活跃
        $addShortcutInfo = (new UserShortcutActionData())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('action_time',$timeRange)
            ->first();

        if(!empty($addShortcutInfo)) return true;

        return false;
    }
}

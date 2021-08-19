<?php

namespace App\Services;

use App\Common\Enums\MatcherEnum;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Datas\N8UnionUserData;
use App\Datas\ProductData;
use App\Datas\UserLoginActionData;
use App\Datas\UserReadActionData;
use App\Datas\UserShortcutActionData;
use App\Enums\QueueEnums;
use App\Models\N8UnionUserExtendModel;

class N8UnionUserService extends BaseService
{

    public $unionUserModelData;

    public function __construct(){
        parent::__construct();
        $this->unionUserModelData = new N8UnionUserData();
    }


    public function read($n8Guid,$channelId){
        return  $this->unionUserModelData
            ->setParams(['n8_guid' => $n8Guid, 'channel_id' => $channelId])
            ->read();
    }


    public function updateSave($user,$actionData){
        $unionUser = $this->read($user['n8_guid'],$actionData['channel_id']);
        // 更新
        if(!empty($unionUser)){
            $this->update($unionUser,$actionData);
            return $this->read($user['n8_guid'],$actionData['channel_id']);
        }

        // 空渠道union user
        $noChannelUnionUser = $this->read($user['n8_guid'],0);
        if(!empty($noChannelUnionUser)){
            if(!empty($actionData['channel_id']) && $noChannelUnionUser['created_time'] >= $actionData['action_time']){
                $changeData = ['channel_id' => $actionData['channel_id']];

                // 关注行为不更新注册时间
                if($actionData['action_type'] != QueueEnums::USER_FOLLOW_ACTION){
                    $changeData['created_time'] = $actionData['action_time'];
                }

                $channel = (new ChannelData())->setParams(['id' => $actionData['channel_id']])->read();
                $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $actionData['channel_id']])->read();
                $changeData['book_id'] = $channel['book_id'];
                $changeData['chapter_id'] = $channel['chapter_id'];
                $changeData['force_chapter_id'] = $channel['force_chapter_id'];
                $changeData['admin_id'] = $channelExtend['admin_id'];
                $changeData['adv_alias'] = $channelExtend['adv_alias'];
                $this->unionUserModelData->update(['id' => $noChannelUnionUser['id']],$changeData);
                return $this->read($user['n8_guid'],$actionData['channel_id']);
            }
        }


        // 有效渠道
        $product = (new ProductData())->setParams(['id' => $user['product_id']])->read();

        //没有渠道且用户渠道不为空就不重复创建  OR 系统归因才进行验证
        if(
            (empty($actionData['channel_id']) && !empty($user['channel_id']))
            OR
            (
                $product['matcher'] == MatcherEnum::SYS
                && !$this->isValidChange($user,$actionData['channel_id'],$actionData['action_time'])
            )
        ){

            // 无需变更渠道
            $actionData['channel_id'] = $user['channel_id'];
        }


        $actionData['matcher'] = $product['matcher'];
        $this->create($user,$actionData);
    }





    public function update($unionUser,$actionData){
        $uuid = $unionUser['id'];
        $updateData = [];
        //  兼容行为上报顺序问题
        if($unionUser['created_time'] >= $actionData['action_time']){

            $updateData['created_time'] = $actionData['action_time'];

            // 可修改字段
            $allowChangeField = ['request_id','ip','ua'];
            foreach ($allowChangeField as $field){
                if(!empty($actionData[$field])){
                    $updateData[$field] = $actionData[$field];
                }
            }
        }

        if(!empty($updateData)){
            $this->unionUserModelData->update(['id' => $uuid],$updateData);

            if(!empty($actionData['request_id']) || !empty($actionData['ip']) || !empty($actionData['ua'])){
                (new N8UnionUserExtendModel())->where('uuid',$uuid)->update($updateData);
            }
        }

    }



    public function create($user,$actionData){

        // 更改用户渠道ID
        (new UserService())->setUser($user)->update([
            'channel_id' => $actionData['channel_id'],
            'action_time' => $actionData['action_time']
        ]);

        return (new N8UnionUserData())->create($actionData);
    }





    /**
     * @param $user
     * @param $channelId
     * @param $endTime
     * @return bool
     * @throws CustomException
     * 渠道有效变更
     */
    public function isValidChange($user,$channelId,$endTime){

        //自然渠道不受保护
        if(empty($user['channel_id']) && $user['channel_id'] != $channelId){
            return true;
        }

        // 变更渠 且 保护期内不活跃
        if($user['channel_id'] != $channelId && !$this->isActiveUser($user['n8_guid'],$endTime)){

            return true;
        }

        return false;
    }



    /**
     * @param $n8Guid
     * @param $endTime
     * @param int $day
     * @return bool
     * @throws CustomException
     * 是保护期内活跃用户
     */
    public function isActiveUser($n8Guid,$endTime,$day = 0){
        $day = $day ?: env('PROTECT_DATE');
        $protectTime =  60*60*24*$day;
        $endTimestamp = strtotime($endTime);
        $startTimestamp = $endTimestamp  - $protectTime;
        $tmpTime = date('Y-m-d H:i:s',$startTimestamp);


        $timeRange = ['startTime'=>$tmpTime, 'endTime'=>$endTime];
        //激活数据
        $unionUserInfo = (new N8UnionUserData())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('created_time',$timeRange)
            ->first();
        if(!empty($unionUserInfo)) return true;

        //加桌活跃
        $addShortcutInfo = (new UserShortcutActionData())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('action_time',$timeRange)
            ->first();

        if(!empty($addShortcutInfo)) return true;


        $dateRange = [date('Y-m-d',$startTimestamp), date('Y-m-d',$endTimestamp)];

        //阅读活跃
        $readInfo = (new UserReadActionData())->readLastDataByRange($n8Guid,$dateRange);
        if(!empty($readInfo)) return true;

        //登陆活跃
        $loginInfo = (new UserLoginActionData())->readLastDataByRange($n8Guid,$dateRange);
        if(!empty($loginInfo)) return true;


        return false;
    }








    /**
     * @param $data
     * @return array
     * 过滤设备信息
     */
    public function filterDeviceInfo($data){
        return array(
            'ip'                    => $data['ip'] ?? '',
            'ua'                    => $data['ua'] ?? '',
            'muid'                  => $data['muid'] ?? '',
            'oaid'                  => $data['oaid'] ?? '',
            'device_brand'          => $data['device_brand'] ?? '',
            'device_manufacturer'   => $data['device_manufacturer'] ?? '',
            'device_model'          => $data['device_model'] ?? '',
            'device_product'        => $data['device_product'] ?? '',
            'device_os_version_name'=> $data['device_os_version_name'] ?? '',
            'device_os_version_code'=> $data['device_os_version_code'] ?? '',
            'device_platform_version_name' => $data['device_platform_version_name'] ?? '',
            'device_platform_version_code' => $data['device_platform_version_code'] ?? '',
            'android_id'            => $data['android_id'] ?? '',
            'request_id'            => $data['request_id'] ?? ''
        );
    }




}

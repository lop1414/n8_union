<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\MatcherEnum;
use App\Common\Enums\PlatformEnum;
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
use App\Models\N8UnionUserModel;
use App\Models\UserExtendModel;
use Jenssegers\Agent\Agent;

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
        if(empty($user)){
            throw new CustomException([
                'code'    => 'NOT_USER',
                'message' => '没有用户',
                'log'     => false,
                'data'    => ['n8_guid' => $actionData['n8_guid']]
            ]);
        }


        $product = (new ProductData())->setParams(['id' => $user['product_id']])->read();
        // 无效渠道变更
        if($product['matcher'] == MatcherEnum::SYS && !$this->isValidChange($user,$actionData['channel_id'],$actionData['action_time'])){
            $actionData['channel_id'] = $user['channel_id'];
        }

        $unionUser = $this->read($user['n8_guid'],$actionData['channel_id']);

        //有渠道的更新
        if(!empty($unionUser)){
            // 兼容行为上报顺序问题
            if($unionUser['created_time'] >= $actionData['action_time']){
                $this->update($unionUser['id'],$actionData);
            }
            return $this->read($user['n8_guid'],$actionData['channel_id']);
        }

        //空渠道的更新
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


        // 创建
        $actionData['matcher'] = $product['matcher'];
        return $this->create($actionData);
    }





    public function update($uuid,$actionData){
        $updateData = [];
        // 可修改字段
        $allowChangeField = ['created_time','request_id','ip','ua'];
        foreach ($allowChangeField as $field){
            if(!empty($actionData[$field])){
                $updateData[$field] = $actionData[$field];
            }
        }

        if(!empty($updateData)){
            $this->unionUserModelData->update(['id' => $uuid],$updateData);
            (new N8UnionUserExtendModel())->where('uuid',$uuid)->update($updateData);
        }

    }



    public function create($data){
        //默认值
        $channel = [
            'book_id'    => 0,
            'chapter_id' => 0,
            'force_chapter_id' => 0,
        ];
        $channelExtend = [
            'admin_id'  => 0,
            'adv_alias' => AdvAliasEnum::UNKNOWN
        ];

        if(!empty($data['channel_id'])){
            $channelTmp = (new ChannelData())->setParams(['id' => $data['channel_id']])->read();
            if(!empty($channelTmp)){
                $channel = $channelTmp;
            }

            $channelExtendTmp = (new ChannelExtendData())->setParams(['channel_id' => $data['channel_id']])->read();
            if(!empty($channelExtendTmp)){
                $channelExtend = $channelExtendTmp;
            }
        }


        $ua = $data['ua'] ?: $this->getUserUa($data['n8_guid']);
        $platform = PlatformEnum::UNKNOWN;
        if(!empty($ua)){
            $agent = new Agent();
            $agent->setUserAgent($ua);
            $platform = $agent->isiOS() ? PlatformEnum::IOS : PlatformEnum::ANDROID;
        }

        $ret = (new N8UnionUserModel())->create([
            'n8_guid'       => $data['n8_guid'],
            'product_id'    => $data['product_id'],
            'channel_id'    => $data['channel_id'],
            'created_time'  => $data['action_time'],
            'book_id'       => $channel['book_id'],
            'chapter_id'    => $channel['chapter_id'],
            'force_chapter_id' => $channel['force_chapter_id'],
            'platform'      => $platform,
            'admin_id'      => $channelExtend['admin_id'],
            'adv_alias'     => $channelExtend['adv_alias'],
            'matcher'       => $data['matcher'],
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        (new N8UnionUserExtendModel())->create([
            'uuid'                  => $ret->id,
            'ip'                    => $data['ip'],
            'ua'                    => $data['ua'],
            'muid'                  => $data['muid'],
            'oaid'                  => $data['oaid'],
            'device_brand'          => $data['device_brand'],
            'device_manufacturer'   => $data['device_manufacturer'],
            'device_model'          => $data['device_model'],
            'device_product'        => $data['device_product'],
            'device_os_version_name'=> $data['device_os_version_name'],
            'device_os_version_code'=> $data['device_os_version_code'],
            'device_platform_version_name' => $data['device_platform_version_name'],
            'device_platform_version_code' => $data['device_platform_version_code'],
            'android_id'            => $data['android_id'],
            'request_id'            => $data['request_id']
        ]);

        $ret->extend;

        return $ret;
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

        if($user['channel_id'] == $channelId){
            return false;
        }

        if(!empty($user['channel_id']) && empty($channelId)){
            return false;
        }


        //自然渠道不受保护
        if(empty($user['channel_id']) && !empty($channelId)){
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
     * 用户是保护期内活跃
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


//        $dateRange = [date('Y-m-d',$startTimestamp), date('Y-m-d',$endTimestamp)];

        //阅读活跃
//        $readInfo = (new UserReadActionData())->readLastDataByRange($n8Guid,$dateRange);
//        if(!empty($readInfo)) return true;

        //登陆活跃
//        $loginInfo = (new UserLoginActionData())->readLastDataByRange($n8Guid,$dateRange);
//        if(!empty($loginInfo)) return true;


        return false;
    }



    /**
     * @param $data
     * @return array
     * 过滤设备信息
     */
    public function filterDeviceInfo($data){
        return [
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
        ];
    }


    public function getUserUa($n8Guid){
        $info = (new UserExtendModel())->where('n8_guid',$n8Guid)->first();
        return $info['ua'];
    }

}

<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\MatcherEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Datas\N8UnionUserData;
use App\Datas\UserLoginActionData;
use App\Datas\UserReadActionData;
use App\Datas\UserShortcutActionData;
use App\Enums\N8UserTypeEnum;
use App\Enums\QueueEnums;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use App\Models\UserExtendModel;
use Illuminate\Support\Facades\DB;
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
        $product = ProductService::read($user['product_id']);
        // 渠道变更无效
        if($product['matcher'] == MatcherEnum::SYS && !$this->isValidChange($user,$actionData['channel_id'],$actionData['action_time'])){
            $actionData['channel_id'] = $user['channel_id'];
            $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $actionData['channel_id']])->read();
            $actionData['adv_alias'] = $channelExtend['adv_alias'];
        }

        $unionUser = $this->read($user['n8_guid'],$actionData['channel_id']);



        //有渠道的更新
        if(!empty($unionUser)){
            $tmpTime = $actionData['action_time'];

            // 兼容关注行为比注册行为先上报问题
            if($actionData['action_type'] == QueueEnums::USER_REG_ACTION){
                $tmpTime = date('Y-m-d H:i:s',strtotime($actionData['action_time']) - 60*30);
            }

            if($unionUser['created_time'] >= $tmpTime){
                $actionData['adv_alias'] = $unionUser['adv_alias'];
                $this->update($unionUser['id'],$actionData);
            }
            return $this->read($user['n8_guid'],$actionData['channel_id']);
        }

        //空渠道的更新
        $noChannelUnionUser = $this->read($user['n8_guid'],0);
        if(!empty($noChannelUnionUser)){
            //24小时内的行为可覆盖修改union user渠道
            $tmpTime = date('Y-m-d H:i:s',strtotime($actionData['action_time']) - 60*60*24);
            if(!empty($actionData['channel_id']) && $noChannelUnionUser['created_time'] >= $tmpTime){
                $changeData = ['channel_id' => $actionData['channel_id']];
                $channel = (new ChannelData())->setParams(['id' => $actionData['channel_id']])->read();
                $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $actionData['channel_id']])->read();
                $changeData['book_id'] = $channel['book_id'];
                $changeData['chapter_id'] = $channel['chapter_id'];
                $changeData['force_chapter_id'] = $channel['force_chapter_id'];
                $changeData['admin_id'] = $channelExtend['admin_id'];
                $changeData['adv_alias'] = $channelExtend['adv_alias'];

                // 注册、加桌行为可更新ip ua
                if(in_array($actionData['action_type'],[QueueEnums::USER_REG_ACTION,QueueEnums::USER_ADD_SHORTCUT_ACTION])){
                    if(!empty($actionData['ip'])){
                        $changeData['ip'] = $actionData['ip'];
                    }

                    if(!empty($actionData['ua'])){
                        $changeData['ua'] = $actionData['ua'];
                        $changeData['platform'] = $this->getPlatformByUa($actionData['ua']);
                    }
                }

                // 关注行为不更新注册时间
                if($actionData['action_type'] != QueueEnums::USER_FOLLOW_ACTION){
                    $changeData['created_time'] = $actionData['action_time'];
                }

                $this->update($noChannelUnionUser['id'],$changeData);
                return $this->read($user['n8_guid'],$actionData['channel_id']);
            }
        }


        // 创建
        $actionData['matcher'] = $product['matcher'];
        $actionData['cp_type'] = $product['cp_type'];
        $actionData['product_type'] = $product['type'];
        return $this->create($actionData);
    }



    public function update($uuid,$actionData){
        $updateData = [];

        if(!empty($actionData['ua'])){
            $actionData['platform'] = $this->getPlatformByUa($actionData['ua']);
        }

        // 可修改字段
        $unionUserAllowChangeField = [
            'channel_id','book_id','chapter_id','force_chapter_id','admin_id','adv_alias','platform'
        ];
        foreach ($unionUserAllowChangeField as $field){
            if(!empty($actionData[$field])){
                $updateData[$field] = $actionData[$field];
            }
        }


        // 可修改扩展信息字段
        $unionUserExtendAllowChangeField = ['request_id','ip','ua'];
        $extendUpdateData = [];
        foreach ($unionUserExtendAllowChangeField as $field){
            if(!empty($actionData[$field])){
                $extendUpdateData[$field] = $actionData[$field];
                if($field == 'ua'){
                    $uaReadInfo = (new UaReadService())->setUa($actionData[$field])->getInfo();
                    $updateData['sys_version'] = $uaReadInfo['sys_version'] ?? '';
                    $updateData['device_model'] = $uaReadInfo['device_model'] ?? '';
                }
            }
        }



        if(!empty($updateData)){
            $this->unionUserModelData->update(['id' => $uuid],$updateData);
        }

        if(!empty($extendUpdateData)){
            (new N8UnionUserExtendModel())->where('uuid',$uuid)->update($extendUpdateData);
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
            $platform = $this->getPlatformByUa($ua);
        }

        $n8UserSum =  (new N8UnionUserModel())->where('n8_guid',$data['n8_guid'])->count();
        $userType = $n8UserSum > 0 ? N8UserTypeEnum::BACKFLOW : N8UserTypeEnum::NEW;

        $uaReadInfo = [];
        if(!empty($data['ua'])){
            $uaReadInfo = (new UaReadService())->setUa($data['ua'])->getInfo();
        }

        $unionUser = (new N8UnionUserModel())->create([
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
            'user_type'     => $userType,
            'sys_version'   => $uaReadInfo['sys_version'] ?? '',
            'device_model'  => $uaReadInfo['device_model'] ?? '',
            'created_at'    => date('Y-m-d H:i:s')
        ]);


        // 阅文快应用实际接受广点通click_id,但是统一用了request_id返回，这里对调下
        if($channelExtend['adv_alias'] == AdvAliasEnum::GDT && $data['cp_type'] == CpTypeEnums::YW && $data['product_type'] == ProductTypeEnums::KYY){
            $requestId = $data['request_id'];
            $data['request_id'] = $data['adv_click_id'];
            $data['adv_click_id'] = $requestId;
        }

        (new N8UnionUserExtendModel())->create([
            'uuid'                  => $unionUser['id'],
            'ip'                    => $data['ip'],
            'ua'                    => $data['ua'],
            'muid'                  => $data['muid'],
            'oaid'                  => $data['oaid'],
            'adv_click_id'          => $data['adv_click_id'],
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

        $unionUser->extend;

        return $unionUser;
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


        $dateRange = [date('Y-m-d',$startTimestamp), date('Y-m-d',$endTimestamp)];

//        阅读活跃
        $readInfo = (new UserReadActionData())->readLastDataByRange($n8Guid,$dateRange);
        if(!empty($readInfo)) return true;

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
            'adv_click_id'          => $data['adv_click_id'] ?? '',
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



    public function getPlatformByUa($ua){
        $agent = new Agent();
        $agent->setUserAgent($ua);
        return $agent->isiOS() ? PlatformEnum::IOS : PlatformEnum::ANDROID;
    }

}

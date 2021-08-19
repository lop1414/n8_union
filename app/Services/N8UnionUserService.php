<?php

namespace App\Services;

use App\Common\Enums\MatcherEnum;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Datas\N8UnionUserData;
use App\Datas\ProductData;
use App\Enums\QueueEnums;

class N8UnionUserService extends BaseService
{

    public function __construct(){
        parent::__construct();
    }



    public function getChannel($productId,$cpChannelId){
        $channel = (new ChannelService())->getChannelByCpChannelId($productId,$cpChannelId);
        if(empty($channel)){
            throw new CustomException([
                'code'       => 'NO_CHANNEL',
                'message'    => "找不到渠道（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }

        if(empty($channel['channel_extend'])){

            throw new CustomException([
                'code'       => 'NO_CHANNEL_EXTEND',
                'message'    => "渠道待认领（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }
        return $channel;
    }




    public function get($actionType,$actionData){

    }



    public function create($actionData,$actionType = ''){
        try{
            if (empty($this->user)){
                throw new CustomException([
                    'code' => 'NOT_SET_USER',
                    'message' => '未设置用户信息',
                ]);
            }

            $user = $this->user;

            $channelService = new ChannelService();
            $channelService->setChannelId($this->channelId);
            $channelService->setUser($user);

            $actionData['channel_id'] = $this->channelId;

            $product = (new ProductData())->setParams(['id' => $user['product_id']])->read();



            //没有渠道且用户渠道不为空就不重复创建  OR 开启验证且系统归因才进行验证
            if(
                (empty($actionData['channel_id']) && !empty($user['channel_id']))
                OR
                ($this->verify && $product['matcher'] == MatcherEnum::SYS && !$channelService->isValidChange($actionData['action_time']))
            ){
                $this->validChannelId = $user['channel_id'];

                $tmp = (new N8UnionUserData())
                    ->setParams(['n8_guid' => $user['n8_guid'], 'channel_id' => $this->validChannelId])
                    ->read();
                if(!empty($tmp)) return $tmp;
            }

            $actionData['n8_guid'] = $user['n8_guid'];
            $actionData['product_id'] = $user['product_id'];
            $actionData['matcher'] = $product['matcher'];

            // 设备信息过滤
            $actionData = array_merge($actionData,$this->filterDeviceInfo($actionData));

            $this->validChannelId = $actionData['channel_id'];

            // 更改用户渠道ID
            (new UserService())->setUser($user)->update([
                'channel_id' => $actionData['channel_id'],
                'action_time' => $actionData['action_time']
            ]);

            $union = (new N8UnionUserData())
                ->setParams(['n8_guid' => $user['n8_guid'], 'channel_id' => 0])
                ->read();

            if(!empty($union) && !empty($actionData['channel_id']) && $union['created_time'] >= $actionData['action_time']){

                $changeData = ['channel_id' => $actionData['channel_id']];

                // 关注行为不更新注册时间
                if($actionType != QueueEnums::USER_FOLLOW_ACTION){
                    $changeData['created_time'] = $actionData['action_time'];
                }

                 $this->change($union['id'],$changeData);
                 $union['channel_id'] = $actionData['channel_id'];
                 return $union;
            }else{
                // 创建union user
                return (new N8UnionUserData())->create($actionData);

            }

        }catch (CustomException $e){
            // 联运用户已存在
            if($e->getCode() == 'UUID_EXIST'){
                $info =  (new N8UnionUserData())
                    ->setParams(['n8_guid' => $user['n8_guid'], 'channel_id' => $this->validChannelId])
                    ->read();
                if($info['created_time'] > $actionData['action_time']){
                    (new N8UnionUserData())->update([
                        'id'    => $info['id']
                    ],[
                        'created_time' => $actionData['action_time']
                    ]);
                    $info['created_time'] = $actionData['action_time'];
                }
                return $info;
            }else{
                throw $e;
            }
        }
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

    /**
     * @param $uuid
     * @param $changeData
     * @throws CustomException
     * 更改信息
     */
    public function change($uuid,$changeData){
        if(isset($changeData['channel_id'])){
            $channelId = $changeData['channel_id'];
            $channel = (new ChannelData())->setParams(['id' => $channelId])->read();
            $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $channelId])->read();
            $changeData = array_merge([
                'book_id'    => $channel['book_id'],
                'chapter_id' => $channel['chapter_id'],
                'force_chapter_id' => $channel['force_chapter_id'],
                'admin_id'    => $channelExtend['admin_id'],
                'adv_alias' => $channelExtend['adv_alias'],
            ],$changeData);
        }


        // 更新union user
        return (new N8UnionUserData())->update(
            ['id'=>$uuid],
            $changeData
        );
    }


}

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

class UnionUserService extends BaseService
{

    /**
     * @var
     * 渠道ID
     */
    protected $channelId = 0;


    /**
     * @var
     * 有效渠道ID
     */
    protected $validChannelId;


    protected $user;


    /**
     * @var
     * 验证是否活期
     */
    protected $verify;



    public function __construct(){
        parent::__construct();

        // 默认开启验证
        $this->openVerify();
    }


    /**
     * 开启验证
     */
    public function openVerify(){
        $this->verify = true;
    }


    /**
     * 关闭验证
     */
    public function closeVerify(){
        $this->verify = false;
    }



    public function setUser($info){
        $this->user = $info;
    }


    public function getUser(){
        return $this->user;
    }


    public function setChannelId($id){
        $this->channelId = $id;
    }



    /**
     * @param $productId
     * @param $cpChannelId
     * @throws CustomException
     * 通过cp渠道ID设置 联运渠道ID
     */
    public function setChannelIdByCpChannelId($productId,$cpChannelId){

        if(empty($cpChannelId)){
            return;
        }

        $channel = (new ChannelData())
            ->setParams([
                'product_id'    => $productId,
                'cp_channel_id' => $cpChannelId
            ])
            ->read();

        if(empty($channel)){

            throw new CustomException([
                'code'       => 'NO_CHANNEL',
                'message'    => "找不到渠道（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }
        $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $channel['id']])->read();
        if(empty($channelExtend)){

            throw new CustomException([
                'code'       => 'NO_CHANNEL_EXTEND',
                'message'    => "渠道待认领（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }
        $this->setChannelId($channel['id']);
    }



    public function getChannelId(){
        return $this->channelId;
    }



    public function getValidChannelId(){
        return $this->validChannelId;
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



            //没有渠道且用户渠道不为空  OR 开启验证且系统归因才进行验证
            if(
                (empty($actionData['channel_id']) && !empty($user['channel_id']))
                OR
                ($this->verify && $product['matcher'] == MatcherEnum::SYS && !$channelService->isValidChange($actionData['action_time']))
            ){
                $this->validChannelId = $user['channel_id'];

                return (new N8UnionUserData())
                    ->setParams(['n8_guid' => $user['n8_guid'], 'channel_id' => $this->validChannelId])
                    ->read();
            }

            $actionData['n8_guid'] = $user['n8_guid'];
            $actionData['product_id'] = $user['product_id'];
            $actionData['matcher'] = $product['matcher'];

            // 设备信息过滤
            $actionData = array_merge($actionData,$this->filterDeviceInfo($actionData));

            $this->validChannelId = $actionData['channel_id'];

            // 更改用户渠道ID
            (new UpdateUserService())->setUser($user)->update([
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

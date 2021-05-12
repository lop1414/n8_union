<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\N8UnionUserData;
use App\Datas\ProductData;
use App\Services\UserActionDataToDb\RegActionDataToDbService;

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
        $this->setChannelId($channel['id']);
    }



    public function getChannelId(){
        return $this->channelId;
    }



    public function getValidChannelId(){
        return $this->validChannelId;
    }



    public function create($actionData){
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

            //无效变更渠道ID
            if( $this->verify && !$channelService->isValidChange($actionData['action_time'])){
                $this->validChannelId = $user['channel_id'];

                return (new N8UnionUserData())
                    ->setParams(['n8_guid' => $user['n8_guid'], 'channel_id' => $this->validChannelId])
                    ->read();
            }

            $actionData['n8_guid'] = $user['n8_guid'];
            $actionData['product_id'] = $user['product_id'];
            $actionData['channel_id'] = $this->channelId;

            $product = (new ProductData())->setParams(['id' => $actionData['product_id']])->read();
            $actionData['matcher'] = $product['matcher'];

            // 设备信息过滤
            $actionData = array_merge($actionData,$this->filterDeviceInfo($actionData));

            $this->validChannelId = $actionData['channel_id'];

            // 更改用户渠道ID
            $userChangeData = [
                'channel_id' => $actionData['channel_id'],
                'action_time' => $actionData['action_time']
            ];
            (new RegActionDataToDbService())->changeUserItem($user,$userChangeData,false);

            // 创建union user
            return (new N8UnionUserData())->create($actionData);

        }catch (CustomException $e){
            // 联运用户已存在
            if($e->getCode() == 'UUID_EXIST'){
                return (new N8UnionUserData())
                    ->setParams(['n8_guid' => $user['n8_guid'], 'channel_id' => $this->validChannelId])
                    ->read();
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


}

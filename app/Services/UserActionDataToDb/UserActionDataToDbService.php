<?php

namespace App\Services\UserActionDataToDb;

use App\Common\Services\BaseService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomQueue;
use App\Datas\N8GlobalOrderData;
use App\Datas\N8GlobalUserData;
use App\Datas\N8UnionUserData;
use App\Datas\UserData;
use App\Models\OrderModel;
use App\Services\ChannelService;
use Illuminate\Support\Facades\DB;

class UserActionDataToDbService extends BaseService
{

    protected $queueEnum;


    public function __construct(){
        parent::__construct();
    }



    public function run(){

        $queue = new CustomQueue($this->queueEnum);

        $rePushData = [];
        while ($data = $queue->pull()) {

            try{
                DB::beginTransaction();


                $globalUser = [];

                if(isset($data['open_id'])){
                    $globalUser = $this->readGlobalUser($data['product_id'],$data['open_id']);
                }


                $this->item($data,$globalUser);

                DB::commit();

            }catch (CustomException $e){

                DB::rollBack();


                //日志
                (new ErrorLogService())->catch($e);

                $queue->item['exception'] = $e->getErrorInfo();
                $queue->item['code'] = $e->getCode();
                $rePushData[] = $queue->item;

                var_dump($e->getErrorInfo());
            }catch (\Exception $e){

                DB::rollBack();


                //日志
                (new ErrorLogService())->catch($e);

                //命中唯一索引
                if($e->getCode() != 23000){
                    $queue->item['exception'] = $e->getMessage();
                    $rePushData[] = $queue->item;
                }

                var_dump($e->getCode(),$e->getMessage());
            }
        }

        $queue->rePushFailed(function () use ($queue){
            if($queue->item['code'] == 'NO_CHANNEL'){
                // TODO 消息提示
                echo "消息提示\n";
            }
        });

        // 数据重回队列
        foreach ($rePushData as $item){
            $queue->setItem($item);
            $queue->rePush();
        }
    }



    public function item($data,$globalUser){}



    /**
     * @param $productId
     * @param $cpChannelId
     * @return mixed
     * @throws CustomException
     * 获取渠道ID
     */
    public function readChannelId($productId,$cpChannelId){
        return (new ChannelService())->readByCpChannelId($productId,$cpChannelId);
    }



    public function readGlobalUser($productId,$openId){
        $tmp = new N8GlobalUserData();
        $info = $tmp->setParams(['product_id' => $productId,'open_id' => $openId])->read();

        if(empty($info)){
            $info = $tmp->create($productId,$openId);
        }

        return $info;
    }



    public function readGlobalOrder($productId,$orderId){
        $tmp = new N8GlobalOrderData();
        $info = $tmp->setParams(['product_id' => $productId,'order_id' => $orderId])->read();

        if(empty($info)){
            $info = $tmp->create($productId,$orderId);
        }

        return $info;
    }



    /**
     * @param $user
     * @param $channelId
     * @param $actionData
     * @param bool $verify 验证渠道有效变更
     * @return false|null
     * @throws CustomException
     * 创建union用户 渠道ID无效变更则不创建
     */
    public function createUnionUser($user,$channelId,$actionData,$verify = true){

        try{
            $unionUser = false;

            //无效变更渠道ID
            $channelService = new ChannelService();
            if( $verify && !$channelService->isValidChange($user,$channelId,$actionData['action_time'])){
                return false;
            }

            $actionData['n8_guid'] = $user['n8_guid'];
            $actionData['channel_id'] = $channelId;
            $unionUser = (new N8UnionUserData())->create($actionData);
        }catch (CustomException $e){
            // 联运用户已存在
            if($e->getCode() == 'UUID_EXIST'){
                $unionUser = (new N8UnionUserData())
                    ->setParams([
                        'n8_guid'   => $user['n8_guid'],
                        'channel_id'=> $channelId
                    ])
                    ->read();
            }
        }

        return $unionUser;
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



    /**
     * @param $n8Guid
     * @return mixed
     * @throws CustomException
     * 用户存在
     */
    public function userIsExist($n8Guid){
        $user = $this->readUser($n8Guid);

        if(empty($user)){
            throw new CustomException([
                'code'    => 'NOT_USER',
                'message' => '找不到用户',
                'log'     => true,
                'data'    => [
                    'n8_guid' => $n8Guid
                ]
            ]);
        }
        return $user;
    }



    /**
     * @param $n8Goid
     * @throws CustomException
     * 订单不存在
     */
    public function orderIsNotExist($n8Goid){
        $order = $this->readOrder($n8Goid);

        if(!empty($order)){
            throw new CustomException([
                'code'    => 'EXIST_ORDER',
                'message' => '订单已存在',
                'log'     => true,
                'data'    => [
                    'n8_goid' => $n8Goid
                ]
            ]);
        }
    }



    /**
     * @param $n8Guid
     * @return array|null
     * @throws CustomException
     */
    public function readUser($n8Guid){

        return (new UserData())->setParams(['n8_guid'=>$n8Guid])->read();
    }



    /**
     * @param $n8Guid
     * @return array|null
     * @throws CustomException
     * 刷新用户缓存
     */
    public function refreshUserData($n8Guid){
        $tmp = (new UserData())->setParams(['n8_guid'=>$n8Guid]);

        $tmp->clear();
        return $tmp->read();
    }



    /**
     * @param $n8Goid
     * @return mixed
     */
    public function readOrder($n8Goid){
        $info = (new OrderModel())
            ->where('n8_goid',$n8Goid)
            ->first();
        if(!empty($info)) $info->toArray();
        return $info;
    }


    public function getQueueEnum(){
        return $this->queueEnum;
    }

}

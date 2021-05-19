<?php

namespace App\Services\UserActionDataToDb;

use App\Common\Services\BaseService;
use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomQueue;
use App\Datas\ChannelExtendData;
use App\Datas\N8GlobalOrderData;
use App\Datas\N8GlobalUserData;
use App\Datas\OrderData;
use App\Datas\UserData;
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

                // echo
                (new ConsoleEchoService())->error("自定义异常 {code:{$e->getCode()},msg:{$e->getMessage()}}");
            }catch (\Exception $e){

                DB::rollBack();

                //日志
                (new ErrorLogService())->catch($e);

                $queue->item['exception'] = $e->getMessage();
                $queue->item['code'] = $e->getCode();
                $rePushData[] = $queue->item;

                var_dump($e->getMessage());

                // echo
                (new ConsoleEchoService())->error("异常 {code:{$e->getCode()},msg:{$e->getMessage()}}");
            }
        }

        // 数据重回队列
        foreach ($rePushData as $item){
            $queue->setItem($item);
            $queue->rePush();
        }
    }



    public function item($data,$globalUser){}



    /**
     * @param $id
     * @return mixed
     * @throws CustomException
     * 通过渠道 获取广告商标识
     */
    public function getAdvAliasByChannel($id){
        $channel = (new ChannelExtendData())
            ->setParams(['channel_id'=>$id])
            ->read();
        return $channel['adv_alias'];
    }



    /**
     * @param $productId
     * @param $openId
     * @return mixed|null
     * @throws CustomException
     * 获取全局用户信息
     */
    public function readGlobalUser($productId,$openId){
        $tmp = new N8GlobalUserData();
        $info = $tmp->setParams(['product_id' => $productId,'open_id' => $openId])->read();

        if(empty($info)){
            $info = $tmp->create($productId,$openId);
        }

        return $info;
    }


    /**
     * @param $productId
     * @param $orderId
     * @return mixed|null
     * @throws CustomException
     * 获取全局订单信息
     */
    public function readGlobalOrder($productId,$orderId){
        $tmp = new N8GlobalOrderData();
        $info = $tmp->setParams(['product_id' => $productId,'order_id' => $orderId])->read();

        if(empty($info)){
            $info = $tmp->create($productId,$orderId);
        }

        return $info;
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
     * 获取用户信息
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
     * @return array|null
     * @throws CustomException
     * 获取订单信息
     */
    public function readOrder($n8Goid){
        return  (new OrderData())
            ->setParams(['n8_goid'=>$n8Goid])
            ->read();
    }



    /**
     * @return mixed
     * 获取队列枚举
     */
    public function getQueueEnum(){
        return $this->queueEnum;
    }

}

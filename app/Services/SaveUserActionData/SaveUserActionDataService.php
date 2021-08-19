<?php

namespace App\Services\UserActionDataToDb;

use App\Common\Services\BaseService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomQueue;
use App\Datas\ChannelExtendData;
use App\Datas\OrderData;
use App\Datas\UserData;
use App\Enums\QueueEnums;
use App\Services\GlobalOrderService;
use App\Services\GlobalUserService;
use Illuminate\Support\Facades\DB;

class SaveUserActionDataService extends BaseService
{

    protected $queueEnum;

    protected $globalUserService;


    public function __construct(){
        parent::__construct();
        $this->globalUserService = new GlobalUserService();
    }



    public function run(){

        $queue = new CustomQueue($this->queueEnum);

        $rePushData = [];
        while ($data = $queue->pull()) {

            try{
                DB::beginTransaction();


                $globalUser = [];

                if(isset($data['open_id'])){
                    $globalUser = $this->globalUserService->make($data['product_id'],$data['open_id']);
                }


                $this->item($data,$globalUser);

                DB::commit();

            }catch (CustomException $e){

                DB::rollBack();
                $this->failItem($data);
                $errInfo = $e->getErrorInfo(true);

                //不是 订单已存在异常
                if($errInfo['code'] != 'EXIST_ORDER'){
                    //日志
                    (new ErrorLogService())->catch($e);

                    $queue->item['exception'] = $e->getErrorInfo();
                    $queue->item['code'] = $e->getCode();
                    $rePushData[] = $queue->item;
                }

            }catch (\Exception $e){

                DB::rollBack();

                $this->failItem($data);
                //未命中唯一索引
                if($e->getCode() != 23000){
                    //日志
                    (new ErrorLogService())->catch($e);
                    $queue->item['exception'] = $e->getMessage();
                    $queue->item['code'] = $e->getCode();
                    $rePushData[] = $queue->item;
                    echo $e->getMessage()."\n";

                }else{
                    echo "  命中唯一索引 \n";
                }
            }
        }

        // 数据重回队列
        foreach ($rePushData as $item){
            $queue->setItem($item);
            $queue->rePush();
        }
    }



    public function item($data,$globalUser){}


    public function failItem($data){
        // 事务回滚 删除缓存
        $this->globalUserService->clearCache($data['product_id'],$data['open_id']);

        if($this->queueEnum ==  QueueEnums::USER_ORDER_ACTION){
            (new GlobalOrderService())->clearCache($data['product_id'],$data['order_id']);
        }
    }


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
                'log'     => false,
                'data'    => [
                    'n8_guid' => $n8Guid
                ]
            ]);
        }
        return $user;
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
                'log'     => false,
                'data'    => [
                    'n8_goid' => $n8Goid
                ]
            ]);
        }
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

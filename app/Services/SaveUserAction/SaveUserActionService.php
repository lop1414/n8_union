<?php

namespace App\Services\UserActionDataToDb;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Services\BaseService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomQueue;
use App\Enums\QueueEnums;
use App\Services\ChannelService;
use App\Services\GlobalOrderService;
use App\Services\GlobalUserService;
use Illuminate\Support\Facades\DB;

class SaveUserActionService extends BaseService
{

    protected $queueEnum;


    public function run(){

        $queue = new CustomQueue($this->queueEnum);
        $globalUserService = new GlobalUserService();

        $rePushData = [];
        while ($data = $queue->pull()) {

            try{
                DB::beginTransaction();

                $globalUser = $globalUserService->make($data['product_id'],$data['open_id']);
                $data['n8_guid'] = $globalUser['n8_guid'];

                $channel = [
                    'channel_id' => 0,
                    'adv_alias'  => AdvAliasEnum::UNKNOWN
                ];
                if(!empty($data['cp_channel_id'])){
                    $channel = $this->getChannel($data['product_id'],$data['cp_channel_id']);
                }
                $data['channel_id'] = $channel['id'];
                $data['adv_alias'] = $channel['channel_extend']['adv_alias'];


                $this->item($data);

                DB::commit();

            }catch (CustomException $e){

                DB::rollBack();
                $this->failItem($data);
                $errInfo = $e->getErrorInfo(true);

                //订单已存
                if($errInfo['code'] == 'EXIST_ORDER'){
                    continue;
                }

                //日志
                (new ErrorLogService())->catch($e);

                $queue->item['exception'] = $e->getErrorInfo();
                $queue->item['code'] = $e->getCode();
                $rePushData[] = $queue->item;


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

    public function item($data){}

    public function failItem($data){
        // 事务回滚 删除缓存
        (new GlobalUserService())->clearCache($data['product_id'],$data['open_id']);

        if($this->queueEnum ==  QueueEnums::USER_ORDER_ACTION){
            (new GlobalOrderService())->clearCache($data['product_id'],$data['order_id']);
        }
    }

    /**
     * @return mixed
     * 获取队列枚举
     */
    public function getQueueEnum(){
        return $this->queueEnum;
    }

}

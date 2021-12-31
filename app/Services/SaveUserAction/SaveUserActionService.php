<?php

namespace App\Services\SaveUserAction;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Services\BaseService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Common\Tools\CustomQueue;
use App\Datas\N8UnionUserData;
use App\Enums\QueueEnums;
use App\Services\ChannelService;
use App\Services\GlobalOrderService;
use App\Services\GlobalUserService;
use App\Services\N8UnionUserService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;

class SaveUserActionService extends BaseService
{

    protected $queueEnum;

    public $userService;
    public $n8UnionUserService;

    public function __construct(){
        parent::__construct();
        $this->userService = new UserService();
        $this->n8UnionUserService  = new N8UnionUserService();

    }


    public function run(){

        $queue = new CustomQueue($this->queueEnum);
        $globalUserService = new GlobalUserService();

        $rePushData = [];
        while ($data = $queue->pull()) {

            try{
                DB::beginTransaction();

                $data['action_type'] = $this->queueEnum;

                $globalUser = $globalUserService->make($data['product_id'],$data['open_id']);
                $data['n8_guid'] = $globalUser['n8_guid'];
                $data['channel_id'] = 0;
                $data['adv_alias'] = AdvAliasEnum::UNKNOWN;

                if(!empty($data['cp_channel_id'])){
                    $channel = ChannelService::readChannelByCpChannelIdAndCheck($data['product_id'],$data['cp_channel_id']);
                    $data['channel_id'] = $channel['id'];
                    $data['adv_alias'] = $channel['channel_extend']['adv_alias'];
                }

                $user = $this->userService->read($data['n8_guid']);
                $data = array_merge($data,$this->n8UnionUserService->filterDeviceInfo($data));
                $unionUser = $this->item($user,$data);

                //更新user 渠道
                if(!empty($unionUser) && !empty($user) && $unionUser['channel_id'] != $user['channel_id']){
                    $this->userService->update($data['n8_guid'],$data);
                }
                DB::commit();

            }catch (CustomException $e){

                DB::rollBack();
                // 执行异常操作
                $this->failItem($data);

                //订单已存
                $errInfo = $e->getErrorInfo(true);
                if($errInfo['code'] == 'EXIST_ORDER'){
                    continue;
                }

                //日志
                (new ErrorLogService())->catch($e);

                //重回队列
                $queue->item['exception'] = $e->getErrorInfo();
                $queue->item['code'] = $e->getCode();
                $rePushData[] = $queue->item;


            }catch (\Exception $e){

                DB::rollBack();
                // 执行异常操作
                $this->failItem($data);

                // 执行异常操作
                if($e->getCode() == 23000){
                    echo "  命中唯一索引 \n";
                    continue;
                }

                //日志
                (new ErrorLogService())->catch($e);

                //重回队列
                $queue->item['exception'] = $e->getMessage();
                $queue->item['code'] = $e->getCode();
                $rePushData[] = $queue->item;
                echo $e->getMessage()."\n";
            }
        }

        // 数据重回队列
        foreach ($rePushData as $item){
            $queue->setItem($item);
            $queue->rePush();
        }
    }

    public function item($user,$data){}




    public function failItem($data){
        //删除缓存
        (new GlobalUserService())->clearCache($data['product_id'],$data['open_id']);

        (new N8UnionUserData())->setParams(['n8_guid' => $data['n8_guid'], 'channel_id' => $data['channel_id']])->clear();

        if($this->queueEnum ==  QueueEnums::USER_ORDER_ACTION){
            (new GlobalOrderService())->clearCache($data['product_id'],$data['order_id']);
        }
    }

}

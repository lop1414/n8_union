<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Models\N8UnionUserModel;

class ChannelController extends BaseController
{



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ChannelModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){

            foreach ($this->curdService->responseData['list'] as $item){
                $item->cp_channel;
            }
        });
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){

        $this->curdService->getQueryAfter(function(){
            foreach ($this->curdService->responseData as $item){
                $item->cp_channel;
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){

            $this->curdService->responseData->cp_channel;
        });
    }




    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('adv_alias')
            ->addValidRule('required')
            ->addValidEnum(AdvAliasEnum::class);

        $this->curdService->addField('product_id')->addValidRule('required');
        $this->curdService->addField('gcid')->addValidRule('required');
        $this->curdService->addField('status')
            ->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);

        $this->curdService->saveBefore(function(){

            if($this->curdService->getModel()->exist('gcid', $this->curdService->handleData['gcid'])){
                throw new CustomException([
                    'code' => 'GCID_EXIST',
                    'message' => 'CP渠道已被绑定'
                ]);
            }

            $adminUser = Functions::getGlobalData('admin_user_info');

            $this->curdService->handleData['admin_id'] = $adminUser['admin_user']['id'];
        });


    }



    /**
     * 更新预处理
     */
    public function updatePrepare(){

        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('adv_alias')
            ->addValidRule('required')
            ->addValidEnum(AdvAliasEnum::class);

        $this->curdService->addField('gcid')->addValidRule('required');
        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);


        $this->curdService->saveBefore(function(){

            // 有注册用户 不可修改
            $tmp = (new N8UnionUserModel())
                ->where('channel_id',$this->curdService->getModel()->id)
                ->where('created_time',$this->curdService->getModel()->created_at)
                ->first();

            if(!empty($tmp)){
                throw new CustomException([
                    'code' => 'NO_EDITING',
                    'message' => '渠道已产生数据,信息不可修改'
                ]);
            }

            if(
                $this->curdService->getModel()->gcid != $this->curdService->handleData['gcid']
                && $this->curdService->getModel()->uniqueExist([
                    'product_id' => $this->curdService->getModel()->product_id,
                    'gcid' => $this->curdService->handleData['gcid']
                ])){
                throw new CustomException([
                    'code' => 'GCID_EXIST',
                    'message' => 'CP渠道已被绑定'
                ]);
            }

            unset($this->curdService->handleData['product_id']);
            unset($this->curdService->handleData['admin_id']);
        });
    }
}

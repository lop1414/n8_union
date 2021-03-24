<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Models\N8UnionUserModel;

class ChannelController extends BaseController
{


    public $adminUser;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ChannelModel();

        parent::__construct();

        $this->adminUser = Functions::getGlobalData('admin_user_info');

    }



    public function getAdminUser($filter = []){
        $adminUsers = (new CenterApiService())->apiGetAdminUsers($filter);
        return array_column($adminUsers,'name','id');
    }


    /**
     * 有数据权限
     * @return bool
     */
    public function isDataAuth(){
        if($this->adminUser['is_admin']) return true;

        return false;
    }



    /**
     * 根据权限过滤
     */
    public function dataFilter(){
        if(!$this->isDataAuth()){
            $this->curdService->addFiltering([
                [
                    'field'     => 'admin_id',
                    'operator'  => 'EQUALS',
                    'value'     => $this->adminUser['admin_user']['id']
                ]
            ]);
        }
    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        $this->dataFilter();

        $this->curdService->selectQueryAfter(function(){

            $map = $this->getAdminUser();

            foreach ($this->curdService->responseData['list'] as $item){
                $item->product;
                $item->cp_channel;
                $item->cp_channel->book;
                $item->cp_channel->chapter;
                $item->cp_channel->force_chapter;
                $item->admin_name = $map[$item->admin_id];
            }
        });
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){

        if(!$this->isDataAuth()){
            $this->curdService->getQueryBefore(function (){
                $this->curdService->customBuilder(function ($build){
                    $build->where('admin_id',$this->adminUser['admin_user']['id']);
                });
            });
        }

        $this->curdService->getQueryAfter(function(){
            $map = $this->getAdminUser();

            foreach ($this->curdService->responseData as $item){
                $item->product;
                $item->cp_channel;
                $item->cp_channel->book;
                $item->cp_channel->chapter;
                $item->cp_channel->force_chapter;
                $item->admin_name = $map[$item->admin_id];
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->product;
            $this->curdService->responseData->cp_channel;
            $this->curdService->responseData->cp_channel->book;
            $this->curdService->responseData->cp_channel->chapter;
            $this->curdService->responseData->cp_channel->force_chapter;

            $map = $this->getAdminUser([
                'id'  => $this->curdService->responseData->admin_id
            ]);

            $this->curdService->responseData->admin_name = $map[$this->curdService->responseData->admin_id];
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
        $this->curdService->addField('n8_cp_channel_id')->addValidRule('required');
        $this->curdService->addField('status')
            ->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);

        $this->curdService->saveBefore(function(){

            if($this->curdService->getModel()->exist('n8_cp_channel_id', $this->curdService->handleData['n8_cp_channel_id'])){
                throw new CustomException([
                    'code' => 'GCID_EXIST',
                    'message' => 'CP渠道已被绑定'
                ]);
            }

            $this->curdService->handleData['admin_id'] = $this->adminUser['admin_user']['id'];
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

        $this->curdService->addField('n8_cp_channel_id')->addValidRule('required');
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
                $this->curdService->getModel()->gcid != $this->curdService->handleData['n8_cp_channel_id']
                && $this->curdService->getModel()->uniqueExist([
                    'product_id' => $this->curdService->getModel()->product_id,
                    'n8_cp_channel_id' => $this->curdService->handleData['n8_cp_channel_id']
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

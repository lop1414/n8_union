<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Helpers\PlatForm;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\MultiPlatFormChannelData;
use App\Datas\ProductData;
use App\Models\MultiPlatFormChannelModel;

class MultiPlatFormChannelController extends BaseController
{

    protected $defaultOrderBy = 'updated_at';

    public $adminUser;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new MultiPlatFormChannelModel();
        $this->modelData = new MultiPlatFormChannelData();


        parent::__construct();

        $this->adminUser = Functions::getGlobalData('admin_user_info');

    }



    public function getAdminUserName($filter = []){
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
     * 过滤
     */
    public function dataFilter(){
        $this->curdService->customBuilder(function ($builder){


            if(!$this->isDataAuth()){
                $builder->where('e.admin_id',$this->adminUser['admin_user']['id']);
            }

            $req = $this->curdService->requestData;
            if(!empty($req['admin_id'])){
                $builder->where('admin_id',$req['admin_id']);
            }

            if(!empty($req['adv_alias'])){
                $builder->where('adv_alias',$req['adv_alias']);
            }

            if(!empty($req['status'])){
                $builder->where('status',$req['status']);
            }

        });

    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        $this->curdService->selectQueryBefore(function (){
            $this->dataFilter();
        });


        $this->curdService->selectQueryAfter(function(){

            $map = $this->getAdminUserName();

            foreach ($this->curdService->responseData['list'] as $item){

                $item->android_channel;
                $item->ios_channel;
                $item->admin_name = $item->admin_id ? $map[$item->admin_id] : '';
            }
        });
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
        });


        $this->curdService->getQueryAfter(function(){
            $map = $this->getAdminUserName();

            foreach ($this->curdService->responseData as $item){
                $item->android_channel;
                $item->ios_channel;
                $item->admin_name = $item->admin_id ? $map[$item->admin_id] : '';
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->android_channel;
            $this->curdService->responseData->ios_channel;

            $adminId = $this->curdService->responseData->admin_id;
            if(!empty($adminId)){
                $map = $this->getAdminUserName([
                    'id'  => $adminId
                ]);

                $this->curdService->responseData->admin_name = $map[$adminId];
            }else{
                $this->curdService->responseData->admin_name = '';
            }
        });
    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('adv_alias')
            ->addValidRule('required')
            ->addValidEnum(AdvAliasEnum::class);

        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);

        $this->curdService->saveBefore(function(){
            $this->isAndroidChannel($this->curdService->handleData['android_channel_id']);
            $this->isIOSChannel($this->curdService->handleData['ios_channel_id']);
            // 赋值 admin_id
            $adminUser = Functions::getGlobalData('admin_user_info');
            $this->curdService->handleData['admin_id'] = $adminUser['admin_user']['id'];
        });
    }


    /**
     * 更新预处理
     */
    public function updatePrepare(){

        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);

        $this->curdService->saveBefore(function(){
            $this->isAndroidChannel($this->curdService->handleData['android_channel_id']);
            $this->isIOSChannel($this->curdService->handleData['ios_channel_id']);

            unset($this->curdService->handleData['adv_alias']);
            unset($this->curdService->handleData['admin_id']);
        });
    }


    public function isAndroidChannel($channelId){
        $product = $this->getProductInfo($channelId);
        $productType = PlatForm::getAndroidProductType();
        if(!in_array($product['type'],$productType)){
            throw new CustomException([
                'code' => 'NOT_ANDROID_CHANNEL',
                'message' => "不是安卓渠道",
            ]);
        }
    }


    public function isIOSChannel($channelId){
        $product = $this->getProductInfo($channelId);
        $productType = PlatForm::getIOSProductType();
        if(!in_array($product['type'],$productType)){
            throw new CustomException([
                'code' => 'NOT_IOS_CHANNEL',
                'message' => "不是iOS渠道",
            ]);
        }
    }


    public function getProductInfo($channelId){
        $channel = (new ChannelData())
            ->setParams(['id' => $channelId])
            ->read();
        return (new ProductData())
            ->setParams(['id' => $channel['product_id']])
            ->read();
    }
}

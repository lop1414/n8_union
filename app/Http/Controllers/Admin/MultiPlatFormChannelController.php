<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Advs;
use App\Common\Helpers\Functions;
use App\Common\Helpers\Platform;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Datas\MultiPlatFormChannelData;
use App\Datas\ProductData;
use App\Models\MultiPlatFormChannelModel;

class MultiPlatFormChannelController extends BaseController
{


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
                $builder->where('admin_id',$this->adminUser['admin_user']['id']);
            }

            $req = $this->curdService->requestData;
            if(!empty($req['product_id'])){
                $sql = 'SELECT id FROM channels WHERE product_id = '.$req['product_id'];
                $builder->whereRaw("(android_channel_id IN ({$sql}) OR ios_channel_id IN ({$sql}))");
            }

            if(!empty($req['channel_name'])){
                $sql = "SELECT id FROM channels WHERE `name` LIKE '%{$req['channel_name']}%'";
                $builder->whereRaw("(android_channel_id IN ({$sql}) OR ios_channel_id IN ({$sql}))");
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
            $advFeedBack = Advs::getFeedbackUrlMap();
            $advPageFeedBack = Advs::getPageFeedbackUrlMap();

            foreach ($this->curdService->responseData['list'] as $item){
                $url = $advFeedBack[$item['adv_alias']] ?? '';
                $url = str_replace('__ANDROID_CHANNEL_ID__',$item->android_channel->id,$url);
                $item->feedback_url = str_replace('__IOS_CHANNEL_ID__',$item->ios_channel->id,$url);


                $pageUrl = $advPageFeedBack[$item['adv_alias']] ?? '';
                $pageUrl = str_replace('__N8_MULTI_CHANNEL_ID__',$item->id,$pageUrl);
                $pageUrl = str_replace('__ANDROID_CHANNEL_ID__',$item->android_channel->id,$pageUrl);
                $item->page_feedback_url = str_replace('__IOS_CHANNEL_ID__',$item->ios_channel->id,$pageUrl);

                $item->android_channel->product;
                $item->android_channel->book;
                $item->android_channel->chapter;
                $item->android_channel->force_chapter;
                $item->ios_channel->product;
                $item->ios_channel->book;
                $item->ios_channel->chapter;
                $item->ios_channel->force_chapter;
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
                $item->admin_name = $item->admin_id ? $map[$item->admin_id] : '';
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->android_channel->product;
            $this->curdService->responseData->android_channel->book;
            $this->curdService->responseData->android_channel->chapter;
            $this->curdService->responseData->android_channel->force_chapter;

            $this->curdService->responseData->ios_channel->product;
            $this->curdService->responseData->ios_channel->book;
            $this->curdService->responseData->ios_channel->chapter;
            $this->curdService->responseData->ios_channel->force_chapter;
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
            //验证
            $androidChannel = (new ChannelData())
                ->setParams(['id' => $this->curdService->handleData['android_channel_id']])
                ->read();
            $androidProduct = (new ProductData())
                ->setParams(['id' => $androidChannel['product_id']])
                ->read();
            $iosChannel = (new ChannelData())
                ->setParams(['id' => $this->curdService->handleData['ios_channel_id']])
                ->read();
            $iosProduct = (new ProductData())
                ->setParams(['id' => $iosChannel['product_id']])
                ->read();


            if(!in_array($androidProduct['type'],Platform::getAndroidProductType())){
                throw new CustomException([
                    'code' => 'NOT_ANDROID_CHANNEL',
                    'message' => "不是安卓渠道",
                ]);
            }

            if(!in_array($iosProduct['type'],Platform::getIosProductType())){
                throw new CustomException([
                    'code' => 'NOT_IOS_CHANNEL',
                    'message' => "不是iOS渠道",
                ]);
            }


            $androidChannelExtend = (new ChannelExtendData())
                ->setParams(['channel_id' => $this->curdService->handleData['android_channel_id']])
                ->read();
            $iosChannelExtend = (new ChannelExtendData())
                ->setParams(['channel_id' => $this->curdService->handleData['ios_channel_id']])
                ->read();

            if($androidChannelExtend['adv_alias'] != $iosChannelExtend['adv_alias']){
                throw new CustomException([
                    'code' => 'ADV_UNLIKE',
                    'message' => "渠道广告商不一致",
                ]);
            }



            // 赋值 admin_id
            $adminUser = Functions::getGlobalData('admin_user_info');
            $this->curdService->handleData['admin_id'] = $adminUser['admin_user']['id'];
        });
    }


//    /**
//     * 更新预处理
//     */
//    public function updatePrepare(){
//
//        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);
//
//        $this->curdService->saveBefore(function(){
//            $this->isAndroidChannel($this->curdService->handleData['android_channel_id']);
//            $this->isIOSChannel($this->curdService->handleData['ios_channel_id']);
//
//            unset($this->curdService->handleData['adv_alias']);
//            unset($this->curdService->handleData['admin_id']);
//        });
//    }


    public function isAndroidChannel($channelId){
        $product = $this->getProductInfo($channelId);
        $productType = Platform::getAndroidProductType();
        if(!in_array($product['type'],$productType)){
            throw new CustomException([
                'code' => 'NOT_ANDROID_CHANNEL',
                'message' => "不是安卓渠道",
            ]);
        }
    }


    public function isIOSChannel($channelId){
        $product = $this->getProductInfo($channelId);
        $productType = Platform::getIosProductType();
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

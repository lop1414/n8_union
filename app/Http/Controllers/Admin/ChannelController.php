<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\PlatformEnum;
use App\Common\Helpers\Advs;
use App\Common\Helpers\Functions;
use App\Common\Helpers\Platform;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ProductData;
use App\Models\ChannelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChannelController extends BaseController
{

    protected $defaultOrderBy = 'updated_time';

    public $adminUser;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ChannelModel();
        $this->modelData = new ChannelData();


        parent::__construct();

        $this->adminUser = Functions::getGlobalData('admin_user_info');

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

            $builder->leftJoin('channel_extends AS e','channels.id','=','e.channel_id')
                ->select(DB::raw('channels.*,e.adv_alias,e.status,e.admin_id'));



            $req = $this->curdService->requestData;
            if(isset($req['is_bind']) && $req['is_bind'] == 0){
                $builder->whereNull('e.admin_id');
            }else{
                $builder->where('e.admin_id','>',0);

                if(!$this->isDataAuth()){
                    $builder->where('e.admin_id',$this->adminUser['admin_user']['id']);
                }
            }

            if(!empty($req['admin_id'])){
                $builder->where('e.admin_id',$req['admin_id']);
            }

            if(!empty($req['adv_alias'])){
                $builder->where('e.adv_alias',$req['adv_alias']);
            }

            if(!empty($req['status'])){
                $builder->where('e.status',$req['status']);
            }

            if(!empty($req['os'])){
                Functions::hasEnum(PlatformEnum::class,$req['os']);

                $productIds = (new ProductData())
                    ->whereIn('type',Platform::getOSProductType($req['os']))
                    ->get('id')
                    ->toArray();

                return $builder->whereIn('product_id',array_column($productIds,'id'));
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

            $map = $this->getAdminUserMap();

            $advFeedBack = Advs::getFeedbackUrlMap();
            $advPageFeedBack = Advs::getPageFeedbackUrlMap();

            foreach ($this->curdService->responseData['list'] as $item){
                $url = $advFeedBack[$item['adv_alias']] ?? '';
                $url = str_replace('__ANDROID_CHANNEL_ID__',$item['id'],$url);
                $item->feedback_url = str_replace('__IOS_CHANNEL_ID__',$item['id'],$url);

                $pageUrl = $advPageFeedBack[$item['adv_alias']] ?? '';
                $pageUrl = str_replace('__N8_MULTI_CHANNEL_ID__',0,$pageUrl);
                $pageUrl = str_replace('__ANDROID_CHANNEL_ID__',$item['id'],$pageUrl);
                $item->page_feedback_url = str_replace('__IOS_CHANNEL_ID__',$item['id'],$pageUrl);
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $item->admin_id ? $map[$item->admin_id]['name'] : '';
                $item->has_extend = $item->admin_id ? true : false;
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
            $map = $this->getAdminUserMap();

            foreach ($this->curdService->responseData as $item){
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $item->admin_id ? $map[$item->admin_id]['name'] : '';
                $item->has_extend = $item->admin_id ? true : false;
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->channel_extend;
            $this->curdService->responseData->product;
            $this->curdService->responseData->book;
            $this->curdService->responseData->chapter;
            $this->curdService->responseData->force_chapter;

            if(isset($this->curdService->responseData->channel_extend['admin_id'])){
                $adminId = $this->curdService->responseData->channel_extend['admin_id'];

                $map = $this->getAdminUserMap([
                    'id'  => $adminId
                ]);

                $this->curdService->responseData->channel_extend['admin_name'] = $map[$adminId]['name'];
            }else{
                $this->curdService->responseData->channel_extend['admin_name'] = '';
            }

            $this->curdService->responseData->has_extend = $this->curdService->responseData->channel_extend ? true : false;

        });
    }




    public function sync(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'product_id'    =>  'required',
        ]);

        $productId = $req['product_id'];
        $productInfo = (new ProductData())
            ->setParams(['id' => $productId])
            ->read();

        if(empty($productInfo)){
            throw new CustomException([
                'code' => 'PRODUCT_NOT_EXIST',
                'message' => '产品不存在',
            ]);
        }
        $cpType = ucfirst(Functions::camelize($productInfo['cp_type']));
        $class = "App\Services\\{$cpType}\ChannelService";
        if(!class_exists($class)){
            return $this->fail('FAIL','该产品暂无此功能');
        }

        $startDate = $endDate = date('Y-m-d');
        (new $class)->sync($startDate,$endDate,$productId);

        return $this->success();
    }


}

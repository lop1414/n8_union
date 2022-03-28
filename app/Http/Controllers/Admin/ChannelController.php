<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Helpers\Advs;
use App\Common\Helpers\Functions;
use App\Common\Helpers\Platform;
use App\Datas\ChannelData;
use App\Models\ChannelModel;
use App\Models\ProductModel;
use App\Services\ChannelService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChannelController extends BaseController
{

    protected $defaultOrderBy = 'updated_time';

    protected $cpChannelServices;



    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ChannelModel();
        $this->modelData = new ChannelData();

        parent::__construct();
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

                if(!$this->adminUserService->isAdmin()){
                    $adminIds = $this->adminUserService->getHasAuthAdminIds();
                    $builder->whereIn('e.admin_id',$adminIds);
                }
            }

            if(!empty($req['admin_id'])){
                $builder->where('e.admin_id',$req['admin_id']);
            }


            if(!empty($req['status'])){
                $builder->where('e.status',$req['status']);
            }

            if(!empty($req['adv_alias'])){
                $builder->where('e.adv_alias',$req['adv_alias']);
            }

            if(!empty($req['status'])){
                $builder->where('e.status',$req['status']);
            }

            if(!empty($req['os'])){
                Functions::hasEnum(PlatformEnum::class,$req['os']);

                $productIds = (new ProductModel())
                    ->whereIn('type',Platform::getOSProductType($req['os']))
                    ->get('id')
                    ->toArray();

                return $builder->whereIn('product_id',array_column($productIds,'id'));
            }

            $keyword = $this->curdService->requestData['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `channel_id` LIKE '%{$keyword}%')");
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

            $feedbackUrlParam = [];
            if($this->adminUserService->isSupport()){
                $feedbackUrlParam['support_admin_id'] = $this->adminUserService->readId();
            }

            $advFeedBack = Advs::getFeedbackUrlMap($feedbackUrlParam);
            $advPageFeedBack = Advs::getPageFeedbackUrlMap($feedbackUrlParam);

            foreach ($this->curdService->responseData['list'] as $item){
                $adminId = $item->admin_id ?? 0;
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $this->adminUserService->readName($adminId) ;
                $item->has_extend = !!$adminId;

                //监测链接
                $url = $advFeedBack[$item['adv_alias']] ?? '';
                $url = str_replace('__ANDROID_CHANNEL_ID__',$item['id'],$url);
                $item->feedback_url = str_replace('__IOS_CHANNEL_ID__',$item['id'],$url);

                // n8页面监测链接
                $pageUrl = $advPageFeedBack[$item['adv_alias']] ?? '';
                $pageUrl = str_replace('__N8_MULTI_CHANNEL_ID__',0,$pageUrl);
                $pageUrl = str_replace('__ANDROID_CHANNEL_ID__',$item['id'],$pageUrl);
                $item->page_feedback_url = str_replace('__IOS_CHANNEL_ID__',$item['id'],$pageUrl);


                //下载链接
                $popularizeUrl = [];
                isset($item->extends->hap_url) && $popularizeUrl[] = ['name' => 'hap链接', 'url'  => $item->extends->hap_url];
                isset($item->extends->h5_url) && $popularizeUrl[] = ['name' => 'h5链接', 'url'  => $item->extends->h5_url];
                isset($item->extends->http_url) && $popularizeUrl[] = ['name' => 'http链接', 'url'  => $item->extends->http_url];
                isset($item->extends->apk_url) && $popularizeUrl[] = ['name' => 'apk链接', 'url'  => $item->extends->hap_url];

                if($item['adv_alias'] == AdvAliasEnum::BD ){
                    $popularizeUrl = array_merge($popularizeUrl,$this->getJmyForwardUrl($item));
                }

                $item->popularize_url = $popularizeUrl;
                unset($item->extends);
            }
        });
    }


    /**
     * @param $data
     * @return array
     * 获取积木鱼转发url
     */
    public function getJmyForwardUrl($data){
        $productExtends = $data->product->extends;
        $extends = $data->extends;

        $company = config('common.company');
        $ret = [];
        $uri = '';
        $jumpUrl = '';
        if($data->product->type == ProductTypeEnums::KYY){
            $uri = '/forward/kyy.php';
            $jumpUrl = urlencode($extends->h5_url ?? '');
        }

        if($data->product->type == ProductTypeEnums::H5){
            $uri = '/forward';
            $jumpUrl = urlencode($productExtends['index_page_url'] ?? '');
        }

        if(empty($jumpUrl)) return [];

        foreach ($company as $item){
            $url = rtrim($item['page_url'], '/');
            $url .= $uri;
            $url .= '?a=https://www.taobao.com/';
            $url .= '&channel_id='.$data['id'];
            $url .= '&url='.$jumpUrl;
            $ret[] = [
                'name' => '积木鱼跳转链接-'.$item['name'],
                'url'  => $url
            ];
        }

        return $ret;
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
        });

        $this->curdService->getQueryAfter(function(){
            foreach ($this->curdService->responseData as $item){
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;

                $adminId = $item->admin_id ?? 0;
                $item->admin_name = $this->adminUserService->readName($adminId) ;
                $item->has_extend = !!$adminId;
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

                $this->curdService->responseData->channel_extend['admin_name'] = $this->adminUserService->readName($adminId);
            }else{
                $this->curdService->responseData->channel_extend['admin_name'] = '';
            }

            $this->curdService->responseData->has_extend = $this->curdService->responseData->channel_extend ? true : false;

        });
    }



    public function sync(Request $request){
        $req = $request->all();

        //根据id更新
        $channelIds = $req['channel_ids'];
        if(!empty($channelIds)){
            foreach ($channelIds as $channelId){
                $this->renew($channelId);
            }
            return $this->success();
        }


        $this->validRule($req,[
            'product_id'    =>  'required',
        ]);

        $product = ProductService::read($req['product_id']);

        (new ChannelService())->sync([
            'start_date' => date('Y-m-d',strtotime('-1 day')),
            'end_date'   => date('Y-m-d'),
            'product_ids'=> array($req['product_id']),
            'cp_type'    => $product['cp_type']
        ]);

        return $this->success();
    }


    protected function renew($channelId){
        $channelInfo = $this->model->where('id',$channelId)->first();
        $date = date('Y-m-d',strtotime($channelInfo->create_time));

        (new ChannelService())->sync([
            'start_date' => $date,
            'end_date'   => $date,
            'product_ids'   => array($channelInfo->product->id),
            'cp_type'       => $channelInfo->product->cp_type,
            'cp_channel_id' => $channelInfo->cp_channel_id
        ]);

        return $this->success();
    }


}

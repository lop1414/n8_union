<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Advs;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Models\BookModel;
use App\Models\ChannelExtendModel;
use App\Models\ChannelModel;
use App\Models\ChapterModel;
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
                ->select(DB::raw('channels.*,e.adv_alias,e.status,e.admin_id,e.parent_id'));

            $req = $this->curdService->requestData;


            $isSelf = $req['is_self'] ?? 0;
            if($isSelf){
                $builder->where('e.admin_id', $this->adminUserService->readId());
            }else{
                if(isset($req['is_bind']) && $req['is_bind'] == 0){
                    $builder->whereNull('e.admin_id');
                    unset($req['adv_alias']);
                }else{
                    $builder->where('e.admin_id','>',0);

                    if(!$this->adminUserService->isAdmin()){
                        if ($this->adminUserService->isSupport()){
                            //组管理员
                            $adminIds = $this->adminUserService->getGroupAdminIds();
                        }else{
                            //下属管理员
                            $adminIds = $this->adminUserService->getChildrenAdminIds();
                        }
                        $builder->whereIn('e.admin_id',$adminIds);
                    }

                }

                if(!empty($req['admin_id'])){
                    $builder->where('e.admin_id',$req['admin_id']);
                }
            }

            if(!empty($req['adv_alias'])){
                $builder->where('e.adv_alias',$req['adv_alias']);
            }

            if(!empty($req['status'])){
                $builder->where('e.status',$req['status']);
            }



            $keyword = $req['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `id` LIKE '%{$keyword}%')");
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
            $advPageFeedBack = Advs::getPageFeedbackUrlMap();

            $product = ProductModel::find($this->curdService->requestData['product_id'] ?? 0);
            $isCanCopy = false;
            if($product){
                $isCanCopy =  (new ChannelService())->isCanApiCreate($product);
            }

            $isAdmin = $this->adminUserService->isAdmin();
            foreach ($this->curdService->responseData['list'] as $item){
                $adminId = $item->admin_id ?? 0;
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $this->adminUserService->readName($adminId);
                $item->has_extend = !!$adminId;

                //可复制
                $item->is_can_copy = $isCanCopy;


                //监测链接
                $item->feedback_url = '';
                if($item->admin_id == $this->adminUserService->readId() || $isAdmin){
                    $url = $advFeedBack[$item['adv_alias']] ?? '';
                    $url = str_replace('__ANDROID_CHANNEL_ID__',$item['id'],$url);
                    $item->feedback_url = str_replace('__IOS_CHANNEL_ID__',$item['id'],$url);
                }


                // n8页面监测链接
                $pageUrl = $advPageFeedBack[$item['adv_alias']] ?? '';
                $pageUrl = str_replace('__N8_MULTI_CHANNEL_ID__',0,$pageUrl);
                $pageUrl = str_replace('__ANDROID_CHANNEL_ID__',$item['id'],$pageUrl);
                $item->page_feedback_url = str_replace('__IOS_CHANNEL_ID__',$item['id'],$pageUrl);
                $item->href_url = $item->extends->hap_url?? '';

                //下载链接
                $popularizeUrl = [];
                isset($item->extends->hap_url) && $popularizeUrl[] = ['name' => 'hap链接', 'url'  => $item->extends->hap_url];
                isset($item->extends->h5_url) && $popularizeUrl[] = ['name' => 'h5链接', 'url'  => $item->extends->h5_url];
                isset($item->extends->http_url) && $popularizeUrl[] = ['name' => 'http链接', 'url'  => $item->extends->http_url];
                isset($item->extends->apk_url) && $popularizeUrl[] = ['name' => 'apk链接', 'url'  => $item->extends->hap_url];
                isset($item->extends->page_path) && $popularizeUrl[] = ['name' => '应用路径', 'url'  => $item->extends->page_path];

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


    protected function syncByApi($cpType, $productType, $startDate, $endDate, $productIds = [] , $cpChannelId = 0){
        (new ChannelService())->sync($cpType, $productType, $startDate, $endDate, $productIds , $cpChannelId);
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 同步渠道
     */
    public function sync(Request $request){
        $req = $request->all();

        //根据id更新
        $channelIds = $req['channel_ids'] ?? [];
        if(!empty($channelIds)){
            foreach ($channelIds as $channelId){
                $this->renew($channelId);
            }
            return $this->success();
        }

        $this->validRule($req,['product_id' =>  'required']);

        $product = ProductService::read($req['product_id']);

        $this->syncByApi($product['cp_type'], $product['type'], date('Y-m-d',strtotime('-1 day')), date('Y-m-d'),array($req['product_id']));
        return $this->success();
    }


    protected function renew($channelId){
        $channelInfo = $this->model->where('id',$channelId)->first();
        $date = date('Y-m-d',strtotime($channelInfo->create_time));
        $this->syncByApi($channelInfo->product->cp_type, $channelInfo->product->type, $date, $date, array($channelInfo->product->id) , $channelInfo->cp_channel_id);
        return true;
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 复制渠道
     */
    public function copy(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'channel_id' => 'required',
            'name' => 'required',
            'adv_alias' => 'required',
            'status' => 'required',
        ],[
            'channel_id.required' => 'channel_id 不能为空',
            'name.required' => '名称不能为空',
            'adv_alias.required' => 'adv_alias 不能为空',
            'status.required' => 'status 不能为空',
        ]);
        Functions::hasEnum(AdvAliasEnum::class,$req['adv_alias']);
        Functions::hasEnum(StatusEnum::class,$req['status']);

        $copyChannel = $this->model->where('id',$req['channel_id'])->first();

        $channelService = new ChannelService();
        if(!$channelService->isCanApiCreate($copyChannel->product)){
            throw new CustomException(['code' => 'NOT_CAN_CREATE_CHANNEL', 'message' => "该小说平台暂不支持"]);
        }

        $adminId = $this->adminUserService->readId();
        $cpChannelId = $channelService->create($copyChannel->product,$req['name'],$copyChannel->book,$copyChannel->chapter,$copyChannel->force_chapter,$adminId);

        // 同步
        $date = date('Y-m-d');

        $this->syncByApi($copyChannel->product->cp_type, $copyChannel->product->type, $date, $date, array($copyChannel->product->id), $cpChannelId);

        // 认领
        $channel  = $this->model
            ->where('product_id',$copyChannel->product->id)
            ->where('cp_channel_id',$cpChannelId)
            ->first();
        $channelExtendModel = new ChannelExtendModel();
        $channelExtendModel->channel_id = $channel->id;
        $channelExtendModel->adv_alias = $req['adv_alias'];
        $channelExtendModel->status = $req['status'];
        $channelExtendModel->admin_id = $adminId;
        $channelExtendModel->parent_id = $copyChannel->id;
        $channelExtendModel->save();
        return $this->success();
    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('product_id')->addValidRule('required');
        $this->curdService->addField('cp_channel_id')->addValidRule('required');
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('book_id')->addValidRule('required');
        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);
        $this->curdService->addField('adv_alias')->addValidEnum(AdvAliasEnum::class);

        $this->curdService->saveBefore(function(){
            if($this->curdService->getModel()->uniqueExist([
                'product_id' => $this->curdService->handleData['product_id'],
                'cp_channel_id' => $this->curdService->handleData['cp_channel_id'],
            ])){
                throw new CustomException([
                    'code' => 'DATA_EXIST',
                    'message' => '渠道已存在'
                ]);
            }
            $this->curdService->handleData['create_time'] = $this->curdService->handleData['updated_time'] =  date('Y-m-d H:i:s');
        });

        $this->curdService->saveAfter(function (){
            $channelExtendModel = new ChannelExtendModel();

            $adminId = $this->curdService->requestData['admin_id'] ?? 0;
            $channelExtendModel->channel_id = $this->curdService->getModel()->id;
            $channelExtendModel->admin_id = $adminId ?: $this->adminUserService->readId();
            $channelExtendModel->status = $this->curdService->requestData['status'];
            $channelExtendModel->adv_alias = $this->curdService->requestData['adv_alias'];
            $channelExtendModel->parent_id = 0;
            $channelExtendModel->save();
        });
    }


    /**
     * 获取未绑定渠道
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function getNotBindChannel(Request $request){
        $requestData = $request->all();
        $this->validRule($requestData,['product_id' => 'required'],['product_id.required' => 'product_id 不能为空']);
        $keyword = $requestData['keyword'] ?? '';
        $product = (new ProductModel())->where('id',$requestData['product_id'])->first();

        //同步
        $this->syncByApi($product['cp_type'], $product['type'], date('Y-m-d',strtotime('-1 day')), date('Y-m-d'),array($product['id']));

        $page = $requestData['page'] ?? 1;
        $pageSize = $requestData['page_size'] ?? 10;
        $channel = $this->model
            ->leftJoin('channel_extends AS e','channels.id','=','e.channel_id')
            ->select(DB::raw('channels.*'))
            ->where('channels.product_id',$product['id'])
            ->whereNull('e.admin_id')
            ->when($keyword,function ($query,$keyword){
                return  $query->whereRaw(" (`name` LIKE '%{$keyword}%' OR `id` LIKE '%{$keyword}%' OR `cp_channel_id` LIKE '%{$keyword}%')");
            })
            ->listPage($page,$pageSize);

        foreach ($channel['list'] as $item){
            unset($item['extends']);
            $item->book;
            $item->chapter;
            $item->force_chapter;
        }

        return $this->success($channel);
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 创建后同步书城
     */
    public function createWithSync(Request $request){

        $req = $request->all();
        $this->validRule($req,[
            'product_id' => 'required',
            'name' => 'required',
            'book_id' => 'required',
            'chapter_id' => 'required',
//            'force_chapter_id' => 'required',
        ],[
            'product_id.required' => 'product_id 不能为空',
            'name.required' => '名称不能为空',
            'book_id.required' => 'book_id 不能为空',
            'chapter_id.required' => 'chapter_id 不能为空',
            'force_chapter_id.required' => 'chapter_id 不能为空',
        ]);

        $channelService = new ChannelService();
        $product = ProductModel::find($req['product_id']);
        if(!$product){
            throw new CustomException(['code' => 'INVALID_PRODUCT_ID', 'message' => "产品ID无效"]);
        }
        $book = BookModel::find($req['book_id']);
        if(!$book){
            throw new CustomException(['code' => 'INVALID_BOOK_ID', 'message' => "书籍ID无效"]);
        }
        $chapter = ChapterModel::find($req['chapter_id']);
        if(!$chapter){
            throw new CustomException(['code' => 'INVALID_CHAPTER_ID', 'message' => "章节ID无效"]);
        }

        $forceChapter = null;
        if(isset($req['force_chapter_id']) && !empty($req['force_chapter_id'])){
            $forceChapter = ChapterModel::find($req['force_chapter_id']);
            if(!$forceChapter){
                throw new CustomException(['code' => 'INVALID_FORCE_CHAPTER_ID', 'message' => "强制章节ID无效"]);
            }
        }
        $adminId = $this->adminUserService->readId();
        if(isset($req['admin_id'])){
            $admin = $this->adminUserService->read($req['admin_id']);
            if(!$admin){
                throw new CustomException(['code' => 'INVALID_ADMIN_ID', 'message' => "管理员ID无效"]);
            }
            $adminId = $admin['id'];
        }

        $cpChannelId = $channelService->create($product,$req['name'],$book,$chapter,$forceChapter,$adminId);


        // 同步
        $date = date('Y-m-d');

        $this->syncByApi($product->cp_type, $product->type, $date, $date, array($product->id), $cpChannelId);

        // 认领
        $channel  = $this->model
            ->where('product_id',$product->id)
            ->where('cp_channel_id',$cpChannelId)
            ->first();

        return $this->ret($channel, $channel);
    }
}

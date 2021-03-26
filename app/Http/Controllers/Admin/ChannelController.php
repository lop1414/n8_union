<?php


namespace App\Http\Controllers\Admin;


use App\Common\Helpers\Functions;
use App\Common\Services\SystemApi\CenterApiService;
use App\Models\ChannelModel;
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
     * 根据权限过滤
     */
    public function dataFilter(){
        $this->curdService->customBuilder(function ($builder){
            $builder->leftJoin('channel_extends AS e','channels.id','=','e.channel_id')
                ->select(DB::raw('channels.*,e.adv_alias,e.status,e.admin_id'));

            if(!$this->isDataAuth()){
                $builder->whereRaw(" (e.admin_id = {$this->adminUser['admin_user']['id']} OR e.admin_id IS NULL)");
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
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $item->admin_id ? $map[$item->admin_id] : '';
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
            $map = $this->getAdminUserName();

            foreach ($this->curdService->responseData as $item){
                $item->product;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $item->admin_id ? $map[$item->admin_id] : '';
                $item->has_extend = $item->admin_id ? true : false;
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->extend;
            $this->curdService->responseData->product;
            $this->curdService->responseData->book;
            $this->curdService->responseData->chapter;
            $this->curdService->responseData->force_chapter;

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

}

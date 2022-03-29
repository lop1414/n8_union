<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Models\N8UnionUserModel;
use Illuminate\Support\Facades\DB;

class N8UnionUserController extends UserActionBaseController
{

    public $convertType = ConvertTypeEnum::REGISTER;

    public $mapUnionUser = false;


    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'created_time';


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new N8UnionUserModel();

        parent::__construct();
    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){


        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $adminId = $this->curdService->requestData['admin_id'] ?? 0;
                $isSelf = $this->curdService->requestData['is_self'] ?? 1;

                if($isSelf){
                    $adminId = $this->adminUserService->readId();
                }

                if(!empty($adminId)){
                    $builder->where('admin_id',$adminId);
                }

                if(!$this->adminUserService->isAdmin()){
                    $adminIds = $this->adminUserService->getChildrenAdminIds();
                    $builder->whereIn('admin_id',$adminIds);
                }

                if($this->adminUserService->isSupport()){
                    $channel = DB::table('channel_supports')
                        ->where('admin_id', $this->adminUserService->readId())
                        ->select('channel_id');

                    $builder->LeftjoinSub($channel, 'channel', function($join){
                        $tableName = $this->model->getTable();
                        $join->on($tableName.'.channel_id', '=', 'channel.channel_id');
                    });
                }
            });
        });
        $this->filterAdv();

        $this->selectUserCommonFilter();
        $this->selectCommonPrepare();
    }


    public function itemPrepare($item){
        $item->extend;
        $item->book;
        $item->chapter;
        $item->force_chapter;
        $item->admin_name = $this->adminUserService->readName($item->admin_id);

    }


    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->readCommonPrepare();
        $this->curdService->findAfter(function(){
            $this->curdService->responseData->extend;
            $this->curdService->responseData->book;
            $this->curdService->responseData->chapter;
            $this->curdService->responseData->force_chapter;
            $this->curdService->responseData->admin_name = $this->adminUserService->readName($this->curdService->responseData->admin_id);
        });
    }
}

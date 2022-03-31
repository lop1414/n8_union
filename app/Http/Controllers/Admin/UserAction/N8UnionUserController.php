<?php


namespace App\Http\Controllers\Admin\UserAction;



use App\Common\Enums\ConvertTypeEnum;
use App\Models\N8UnionUserModel;



class N8UnionUserController extends UserActionBaseController
{

    public $convertType = ConvertTypeEnum::REGISTER;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new N8UnionUserModel();
        // 默认排序字段
        $tableName = $this->model->getTable();
        $this->defaultOrderBy =  $tableName.'.created_time';
        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->selectFilterOpenId();
        $this->selectFilter('id');
        $this->selectFilterAdv($this->convertType);

        $this->selectConvertMap($this->convertType);
        $this->selectCustomConvertMap($this->convertType,ConvertTypeEnum::PAY);

        $this->curdService->selectQueryAfter(function() {
            foreach ($this->curdService->responseData['list'] as $item){
                $item->user;
                $item->global_user;
                $item->channel;
                $item->extend;
                $item->book;
                $item->chapter;
                $item->force_chapter;
                $item->admin_name = $this->adminUserService->readName($item->admin_id);
            }
        });
    }
}

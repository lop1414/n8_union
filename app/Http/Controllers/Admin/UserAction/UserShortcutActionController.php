<?php


namespace App\Http\Controllers\Admin\UserAction;


use App\Common\Enums\ConvertTypeEnum;
use App\Models\UserShortcutActionModel;

class UserShortcutActionController extends UserActionBaseController
{


    public $convertType = ConvertTypeEnum::ADD_DESKTOP;



    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'action_time';


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UserShortcutActionModel();

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

        $this->curdService->selectQueryAfter(function() {
            foreach ($this->curdService->responseData['list'] as $item){
                $item->user;
                $item->global_user;
                $item->channel;
                $item->union_user;
                $item->union_user->channel;
                $item->admin_name = $this->adminUserService->readName($item->union_user->admin_id);
            }
        });
    }




}

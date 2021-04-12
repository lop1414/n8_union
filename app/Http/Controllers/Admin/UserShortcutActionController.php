<?php


namespace App\Http\Controllers\Admin;


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
        $this->selectUserCommonFilter();
        $this->selectCommonPrepare();
    }



    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->readCommonPrepare();
    }




}

<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Models\UserFollowActionModel;

class UserFollowActionController extends UserActionBaseController
{



    public $convertType = ConvertTypeEnum::FOLLOW;



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
        $this->model = new UserFollowActionModel();

        parent::__construct();
    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        $this->selectCommonPrepare();

    }



    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->readCommonPrepare();
    }




}

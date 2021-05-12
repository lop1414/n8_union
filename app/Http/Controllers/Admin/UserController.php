<?php


namespace App\Http\Controllers\Admin;


use App\Models\UserFollowActionModel;
use App\Models\UserModel;
use App\Models\UserShortcutActionModel;

class UserController extends BaseController
{

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
        $this->model = new UserModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        $this->selectUserCommonFilter();

        $this->curdService->selectQueryAfter(function(){
            foreach ($this->curdService->responseData['list'] as $item){
                $item->global_user;
                $item->channel;
                $item->extend;
            }
        });
    }





    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $tmpShortcut = (new UserShortcutActionModel())
                ->where('n8_guid',$this->curdService->responseData->n8_guid)
                ->count();

            $tmpFollow = (new UserFollowActionModel())
                ->where('n8_guid',$this->curdService->responseData->n8_guid)
                ->count();

            $this->curdService->responseData->isShortcut = !!$tmpShortcut;
            $this->curdService->responseData->isFollow = !!$tmpFollow;
            $this->curdService->responseData->global_user;
            $this->curdService->responseData->channel;
            $this->curdService->responseData->extend;
            $this->curdService->responseData->product;
        });
    }


}

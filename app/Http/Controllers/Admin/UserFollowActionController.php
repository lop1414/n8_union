<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Models\UserFollowActionModel;

class UserFollowActionController extends BaseController
{



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
        $this->curdService->selectQueryAfter(function(){


            if(!empty($this->curdService->responseData['list'])){
                $convert = [];
                foreach ($this->curdService->responseData['list'] as $item){
                    array_push($convert,[
                        'convert_type' => ConvertTypeEnum::FOLLOW,
                        'convert_id'   => $item['id']
                    ]);
                }

                $tmp = (new AdvOceanApiService())->apiGetConvertCallbacks($convert);

                $convertList = array_column($tmp,null,'convert_id');

                foreach ($this->curdService->responseData['list'] as $item){
                    $item->convert_callback = $convertList[$item['id']]['convert_callback'];
                    $item->user;
                    $item->channel;
                }
            }

        });
    }



    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $tmp = (new AdvOceanApiService())->apiGetConvertCallbacks([
                [
                    'convert_type' => ConvertTypeEnum::FOLLOW,
                    'convert_id'   => $this->curdService->responseData->id
                ]
            ]);

            $this->curdService->responseData->convert_callback = $tmp[0]['convert_callback'];
            $this->curdService->responseData->user;
            $this->curdService->responseData->channel;
        });
    }




}

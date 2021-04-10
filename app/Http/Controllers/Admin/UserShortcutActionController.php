<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Models\UserShortcutActionModel;
use App\Services\ConvertCallbackMapService;

class UserShortcutActionController extends BaseController
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
        $this->model = new UserShortcutActionModel();

        parent::__construct();
    }




    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){


            if(!empty($this->curdService->responseData['list'])){
                $convertList = (new ConvertCallbackMapService())
                    ->listMap($this->curdService->responseData['list'],ConvertTypeEnum::ADD_DESKTOP);

                foreach ($this->curdService->responseData['list'] as $item){
                    $item->convert_callback = $convertList[$item['id']]['convert_callback'];
                    $item->user;
                    $item->channel;
                    $item->union_user = $this->model->union_user($item->n8_guid,$item->channel_id);
                }
            }

        });
    }



    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){

            $convertList = (new ConvertCallbackMapService())
                ->listMap([$this->curdService->responseData],ConvertTypeEnum::ADD_DESKTOP);

            $this->curdService->responseData->convert_callback = $convertList[$this->curdService->responseData->id]['convert_callback'];
            $this->curdService->responseData->user;
            $this->curdService->responseData->channel;
            $this->curdService->responseData->union_user = $this->model->union_user($this->curdService->responseData->n8_guid,$this->curdService->responseData->channel_id);

        });
    }




}

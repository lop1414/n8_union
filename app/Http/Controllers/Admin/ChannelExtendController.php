<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Datas\ChannelExtendData;
use App\Models\ChannelExtendModel;
use App\Models\N8UnionUserModel;

class ChannelExtendController extends BaseController
{


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ChannelExtendModel();

        parent::__construct();

    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->curdService->addField('channel_id')->addValidRule('required');
        $this->curdService->addField('adv_alias')
            ->addValidRule('required')
            ->addValidEnum(AdvAliasEnum::class);

        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);

        // 追加主键
        $this->curdService->addColumns([$this->curdService->getModel()->getPrimaryKey()]);

        $this->curdService->saveBefore(function(){
            // 赋值 admin_id
            $adminUser = Functions::getGlobalData('admin_user_info');
            $this->curdService->handleData['admin_id'] = $adminUser['admin_user']['id'];
        });
    }




    /**
     * 更新预处理
     */
    public function updatePrepare(){

        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);

        $this->curdService->saveBefore(function(){

            $this->extendData();
            unset($this->curdService->handleData['adv_alias']);
        });
    }




    /**
     * @throws CustomException
     * 有注册用户 不可修改
     */
    public function extendData(){
        $tmp = (new N8UnionUserModel())
            ->where('channel_id',$this->curdService->getModel()->id)
            ->where('created_time',$this->curdService->getModel()->created_at)
            ->first();

        if(!empty($tmp)){
            throw new CustomException([
                'code' => 'NO_EDITING',
                'message' => '渠道已产生数据,信息不可修改'
            ]);
        }
    }
}

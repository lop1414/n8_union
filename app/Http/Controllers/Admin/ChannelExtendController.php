<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelExtendData;
use App\Models\ChannelExtendModel;
use App\Models\ChannelModel;
use App\Models\N8UnionUserModel;
use Illuminate\Http\Request;

class ChannelExtendController extends BaseController
{


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ChannelExtendModel();
        $this->modelData = new ChannelExtendData();

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



    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 批量保存
     */
    public function batchSave(Request $request){
        $requestData = $request->all();



        $this->validRule($requestData,[
            'channel_ids' => 'required|array',
            'adv_alias' => 'required'
        ],[
            'channel_ids.required' => 'channel_ids 不能为空',
            'channel_ids.array' => 'channel_ids 不是数组',
            'adv_alias.required' => 'adv_alias 不能为空'
        ]);

        Functions::hasEnum(AdvAliasEnum::class,$requestData['adv_alias']);

        if(!isset($requestData['status'])){
            $requestData['status'] = StatusEnum::ENABLE;
        }
        Functions::hasEnum(StatusEnum::class,$requestData['status']);



        // 赋值 admin_id
        $adminUser = Functions::getGlobalData('admin_user_info');
        $adminId = $adminUser['admin_user']['id'];


        $ret = [
            'success' => [],
            'fail' => []
        ];
        $channelModel = new ChannelModel();
        foreach ($requestData['channel_ids'] as $channelId){
            $cpChannelId = $channelModel->where('id',$channelId)->pluck('cp_channel_id')->first();

            try {
                $channelExtendModel = new ChannelExtendModel();
                $channelExtendModel->channel_id = $channelId;
                $channelExtendModel->adv_alias = $requestData['adv_alias'];
                $channelExtendModel->status = $requestData['status'];
                $channelExtendModel->admin_id = $adminId;
                $tmp = $channelExtendModel->save();

                if($tmp){
                    $ret['success'][] = [
                        'channel_id'    => $channelId,
                        'cp_channel_id' => $cpChannelId,
                        'message'       => '成功'
                    ];
                }else{
                    $ret['fail'][] = [
                        'channel_id'    => $channelId,
                        'cp_channel_id' => $cpChannelId,
                        'message'       => '入库失败'
                    ];
                }
            }catch (\Exception $e){

                //未命中唯一索引
                if($e->getCode() != 23000){
                    //日志
                    (new ErrorLogService())->catch($e);
                }else{
                    $ret['fail'][] = [
                        'channel_id'    => $channelId,
                        'cp_channel_id' => $cpChannelId,
                        'message'       => '已被认领，请刷新后重试！'
                    ];
                }
            }
        }

        return $this->success($ret);
    }

}

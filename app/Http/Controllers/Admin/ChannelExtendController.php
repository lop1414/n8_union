<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\ErrorLogService;
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
            $this->curdService->handleData['admin_id'] = $this->adminUserService->readId();
        });
    }




    /**
     * 更新预处理
     */
    public function updatePrepare(){

        $this->curdService->addField('status')->addValidEnum(StatusEnum::class);
        $this->curdService->addField('adv_alias')->addValidEnum(AdvAliasEnum::class);


        $this->curdService->saveBefore(function(){
            if($this->hasData()){
                unset($this->curdService->handleData['adv_alias']);
            }

            if(!$this->adminUserService->isAdmin()){
                unset($this->curdService->handleData['admin_id']);
            }

        });
    }




    /**
     * @return bool
     * 渠道下有注册用户
     */
    public function hasData(){
        $tmp = (new N8UnionUserModel())
            ->where('channel_id',$this->curdService->getModel()->id)
            ->where('created_time','>',$this->curdService->getModel()->created_at)
            ->first();

        if(!empty($tmp)){
            return false;
        }

        return true;
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
            'adv_alias' => 'required',
            'status' => 'required',
        ],[
            'channel_ids.required' => 'channel_ids 不能为空',
            'channel_ids.array' => 'channel_ids 不是数组',
            'adv_alias.required' => 'adv_alias 不能为空',
            'status.required' => 'status 不能为空',
        ]);

        Functions::hasEnum(AdvAliasEnum::class,$requestData['adv_alias']);
        Functions::hasEnum(StatusEnum::class,$requestData['status']);



        // 赋值 admin_id
        if($this->adminUserService->isAdmin() && !empty($requestData['admin_id'])){
            $adminId = $requestData['admin_id'];
        }else{
            $adminId = $this->adminUserService->readId();
        }


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
                $channelExtendModel->parent_id = 0;
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

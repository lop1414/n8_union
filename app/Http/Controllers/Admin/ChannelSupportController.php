<?php


namespace App\Http\Controllers\Admin;


use App\Common\Tools\CustomException;
use App\Models\ChannelSupportModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChannelSupportController extends ChannelController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 过滤
     */
    public function dataFilter(){

        $this->curdService->customBuilder(function ($builder){

            $builder->leftJoin('channel_extends AS e','channels.id','=','e.channel_id')
                ->leftJoin('channel_supports AS s','channels.id','=','s.channel_id')
                ->select(DB::raw('channels.*,e.adv_alias,e.status,e.admin_id,s.admin_id AS support_id'))
                ->where('e.admin_id','>',0);

            // 组管理员
            $adminIds = $this->adminUserService->getGroupAdminIds();
            $builder->whereIn('e.admin_id',$adminIds);

            $req = $this->curdService->requestData;
            if(empty($req['is_bind'])){
                $builder->whereNull('s.admin_id');
            }

            if(!empty($req['admin_id'])){
                $builder->where('e.admin_id',$req['admin_id']);
            }

            if(!empty($req['status'])){
                $builder->where('e.status',$req['status']);
            }

            if(!empty($req['adv_alias'])){
                $builder->where('e.adv_alias',$req['adv_alias']);
            }


            $keyword = $this->curdService->requestData['keyword'] ?? '';
            if(!empty($keyword)){
                $builder->whereRaw(" (`name` LIKE '%{$keyword}%' OR `channel_id` LIKE '%{$keyword}%')");
            }
        });
    }


    /**
     * @param $item
     * @return bool
     * 是否可复制监测链接
     */
    public function canCopyFeedBack($item): bool
    {
        return !empty($item->support_id);
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 绑定
     */
    public function bind(Request $request){
        $req = $request->all();

        $info = (new ChannelSupportModel())
            ->where('channel_id',$req['channel_id'])
            ->first();

        if(!empty($info)){
            throw new CustomException([
                'code' => 'REPEAT_BIND',
                'message' => '该渠道已被认领',
                'log' => false
            ]);
        }

        $model = new ChannelSupportModel();
        $model->channel_id = $req['channel_id'];
        $model->admin_id = $this->adminUserService->readId();
        $model->bind_time = date('Y-m-d H:i:s');
        $model->save();
        return $this->success();
    }

}

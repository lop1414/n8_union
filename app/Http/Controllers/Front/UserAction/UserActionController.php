<?php

namespace App\Http\Controllers\Front\UserAction;

use App\Common\Controllers\Front\FrontController;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use Illuminate\Http\Request;

class UserActionController extends FrontController
{

    protected $model;

    protected $timeFilterField;


    public function __construct()
    {
        parent::__construct();
    }


    public function setModel($model){
        $this->model = $model;
    }


    /**
     * @param $request
     * @throws CustomException
     * 验证
     */
    public function verify($request){
        $req = $request->all();
        $this->validRule($req,[
            'start_time'    =>  'required',
            'end_time'      =>  'required',
        ]);

        Functions::checkTimeRange($req['start_time'],$req['end_time']);

        $diff = strtotime($req['end_time']) - strtotime($req['start_time']);
        if($diff > 30*60){
            throw new CustomException([
                'code' => 'DATE_TIME_RANGE_ERROR',
                'message' => '日期时间范围不能超过30分钟',
            ]);
        }

    }


    public function get(Request $request){

        $startTime = $request->get('start_time');
        $endTIme = $request->get('end_time');
        $productId = $request->get('product_id');
        $channelId = $request->get('channel_id');
        $advAlias = $request->get('adv_alias');
        $fields = $request->get('fields');
        $list = $this->model
            ->when($productId,function ($query,$productId){
                return $query->where('product_id',$productId);
            })
            ->when($channelId,function ($query,$channelId){
                return $query->where('channel_id',$channelId);
            })
            ->when($advAlias,function ($query,$advAlias){
                return $query->where('adv_alias',$advAlias);
            })
            ->whereBetween($this->timeFilterField,[$startTime,$endTIme])
            ->get();


        $result = [];
        foreach ($list as $key => $item){
            $tmp = $this->item($item)->toArray();

            if(empty($fields)){
                $result[$key] = $tmp;
            }else{

                //过滤字段
                foreach ($tmp as $k => $v){
                    if(in_array($k,$fields)){
                        $result[$key][$k] = $v;
                    }
                }
            }
        }


        return $this->success($result);
    }




    public function item($item){
        return $item;
    }





}

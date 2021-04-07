<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Common\Helpers\Functions;
use App\Models\UserLoginActionModel;

class UserLoginActionData extends BaseData
{

    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = false;

    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(UserLoginActionModel::class);
    }


    /**
     * @param $n8Guid
     * @param $timeRange
     * @return array
     * @throws \App\Common\Tools\CustomException
     * 按日期范围获取最近一条数据
     */
    public function readLastDataByRange($n8Guid,$timeRange){
        $monthList = Functions::getMonthListByRange($timeRange,'Y-m-01');
        foreach ($monthList as $month){
            $tmp = $this->model->setTableNameWithMonth($month)
                ->where('n8_guid',$n8Guid)
                ->whereBetween('action_time',$timeRange)
                ->orderBy('action_time','desc')
                ->first();

            if(!empty($tmp)){
                return $tmp;
            }
        }

        return  [];
    }

}

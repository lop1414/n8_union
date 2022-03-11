<?php

namespace App\Services\Device;

use App\Common\Services\BaseService;
use App\Models\N8UnionUserModel;


class DeviceBaseService extends BaseService
{


    /**
     * @return array
     * 获取设备机型
     */
    public function getDeviceModel($startTime = '',$endTime = ''){
        $list = (new N8UnionUserModel())
            ->when($startTime && $endTime ,function ($query) use ($startTime,$endTime){
                return $query->whereBetween('created_time', [$startTime, $endTime]);
            })
            ->groupBy('device_model')
            ->get('device_model');
        $res = [];
        if(!$list->isEmpty()){
            $res = array_column($list->toArray(),'device_model');
            // 过滤空字符串
            $res = array_filter($res);
        }

        return $res;
    }

}

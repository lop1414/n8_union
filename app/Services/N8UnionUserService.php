<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use Illuminate\Support\Facades\DB;

class N8UnionUserService extends BaseService
{



    public function create($data){

        try{
            DB::beginTransaction();


            $ret = (new N8UnionUserModel())->create([
                'n8_guid'      => $data['n8_guid'],
                'channel_id'   => $data['channel_id'],
                'created_time' => $data['created_time'],
                'click_id'     => $data['click_id'] ?? 0,
                'created_at'   => date('Y-m-d H:i:s')
            ]);


            (new N8UnionUserExtendModel())->create([
                'uuid'                  => $ret->id,
                'ip'                    => $data['ip'] ?? '',
                'ua'                    => $data['ua'] ?? '',
                'muid'                  => $data['muid'] ?? '',
                'oaid'                  => $data['oaid'] ?? '',
                'device_brand'          => $data['device_brand'] ?? '',
                'device_manufacturer'   => $data['device_manufacturer'] ?? '',
                'device_model'          => $data['device_model'] ?? '',
                'device_product'        => $data['device_product'] ?? '',
                'device_os_version_name'=> $data['device_os_version_name'] ?? '',
                'device_os_version_code'=> $data['device_os_version_code'] ?? '',
                'device_platform_version_name' => $data['device_platform_version_name'] ?? '',
                'device_platform_version_code' => $data['device_platform_version_code'] ?? '',
                'android_id'            => $data['android_id'] ?? '',
                'request_id'            => $data['request_id'] ?? ''
            ]);


            $ret->extend;
            DB::commit();

        }catch (\Exception $e){
            DB::rollBack();
            $ret =  false;
        }

        return $ret;

    }




}

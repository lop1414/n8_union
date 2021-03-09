<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Models\CpChannelModel;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use Illuminate\Support\Facades\DB;

class N8UnionUserService extends BaseService
{



    public function create($data){

        try{
            DB::beginTransaction();

            $channelInfo = (new ChannelModel())
                ->where('id',$data['channel_id'])
                ->first();
            $cpChannelInfo = (new CpChannelModel())
                ->where('product_id',$channelInfo->product_id)
                ->where('cp_channel_id',$channelInfo->cp_channel_id)
                ->first();

            $ret = (new N8UnionUserModel())->create([
                'n8_guid'       => $data['n8_guid'],
                'channel_id'    => $data['channel_id'],
                'created_time'  => $data['created_time'],
                'cp_book_id'    => $cpChannelInfo->cp_book_id,
                'cp_chapter_id' => $cpChannelInfo->cp_chapter_id,
                'cp_force_chapter_id' => $cpChannelInfo->cp_force_chapter_id,
                'admin_id'      => $channelInfo->admin_id,
                'created_at'    => date('Y-m-d H:i:s')
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


            DB::commit();

            $ret->extend;

            return $ret;

        }catch (\Exception $e){
            DB::rollBack();

            if($e->getCode() == 23000){
                throw new CustomException([
                    'code'      => 'UUID_EXIST',
                    'message'   => '用户已存在',
                    'log'       => true,
                    'data'      => $data
                ]);
            }
        }

    }




}

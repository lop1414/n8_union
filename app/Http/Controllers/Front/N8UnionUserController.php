<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Traits\ValidRule;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use Illuminate\Http\Request;

class N8UnionUserController extends FrontController
{
    use ValidRule;

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function create(Request $request){
        $requestData = $request->all();

        $this->validRule($requestData,[
            'n8_guid'      => 'required',
            'channel_id'   => 'required',
            'created_time' => 'required'
        ]);


        $ret = (new N8UnionUserModel())->create([
            'n8_guid'      => $requestData['n8_guid'],
            'channel_id'   => $requestData['channel_id'],
            'created_time' => $requestData['created_time'],
            'click_id'     => $requestData['click_id'] ?? 0,
            'created_at'   => date('Y-m-d H:i:s')
        ]);


        (new N8UnionUserExtendModel())->create([
            'uuid'                  => $ret->id,
            'ip'                    => $requestData['ip'] ?? '',
            'ua'                    => $requestData['ua'] ?? '',
            'muid'                  => $requestData['muid'] ?? '',
            'oaid'                  => $requestData['oaid'] ?? '',
            'device_brand'          => $requestData['device_brand'] ?? '',
            'device_manufacturer'   => $requestData['device_manufacturer'] ?? '',
            'device_model'          => $requestData['device_model'] ?? '',
            'device_product'        => $requestData['device_product'] ?? '',
            'device_os_version_name'=> $requestData['device_os_version_name'] ?? '',
            'device_os_version_code'=> $requestData['device_os_version_code'] ?? '',
            'device_platform_version_name' => $requestData['device_platform_version_name'] ?? '',
            'device_platform_version_code' => $requestData['device_platform_version_code'] ?? '',
            'android_id'            => $requestData['android_id'] ?? '',
            'request_id'            => $requestData['request_id'] ?? ''
        ]);


        $ret->extend;

        return $this->ret($ret,$ret);
    }


}

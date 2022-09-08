<?php
namespace App\Http\Controllers\Admin;



use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\DeviceBrandEnum;
use App\Common\Enums\SystemAliasEnum;
use Illuminate\Http\Request;

class EnumController extends BaseController
{


    /**
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request){
        $requestData = $request->all();
        $type = $requestData['type'] ?? '';
        $arr = [];
        switch ($type){
            case 'device_brand':
                // 设备品牌
                $arr = DeviceBrandEnum::$list;
                break;
            case 'sys_alias':
                //系统别名
                $arr = SystemAliasEnum::$list;
                break;
            case 'cp_type':
                //产品平台类型
                $arr = CpTypeEnums::$list;
                break;
        }


        return $this->success($arr);
    }

}

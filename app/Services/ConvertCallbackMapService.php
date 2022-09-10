<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\AdvBdApiService;
use App\Common\Services\SystemApi\AdvGdtApiService;
use App\Common\Services\SystemApi\AdvKsApiService;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Common\Services\SystemApi\AdvOceanV2ApiService;
use App\Common\Services\SystemApi\AdvUcApiService;

class ConvertCallbackMapService extends BaseService
{

    public function listMap($list,$convertType,$convertId = 'id'){
        $convertList = [];
        foreach ($list as $item){
            if(empty($item['click_id'])) continue;

            $advAlias = $convertType == ConvertTypeEnum::REGISTER ? $item['adv_alias'] : $item->union_user['adv_alias'];

            // 未完成订单无需映射
            if($convertType == ConvertTypeEnum::PAY && $item->status != OrderStatusEnums::COMPLETE){
                continue;
            }

            $convertList[$advAlias][] = array(
                'convert_type' => $convertType,
                'convert_id'   => $item[$convertId]
            );

        }
        $result = [];
        foreach ($convertList as $adv => $convert){
            if(!empty($adv) && $adv != AdvAliasEnum::UNKNOWN){
                $adv = Functions::camelize($adv);
                $result += $this->$adv($convert);
            }
        }
        return $result;
    }



    public function ocean($convert){
        $tmp = (new AdvOceanApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }

    public function oceanV2($convert){
        $tmp = (new AdvOceanV2ApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }


    public function bd($convert){
        $tmp = (new AdvBdApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }

    public function ks($convert){
        $tmp = (new AdvKsApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }

    public function uc($convert){
        $tmp = (new AdvUcApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }

    public function gdt($convert){
        $tmp = (new AdvGdtApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }

}

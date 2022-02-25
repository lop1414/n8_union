<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\ConvertTypeEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\AdvBdApiService;
use App\Common\Services\SystemApi\AdvKsApiService;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Common\Services\SystemApi\AdvUcApiService;

class CustomConvertCallbackMapService extends BaseService
{

    public function listMap($list,$convertType,$convertId = 'id'){
        $convertList = [];
        foreach ($list as $item){
            if(empty($item['click_id'])) continue;

            $advAlias = $item['adv_alias'];
            if( $convertType != ConvertTypeEnum::REGISTER && !empty($item->union_user)){
                $advAlias = $item->union_user['adv_alias'];
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
        $tmp = (new AdvOceanApiService())->apiGetCustomConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }


    public function bd($convert){
        return [];
    }

    public function ks($convert){
        return [];
    }

    public function uc($convert){
        return [];
    }

}

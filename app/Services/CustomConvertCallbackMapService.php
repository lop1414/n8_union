<?php

namespace App\Services;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\OrderStatusEnums;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\AdvOceanApiService;

class CustomConvertCallbackMapService extends BaseService
{

    /**
     * @param $list
     * @param string $convertType     回传转化类型
     * @param string $convertId
     * @return array
     */
    public function listMap($list, string $convertType, string $convertId = 'id'){
        $convertList = [];
        foreach ($list as $item){
            if(empty($item['click_id'])) continue;

            $advAlias = $convertType == ConvertTypeEnum::REGISTER ? $item['adv_alias'] : $item->union_user['adv_alias'];

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

    public function oceanV2($convert){
        return [];
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

    public function gdt($convert){
        return [];
    }

}

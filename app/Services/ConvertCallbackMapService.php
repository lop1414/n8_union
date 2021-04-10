<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\AdvOceanApiService;

class ConvertCallbackMapService extends BaseService
{

    public function listMap($list,$convertType,$convertId = 'id'){
        $convertList = [];
        foreach ($list as $item){
            $convertList[$item['adv_alias']][] = array(
                'convert_type' => $convertType,
                'convert_id'   => $item[$convertId]
            );
        }
        $result = [];
        foreach ($convertList as $adv => $convert){
            $adv = strtolower($adv);
            $result += $this->$adv($convert);
        }
        return $result;
    }



    public function ocean($convert){
        $tmp = (new AdvOceanApiService())->apiGetConvertCallbacks($convert);
        return array_column($tmp,null,'convert_id');
    }


}

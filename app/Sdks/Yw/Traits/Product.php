<?php

namespace App\Sdks\Yw\Traits;


trait Product
{

    public function getKyyProduct($param){
        $uri = 'cpapi/wxRecharge/getapplist';
        $param['coop_type'] = 11;
        return $this->apiRequest($uri,$param);
    }



    public function getH5Product($param){
        $uri = 'cpapi/wxRecharge/getapplist';
        return $this->apiRequest($uri,$param);
    }

}

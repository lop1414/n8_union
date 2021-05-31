<?php

namespace App\Sdks\Yw\Traits;


trait User
{

    public function getUsers($param){
        $uri = 'cpapi/WxUserInfo/QuickAppFbQueryUserInfo';

        return $this->apiRequest($uri,$param);
    }

}

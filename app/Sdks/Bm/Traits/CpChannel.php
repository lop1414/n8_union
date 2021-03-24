<?php

namespace App\Sdks\Bm\Traits;


trait CpChannel
{

    public function getCpChannel($param){
        $uri = 'foreign/channels';

        return $this->apiRequest($uri,$param);
    }

}

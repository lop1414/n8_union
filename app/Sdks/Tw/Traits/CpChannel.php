<?php

namespace App\Sdks\Tw\Traits;


trait CpChannel
{

    public function getCpChannel($param){
        $uri = 'channel_list';

        return $this->apiRequest($uri,$param);
    }

}

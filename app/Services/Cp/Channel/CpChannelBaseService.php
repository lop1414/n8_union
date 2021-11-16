<?php

namespace App\Services\Cp\Channel;


use App\Datas\ChannelData;
use App\Models\ChannelModel;
use App\Services\Cp\CpBaseService;

class CpChannelBaseService extends CpBaseService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }




    public function save($data){
        $channelModelData = new ChannelData();
        return $channelModelData->save($data);
    }
}

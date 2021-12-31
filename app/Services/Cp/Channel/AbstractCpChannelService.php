<?php

namespace App\Services\Cp\Channel;


use App\Datas\ChannelData;
use App\Models\ChannelModel;
use App\Services\Cp\CpBaseService;

abstract class AbstractCpChannelService extends CpBaseService
{
    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }



    /**
     * @return mixed
     * 同步
     */
    abstract protected function sync();



    /**
     * @return mixed
     * 根据ID同步
     */
    abstract protected function syncById();



    public function save($data){
        $channelModelData = new ChannelData();
        return $channelModelData->save($data);
    }
}

<?php

namespace App\Services\UserActionMatch;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Models\N8UnionUserModel;


class RegActionMatchService extends UserActionMatchService
{

    protected $convertType = ConvertTypeEnum::REGISTER;


    public function __construct(){
        parent::__construct();
        $model = new N8UnionUserModel();
        $this->setModel($model);
    }



    public function getQuery(){
        return $this->model
            ->where('adv_alias',$this->advAlias)
            ->when($this->timeRange,function ($query){
                $query->whereBetween('created_at',$this->timeRange);
            })
            ->where('click_id',0)
            ->orderBy('created_time');
    }


    public function ocean(){

        $this->modelListPage(function ($list){
            $convert = [];
            foreach ($list as $item){

                $tmp = [
                    'convert_type' => $this->convertType,
                    'convert_id'   => $item['id'],
                    'convert_at'   => $item['created_time'],
                    'convert_times'=> 1,
                    'n8_union_user'=> [
                        'guid'  => $item['n8_guid'],
                        'channel_id' => $item['channel_id'],
                        'created_at' => $item['created_time'],
                        'click_source'  => $this->getAdvClickSourceEnum($item['matcher'])
                    ]
                ];

                $extend = $item->extend ? $item->extend->toArray() : [];
                array_push($convert,array_merge($tmp,$extend));
            }

            if(!empty($convert)){
                $matchList = (new AdvOceanApiService())->apiConvertMatch($convert);

                // 保存click_id
                foreach ($matchList as $match){
                    if($match['click_id'] > 0){
                        (new N8UnionUserModel)
                            ->where('id',$match['convert_id'])
                            ->update(['click_id' => $match['click_id']]);
                    }
                }
            }


        });
    }



}

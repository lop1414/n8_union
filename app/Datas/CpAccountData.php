<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Common\Enums\MatcherEnum;
use App\Common\Enums\OperatorEnum;
use App\Common\Enums\StatusEnum;
use App\Models\CpAccountModel;

class CpAccountData extends BaseData
{

    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [
        ['account','cp_type']
    ];



    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(CpAccountModel::class);
    }



    public function save($data){
        $where = [
            'account' => $data['account'],
            'cp_type' => $data['cp_type']
        ];

        //清除缓存
        $this->setParams($where)->clear();

        $info = (clone $this->getModel())->where($where)->first();

        if(empty($info)){
            $info = clone $this->getModel();
            $info->account = $data['account'];
            $info->cp_secret = $data['cp_secret'];
            $info->cp_type = $data['cp_type'];
            $info->status = StatusEnum::ENABLE;
        }
        if(isset($data['cp_secret'])){
            $info->cp_secret = $data['cp_secret'];
        }

        $info->save();
        return $info;
    }

}

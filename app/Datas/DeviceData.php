<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\DeviceModel;

class DeviceData extends BaseData
{

    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = true;


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
        ['model']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(DeviceModel::class);
    }



    public function save($data){

        $where = [
            'model' => $data['model']
        ];
        $this->setParams($where)->clear();

        return $this->model->updateOrCreate($where, [
                'name'    => $data['name'],
                'brand'   => $data['brand'],
                'model'   => $data['model'],
            ]
        );
    }

}

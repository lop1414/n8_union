<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Models\UaDeviceLogModel;
use App\Models\UaDeviceModel;

class UaDeviceData extends BaseData
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
        parent::__construct(UaDeviceModel::class);
    }



    public function create($data = [],$logExtend = []){
        $info = $this->getModel();
        $info->model = $data['device_model'];
        $info->name  = $data['name'];
        $info->brand = $data['brand'];
        $info->save();

        $this->createLog($info->toArray(),$logExtend);

        return $info;
    }

    public function createLog($info = [],$extend = []){
        $log = new UaDeviceLogModel();
        $log->ua_device_id = $info['id'];
        $log->name = $info['name'];
        $log->model = $info['model'];
        $log->brand = $info['brand'];
        $log->extends = $extend;
        $log->save();
        return $log;
    }

    public function update($where = [],$update = [],$logExtend = []){
        if(empty($update)){
            return $this->setParams($where)->read();
        }

        $this->model->where($where)->update($update);

        // 删除缓存
        $this->setParams($where)->clear();

        $info = $this->setParams($where)->read();

        $this->createLog($info,$logExtend);

        return $info;
    }

}

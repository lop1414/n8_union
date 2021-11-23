<?php

namespace App\Services;



use App\Common\Services\BaseService;
use App\Models\ProductAdminLogModel;
use App\Models\ProductAdminModel;

class ProductAdminService extends BaseService
{

    /**
     * @param $data
     * 批量更新
     */
    public function batchUpdate($data){
        foreach ($data['admin_ids'] as $adminId){
            foreach ($data['product_ids'] as $productId){
                $this->update([
                    'admin_id' => $adminId,
                    'product_id' => $productId,
                    'status' => $data['status'],
                ]);
            }
        }
    }

    /**
     * @param $data
     * @return bool
     * 更新
     */
    public function update($data){
        $productAdminModel = new ProductAdminModel();

        $productAdmin = $productAdminModel->where('product_id', $data['product_id'])
            ->where('admin_id', $data['admin_id'])
            ->first();

        $flag = $this->buildFlag($productAdmin);
        if(empty($productAdmin)){
            $productAdmin = new ProductAdminModel();
            // 初始化记录
            if($data['admin_id'] == 0){
                $productAdmin->created_at = '2000-01-01 00:00:00';
                $productAdmin->updated_at = '2000-01-01 00:00:00';
            }
        }

        $productAdmin->product_id = $data['product_id'];
        $productAdmin->admin_id = $data['admin_id'];
        $productAdmin->status = $data['status'];
        $ret = $productAdmin->save();

        if($ret && !empty($productAdmin->id) && $flag != $this->buildFlag($productAdmin)){
            $this->createChannelAdLog($productAdmin);
        }

        return $ret;
    }


    /**
     * @param $productAdmin
     * @return string
     * 构建标识
     */
    protected function buildFlag($productAdmin){
        if(empty($productAdmin)){
            $flag = '';
        }else{
            $flag = implode("_", [
                $productAdmin->product_id,
                $productAdmin->admin_id,
                $productAdmin->status
            ]);
        }
        return $flag;
    }

    /**
     * @param $data
     * @return bool
     * 创建产品-管理员日志
     */
    protected function createChannelAdLog($data){
        $productAdminLog = new ProductAdminLogModel();
        $productAdminLog->product_admin_id = $data['id'];
        $productAdminLog->product_id = $data['product_id'];
        $productAdminLog->admin_id = $data['admin_id'];
        $productAdminLog->status = $data['status'];
        $productAdminLog->created_at = $data['created_at'];
        $productAdminLog->updated_at = $data['updated_at'];
        return $productAdminLog->save();
    }
}

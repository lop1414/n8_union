<?php

namespace App\Services\Cp\Product;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Sdks\Yw\YwSdk;

class YwProductService extends AbstractCpProductService
{
    protected $cpType = CpTypeEnums::YW;


    public function sync(){
        $cpAccount = $this->getParam('cp_account');
        $startTime = strtotime($this->getParam('start_date'));
        $endTime = strtotime($this->getParam('end_date'));
        $sdk = new YwSdk('',$cpAccount->account,$cpAccount->cp_secret);

        // 快应用产品
        $data = $sdk->getKyyProduct([
            'start_time'  => $startTime,
            'end_time'    => $endTime
        ]);
        $this->saveData($data['list'],$cpAccount->id,ProductTypeEnums::KYY);


        // h5产品
        $h5Data = $sdk->getH5Product([
            'start_time'  => $startTime,
            'end_time'    => $endTime
        ]);
        $this->saveData($h5Data['list'],$cpAccount->id,ProductTypeEnums::H5);
    }


    public function saveData($list,$cpAccountId,$type){
        foreach ($list as $product){
            $this->save([
                'cp_account_id'     => $cpAccountId,
                'cp_product_alias'  => $product['appflag'],
                'cp_type'           => $this->cpType,
                'type'              => $type,
                'name'              => $product['app_name']
            ]);
        }
    }
}

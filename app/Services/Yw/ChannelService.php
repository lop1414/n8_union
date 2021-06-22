<?php

namespace App\Services\Yw;



use App\Common\Enums\ResponseCodeEnum;
use App\Common\Enums\SystemAliasEnum;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;



class ChannelService extends YwService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }


    public function sync($startDate,$endDate,$productId = null){
        $where = $productId ? ['id'=>$productId] : [];
        $productList = $this->getProductList($where);

        $repData = [
            'start_date'    => $startDate,
            'end_date'      => $endDate
        ];

        foreach ($productList as $product){
            $repData['product_id'] = $product['id'];
            $url = config('common.system_api.'.SystemAliasEnum::TRANSFER.'.url').'/open/sync_yw_channel?'. http_build_query($repData);
            $res = json_decode(file_get_contents($url),true);
            if($res['code'] != ResponseCodeEnum::SUCCESS){
                throw new CustomException([
                    'code' => $res['code'],
                    'message' => $res['message']
                ]);
            }
        }
    }
}

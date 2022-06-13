<?php

namespace App\Services\Weixin\MiniProgram;


use App\Datas\ProductWeixinMiniProgramData;
use App\Datas\WeixinMiniProgramData;

class WeixinMiniProgramUrlLinkService extends WeixinMiniProgramService
{

    public function make($productId){
        $productWeixinMiniProgram = (new ProductWeixinMiniProgramData())->setParams(['product_id' => $productId])->read();
        $weixinMiniProgram = (new WeixinMiniProgramData())->setParams(['id' => $productWeixinMiniProgram['weixin_mini_program_id']])->read();

        $query = 'url='.urlencode($productWeixinMiniProgram['guide_url']);
        $res =  $this->sdk->generateUrlLink($weixinMiniProgram['access_token'], $productWeixinMiniProgram['guide_path'],$query);
        return $res['url_link'];
    }
}

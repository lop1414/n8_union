<?php


namespace App\Http\Controllers\Open;


use App\Common\Enums\CpTypeEnums;
use App\Datas\ProductData;
use App\Enums\QueueEnums;
use App\Common\Services\DataToQueueService;
use Illuminate\Http\Request;

class YwController extends BaseController
{


    /**
     * @param Request $request
     * @return mixed
     * 阅读行为
     */
    public function read(Request $request){
        $reqData = $request->all();

        $product = $this->getProduct($reqData['appflag']);
        $service = new DataToQueueService(QueueEnums::USER_READ_ACTION);
        $readTime = !empty($reqData['read_time']) ? date('Y-m-d H:i:s',$reqData['read_time']) : date('Y-m-d H:i:s');
        $service->push([
            'product_alias' => $reqData['appflag'],
            'product_id' => $product['id'],
            'cp_type' => CpTypeEnums::YW,
            'open_id' => $reqData['guid'],
            'action_time' => $readTime,
            'cp_channel_id' => '',
            'cp_book_id'    => $reqData['book_id'],
            'cp_book_name'  => $reqData['book_name'],
            'cp_chapter_id' => $reqData['chapter_id'],
            'cp_chapter_name' => $reqData['chapter_name'] ?? '',
            'cp_chapter_index'=> $reqData['chapter_index'],
            'extends'         => $reqData
        ]);

        return $this->success();
    }


    public function getProduct($productAlias){
        return (new ProductData())
            ->setParams([
                'cp_product_alias'  => $productAlias,
                'cp_type'           => CpTypeEnums::YW
            ])
            ->read();
    }







}

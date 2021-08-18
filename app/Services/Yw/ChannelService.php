<?php

namespace App\Services\Yw;


use App\Common\Enums\ProductTypeEnums;
use App\Common\Enums\ResponseCodeEnum;
use App\Common\Enums\SystemAliasEnum;
use App\Common\Tools\CustomException;
use App\Models\ChannelModel;
use App\Sdks\Yw\YwSdk;


class ChannelService extends YwService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }


    public function sync($startDate,$endDate,$productId = null,$channelIds = [],$productType = null){
        $where = $productId ? ['id'=>$productId] : [];
        if(!empty($productType)){
            $where['type'] = $productType;
        }
        $productList = $this->getProductList($where);



        foreach ($productList as $product){

            $date = $startDate;
            do{

                $startTime = $date.' 00:00:00';
                $endTime = $date.' 23:59:59';
                if($product['type'] == ProductTypeEnums::KYY){
                    if(empty($channelIds)){
                        $this->syncKyyItem($startTime,$endTime,$product);
                    }else{
                        $this->syncKyyItemById($product,$channelIds);
                    }
                }

                if($product['type'] == ProductTypeEnums::H5){
                    $repData = [
                        'product_id'    => $product['id'],
                        'start_date'    => $date,
                        'end_date'      => $date
                    ];
                    $url = config('common.system_api.'.SystemAliasEnum::TRANSFER.'.url').'/open/sync_yw_channel?'. http_build_query($repData);
                    $res = json_decode(file_get_contents($url),true);
                    if($res['code'] != ResponseCodeEnum::SUCCESS){
                        throw new CustomException([
                            'code' => $res['code'],
                            'message' => '请联系管理员!'
                        ]);
                    }
                }

                $date = date('Y-m-d',  strtotime('+1 day',strtotime($date)) );
            }while($date <= $endDate);

        }
    }


    public function syncKyyItem($startTime,$endTime,$product){
        $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
        $currentTotal = 0;
        $page = 1;

        do{
            $list  = $sdk->getChannelList($startTime,$endTime,$page);
            $total = $list['total_count'];
            $currentTotal += count($list['list']);
            foreach ($list['list'] as $item){
                $model = (new ChannelModel())
                    ->where('product_id',$product['id'])
                    ->where('cp_channel_id',$item['channel_id'])
                    ->first();

                if(empty($model)){
                    $model = new ChannelModel();
                }

                $book = (new BookService())->setProduct($product)->read($item['cbid']);
                $chapterService = (new ChapterService())->setProduct($product)->setBook($book);
                $chapter = $chapterService->read($item['ccid']);
                $forceChapter = $chapterService->readBySeq($item['force_chapter']);


                $model->product_id = $product['id'];
                $model->cp_channel_id = $item['channel_id'];
                $model->name = $item['channel_name'];
                $model->book_id = $book['id'];
                $model->chapter_id = $chapter['id'] ?? 0;
                $model->force_chapter_id = $forceChapter['id'] ?? 0;
                $model->extends = [
                    'hap_url'   => $item['hap_url'],
                    'h5_url'    => $item['h5_url'],
                    'http_url'  => $item['http_url'],
                    'apk_url'   => $item['apk_url'],
                ];
                $model->create_time = date('Y-m-d H:i:s',$item['create_time']);
                $model->updated_time = date('Y-m-d H:i:s',$item['create_time']);
                $model->save();

            }
            $page += 1;

        }while($currentTotal < $total);
    }


    public function syncKyyItemById($product,$channelIds){
        $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
        $channelList = (new ChannelModel())->whereIn('id',$channelIds)->get();
        foreach ($channelList as $channel){
            $startTime = date('Y-m-d H:i:s',strtotime($channel['create_time']) - 60 * 10);
            $endTime = date('Y-m-d H:i:s',strtotime($channel['create_time']) + 60 * 10);
            $tmp  = $sdk->getChannelById($startTime,$endTime,$channel['cp_channel_id']);
            $info = $tmp['list'];
            if(empty($info)) continue;
            $item = $info[0];
            $channel->name = $item['channel_name'];
            $channel->extends = [
                'hap_url'   => $item['hap_url'],
                'h5_url'    => $item['h5_url'],
                'http_url'  => $item['http_url'],
                'apk_url'   => $item['apk_url'],
            ];
            $channel->save();
        }
    }
}

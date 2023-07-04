<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Qr\QrSdk;
use App\Common\Tools\CustomException;
use App\Services\BookService;
use App\Services\ChapterService;
use App\Services\ProductService;


class QrWeChatMiniProgramChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::QR;
    }



    public function getType(): string
    {
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }


    protected function getSdk($product): QrSdk
    {
        $cpAccount = $product['cp_account'];
        list($hostId,$appid) = explode('#',$cpAccount['account']);
        return new QrSdk($hostId,$appid,$cpAccount['cp_secret']);
    }


    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取
        $data = array();
        $sdk = $this->getSdk($product);

        $bookService = new BookService();
        $chapterService = new ChapterService();

        $startTime = $date.' 00:00:00';
        $endTime = $date.' 23:59:59';

        $productList = (new ProductService())->get([
            'cp_type'=> CpTypeEnums::QR,
        ]);
        $productMap = array_column($productList->toArray(),null,'cp_product_alias');
        try {
            $channels = $sdk->getChannelList($startTime,$endTime);

            if($channels['total'] <= 0){
                return [];
            }
            foreach ($channels['promotionList'] as $item){
                $tmpProduct = $productMap[$item['pack_appid']];
                // 书籍
                $book['id'] = 0;

                $book = $bookService->readSave([
                    'cp_book_id' => $item['video_id'],
                    'name'       => $item['video_name'],
                    'cp_type'    => $tmpProduct['cp_type']
                ]);

                // 打开章节
                $chapter = $chapterService->readSave($book['id'],$item['chapter_id'],$item['chapter_title'],$item['chapter_order']);


                $data[] = [
                    'product_id'    => $tmpProduct['id'],
                    'cp_channel_id' => $item['promotion_id'],
                    'name'          => $item['promotion_name'],
                    'book_id'       => $book['id'],
                    'chapter_id'    => $chapter['id'] ?? 0,
                    'force_chapter_id' => $forceChapter['id'] ?? 0,
                    'extends'       => [
                        'h5_url'    => $item['promotion_wechat_redirect_url'],
                    ],
                    'create_time' => $item['create_time'],
                    'updated_time' => $item['create_time'],
                ];
            }

        } catch (CustomException $e) {
            $errInfo = $e->getErrorInfo(true);
            // 请求接口过频 异常过滤
            if($errInfo['data']['result']['code'] != '30001'){
                throw $e;
            }
        }



        return $data;
    }
}

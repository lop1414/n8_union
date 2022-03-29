<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Sdks\Tw\TwSdk;
use App\Services\BookService;
use App\Services\ChapterService;


class TwKyyChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::TW;
    }



    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();
        $sdk = new TwSdk($product['cp_product_alias'],$product['cp_secret']);
        $bookService = new BookService();
        $chapterService = new ChapterService();

        $para = [
            'time'  => TIMESTAMP,
            'adate' => date('Ymd',strtotime($date))
        ];
        $channels = $sdk->getCpChannel($para);

        foreach ($channels as $channel){
            // 书籍
            $book = $bookService->readSave([
                'cp_book_id' => $channel['bid'],
                'name'       => $channel['book_name'],
                'cp_type'    => $product['cp_type']
            ]);
            // 打开章节
            $openChapter = $chapterService->readSave($book['id'],0,$data['num_name'],$data['num']);

            //强制章节
            $installChapter = $chapterService->readSave($book['id'],0,$data['follow_num_name'],$data['follow_num']);


            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'],
                'chapter_id'     => $openChapter['id'],
                'force_chapter_id'  => $installChapter['id'],
                'extends'       => [
                    'hap_url'   => $channel['hap_links'],
                    'h5_url'    => $channel['links']
                ],
                'create_time'    => $channel['created_at'],
                'updated_time'   => $channel['created_at'],
            ];
        }

        return $data;
    }
}

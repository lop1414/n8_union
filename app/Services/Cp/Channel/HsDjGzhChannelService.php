<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Hs\HsSdk;
use App\Services\BookService;
use App\Services\ChapterService;


class HsDjGzhChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::HS;
    }



    public function getType(): string
    {
        return ProductTypeEnums::DJ_GZH;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取
        $data = array();
        $cpAccount =  $product->cp_account;

        list($apiKey,$apiSecurity) = explode('#',$cpAccount['cp_secret']);
        $sdk = new HsSdk($cpAccount['account'],$apiKey,$apiSecurity);

        $bookService = new BookService();
        $chapterService = new ChapterService();

        $param = [
            'applet_id' => $product->extends['applet_id'],
            'show_id' => $product->extends['show_id'],
            'channel_id' => $product->cp_product_alias,
            'start' => $date,
            'end' => $date,
        ];

        $channels = $sdk->getChannels($param);


        foreach ($channels['data'] as $item){
            // 书籍
            $book['id'] = 0;

            $book = $bookService->readSave([
                'cp_book_id' => $item['book_id'],
                'name'       => $item['book_name'],
                'cp_type'    => $product['cp_type']
            ]);

            // 打开章节
            $chapter = $chapterService->readSave($book['id'],$item['chapter_id'],$item['chapter_name'],0);

            //强制章节
//            $forceChapter = $chapterService->readSave($book['id'],$item['force_follow_chapter'],'',$item['force_follow_chapter_order']);

            $data[] = [
                'product_id'    => $product['id'],
                'cp_channel_id' => $item['spread_id'],
                'name'          => $item['spread_name'],
                'book_id'       => $book['id'],
                'chapter_id'    => $chapter['id'] ?? 0,
                'force_chapter_id' => $forceChapter['id'] ?? 0,
                'extends'       => [
                    'h5_url'    => $item['url_out'],
                    'page_path' => $item['url']
                ],
                'create_time' => $item['created_at'],
                'updated_time' => $item['updated_at'],
            ];
        }

        return $data;
    }
}

<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\TwH5\TwH5Sdk;
use App\Common\Tools\CustomException;
use App\Services\BookService;
use App\Services\ChapterService;


class TwH5ChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::TW;
    }



    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();
        $sdk = new TwH5Sdk($product['cp_product_alias'],$product['cp_secret']);
        $bookService = new BookService();
        $chapterService = new ChapterService();

        $para = [
            'time'  => TIMESTAMP
        ];
        $channels = $sdk->getChannel($para);

        foreach ($channels as $channel){
            // 书籍
            $book = $bookService->readSave([
                'cp_book_id' => $channel['book_id'],
                'name'       => $channel['book_name'],
                'cp_type'    => $product['cp_type']
            ]);
            // 打开章节
            $openChapter = $chapterService->readSave($book['id'],intval($channel['book_id'].$channel['number']),$channel['chapter_name'],$channel['number']);

            //强制章节
            $installChapter = $chapterService->readSave($book['id'],intval($channel['book_id'].$channel['follow_number']),$channel['follow_chapter_name'],$channel['follow_number']);


            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'],
                'chapter_id'     => $openChapter['id'],
                'force_chapter_id'  => $installChapter['id'],
                'extends'       => [
                    'h5_url'    => $channel['url']
                ],
                'create_time'    => $channel['create_time'],
                'updated_time'   => $channel['create_time'],
            ];
        }

        return $data;
    }

    public function create($product, $name, $book, $chapter,$forceChapter): string
    {
        throw new CustomException([
            'code'       => 'NOT_CAN_CREATE_CHANNEL',
            'message'    => "该小说平台暂不支持"
        ]);
    }
}

<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\TwApp\TwAppSdk;
use App\Services\BookService;


class TwAppChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::TW;
    }



    public function getType(): string
    {
        return ProductTypeEnums::APP;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $data = array();
        $sdk = new TwAppSdk($product['cp_product_alias'],$product['cp_secret']);
        $bookService = new BookService();


        $channels = $sdk->getChannel();
;
        foreach ($channels as $channel){
            // 书籍
            $book = $bookService->readSave([
                'cp_book_id' => $channel['bid'],
                'name'       => $channel['book_name'],
                'cp_type'    => $product['cp_type']
            ]);

            $data[] = [
                'product_id'     => $product['id'],
                'cp_channel_id'  => $channel['id'],
                'name'           => $channel['name'],
                'book_id'        => $book['id'],
                'chapter_id'     => 0,
                'force_chapter_id'  => 0,
                'extends'        => [],
                'create_time'    => null,
                'updated_time'   => null,
            ];
        }

        return $data;
    }
}

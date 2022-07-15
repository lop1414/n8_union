<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Mb\MbSdk;
use App\Common\Tools\CustomException;
use App\Datas\BookData;


class MbDyMiniProgramChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::MB;
    }



    public function getType(): string
    {
        return ProductTypeEnums::DY_MINI_PROGRAM;
    }



    public function get($product, $date, $cpId): array
    {

        $sdk = new MbSdk($product['cp_account']['account'],$product['cp_product_alias'],$product['cp_account']['cp_secret']);

        $bookData = new BookData();

        $page = 1;

        $data = array();

        do{
            $channels = $sdk->getChannels($date.' 00:00:00',$date.' 23:59:59',$page);

            foreach ($channels['items'] as $channel){

                // 书籍
                $book = $bookData->save([
                    'cp_type'       => $product['cp_type'],
                    'cp_book_id'    => $channel['contentId'],
                    'name'          => $channel['contentName'],
                    'author_name'   => '',
                    'all_words'     => 0,
                    'update_time'   => null
                ]);



                //渠道
                $data[] = [
                    'product_id'     => $product['id'],
                    'cp_channel_id'  => $channel['promotionId'],
                    'name'           => $channel['name'],
                    'book_id'        => $book['id'],
                    'chapter_id'     => 0,
                    'force_chapter_id'   => 0,
                    'extends'        => [
                        'hap_url'   => $channel['hap'],
                        'h5_url'    => $channel['h5'],
                        'http_url'  => $channel['httpsHap'],
                        'page_path' => $channel['pagePath']
                    ],
                    'create_time'    => $channel['createDate'],
                    'updated_time'   => $channel['createDate'],
                ];
            }

            $page += 1;
        }while($channels['totalPages'] >= $page);

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

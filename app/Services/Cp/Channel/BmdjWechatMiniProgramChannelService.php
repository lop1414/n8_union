<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Bmdj\BmdjSdk;
use App\Datas\BookData;
use App\Datas\ChapterData;


class BmdjWechatMiniProgramChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::BMDJ;
    }



    public function getType(): string
    {
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取
        $sdk = new BmdjSdk($product['cp_account']['account'],$product['cp_account']['cp_secret'],$product['cp_product_alias']);
        $bookData = new BookData();
        $chapterData = new ChapterData();

        $page = 1;

        $data = array();
        $startDateTime = $date.' 00:00:00';
        $endDateTime = $date.' 23:59:59';
        do{
            $channels = $sdk->getChannel($startDateTime,$endDateTime,$page);

            foreach ($channels['list'] as $channel){

                // 书籍
                $book = $bookData->save([
                    'cp_type'       => $product['cp_type'],
                    'cp_book_id'    => $channel['seriesid'],
                    'name'          => $channel['seriesName'],
                    'author_name'   => '',
                    'all_words'     => 0,
                    'update_time'   => null
                ]);
                // 打开章节
                $openChapter = $chapterData->save([
                    'book_id'       => $book['id'],
                    'cp_chapter_id' => $channel['openEpisodeid'],
                    'name'          => $channel['openEpisodeName'],
                    'seq'           => $channel['openEpisodeNumber']
                ]);


                //渠道
                $data[] = [
                    'product_id'     => $product['id'],
                    'cp_channel_id'  => $channel['channelid'],
                    'name'           => $channel['channelName'],
                    'book_id'        => $book['id'],
                    'chapter_id'     => $openChapter['id'],
                    'force_chapter_id'   => 0,
                    'extends'        => [
                        'h5_url'    => $channel['webLink'],
                    ],
                    'create_time'    => date('Y-m-d H:i:s',$channel['createTime']),
                    'updated_time'   => date('Y-m-d H:i:s',$channel['updateTime']),
                ];
            }

            $page += 1;
        }while($channels['totalPage'] > $channels['page']);

        return $data;
    }
}

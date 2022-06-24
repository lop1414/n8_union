<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
use App\Datas\ChapterData;
use App\Common\Sdks\Bm\BmSdk;


class BmKyyChannelService implements CpChannelInterface
{


    public function getCpType(): string
    {
        return CpTypeEnums::BM;
    }



    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }



    public function get($product, $date, $cpId): array
    {
        //不支持cp id 获取

        $sdk = new BmSdk($product['cp_product_alias'],$product['cp_secret']);
        $bookData = new BookData();
        $chapterData = new ChapterData();

        $para = [
            'page'  => 1,
            'createTimeStart' => strtotime($date.' 00:00:00'),
            'createTimeEnd' => strtotime($date.' 23:59:59'),
        ];

        $data = array();

        do{
            $channels = $sdk->getChannel($para);

            foreach ($channels['list'] as $channel){

                // 书籍
                $book = $bookData->save([
                    'cp_type'       => $product['cp_type'],
                    'cp_book_id'    => $channel['novelid'],
                    'name'          => $channel['novelName'],
                    'author_name'   => '',
                    'all_words'     => 0,
                    'update_time'   => null
                ]);
                // 打开章节
                $openChapter = $chapterData->save([
                    'book_id'       => $book['id'],
                    'cp_chapter_id' => $channel['openChapterid'],
                    'name'          => $channel['openChapterName'],
                    'seq'           => $channel['openChapterNumber']
                ]);
                //强制章节
                $installChapter = $chapterData->save([
                    'book_id'       => $book['id'],
                    'cp_chapter_id' => $channel['installChapterid'],
                    'name'          => $channel['installChapterName'],
                    'seq'           => $channel['installChapterNumber']
                ]);


                //渠道
                $data[] = [
                    'product_id'     => $product['id'],
                    'cp_channel_id'  => $channel['channelid'],
                    'name'           => $channel['channelName'],
                    'book_id'        => $book['id'],
                    'chapter_id'     => $openChapter['id'],
                    'force_chapter_id'   => $installChapter['id'],
                    'extends'        => [
                        'hap_url'   => $channel['hapLink'],
                        'h5_url'    => $channel['webLink'],
                    ],
                    'create_time'    => $channel['createTime'],
                    'updated_time'   => $channel['updateTime'],
                ];
            }

            $para['page'] += 1;
        }while($channels['totalPage'] >= $para['page']);

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

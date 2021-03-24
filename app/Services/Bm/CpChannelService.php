<?php

namespace App\Services\Bm;


use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
use App\Datas\ChapterData;
use App\Datas\CpChannelData;
use App\Models\CpChannelModel;
use App\Sdks\Bm\BmSdk;


class CpChannelService extends BmService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new CpChannelModel());
    }


    public function sync($startDate,$endDate){

        $productList = $this->getProductList();

        $bookData = new BookData();
        $chapterData = new ChapterData();
        $cpChannelData = new CpChannelData();
        foreach ($productList as $product){
            $sdk = new BmSdk($product['cp_product_alias'],$product['cp_secret']);

            $channels = $sdk->getCpChannel([
                'page'      => 1,
                'createTimeStart' => strtotime($startDate.' 00:00:00'),
                'createTimeEnd'   => strtotime($endDate.' 23:59:59')
            ]);

            foreach ($channels['list'] as $i => $channel){

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
                $cpChannelData->save([
                    'product_id'     => $product['id'],
                    'cp_channel_id'  => $channel['channelid'],
                    'name'           => $channel['channelName'],
                    'book_id'        => $book['id'],
                    'chapter_id'     => $openChapter['id'],
                    'force_chapter_id'   => $installChapter['id'],
                    'create_time'    => $channel['createTime'],
                    'updated_time'   => $channel['updateTime'],
                ]);

            }
        }
    }


    public function saveData($data,$product){


        $this->model->updateOrCreate(
            [
                'cp_type'    => $product['cp_type'],
                'cp_book_id' => $data['cbid']
            ],
            [
                'name'       => $data['title'],
                'author_name'   => $data['author_name'],
                'all_words'     => $data['all_words'],
                'update_time'   => $data['update_time']
            ]
        );
    }
}

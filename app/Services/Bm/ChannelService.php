<?php

namespace App\Services\Bm;


use App\Datas\BookData;
use App\Datas\ChannelData;
use App\Datas\ChapterData;
use App\Models\ChannelModel;
use App\Sdks\Bm\BmSdk;


class ChannelService extends BmService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new ChannelModel());
    }


    public function sync($startDate,$endDate){

        $productList = $this->getProductList();

        $bookData = new BookData();
        $chapterData = new ChapterData();
        $channelData = new ChannelData();
        foreach ($productList as $product){
            $sdk = new BmSdk($product['cp_product_alias'],$product['cp_secret']);

            $parameter = [
                'page'  => 1
            ];
            if(!empty($startDate) && !empty($endDate)){
                $parameter['createTimeStart'] = strtotime($startDate.' 00:00:00');
                $parameter['createTimeEnd'] = strtotime($endDate.' 00:00:00');
            }

            do{
                $channels = $sdk->getCpChannel($parameter);

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
                    $channelData->save([
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

                $parameter['page'] += 1;
            }while($channels['totalPage'] >= $parameter['page']);
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

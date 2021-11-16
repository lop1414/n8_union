<?php

namespace App\Services\Cp\Channel;

use App\Common\Enums\CpTypeEnums;
use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
use App\Datas\ChannelData;
use App\Datas\ChapterData;
use App\Sdks\Tw\TwSdk;


class TwChannelService extends CpChannelBaseService
{

    public $bookData,$chapterData,$channelData;
    protected $cpType = CpTypeEnums::TW;

    public function __construct(){
        parent::__construct();
        $this->bookData = new BookData();
        $this->chapterData = new ChapterData();
        $this->channelData = new ChannelData();
    }

    /**
     * @throws CustomException
     * 同步
     */
    public function sync(){
        $startDate = $this->getParam('start_date');
        $endDate = $this->getParam('end_date');

        $productList = $this->getProductList();


        foreach ($productList as $product){
            $sdk = new TwSdk($product['cp_product_alias'],$product['cp_secret']);

            $parameter = [
                'time'  => TIMESTAMP,
            ];

            $date = $startDate = date('Ymd',strtotime($startDate));
            $endDate = date('Ymd',strtotime($endDate));

            do{
                try {

                    $parameter['adate'] = $date;

                    $channels = $sdk->getCpChannel($parameter);

                    foreach ($channels as $channel){

                     $this->saveItem($product,$channel);

                    }
                }catch (CustomException $e){

                    //日志
                    (new ErrorLogService())->catch($e);


                    // echo
                    (new ConsoleEchoService())->error("自定义异常 {code:{$e->getCode()},msg:{$e->getMessage()}}");
                }catch (\Exception $e){

                    //日志
                    (new ErrorLogService())->catch($e);

                    // echo
                    (new ConsoleEchoService())->error("异常 {code:{$e->getCode()},msg:{$e->getMessage()}}");
                }
                $date = date('Ymd',strtotime('+1 day',strtotime($date)));

            }while($date <= $endDate);

        }
    }



    /**
     * @param $product
     * @param $data
     * 保存
     */
    public function saveItem($product,$data){
        // 书籍
        $book = $this->bookData->save([
            'cp_type'       => $product['cp_type'],
            'cp_book_id'    => $data['bid'],
            'name'          => $data['book_name'],
            'author_name'   => '',
            'all_words'     => 0,
            'update_time'   => null
        ]);
        // 打开章节
        $openChapter = $this->chapterData->save([
            'book_id'       => $book['id'],
            'cp_chapter_id' => 0,
            'name'          => $data['num_name'] ?? '',
            'seq'           => $data['num']
        ]);
        //强制章节
        $installChapter = $this->chapterData->save([
            'book_id'       => $book['id'],
            'cp_chapter_id' => 0,
            'name'          => $data['follow_num_name'] ?? '',
            'seq'           => $data['follow_num']
        ]);
        //渠道
        $this->channelData->save([
            'product_id'     => $product['id'],
            'cp_channel_id'  => $data['id'],
            'name'           => $data['name'],
            'book_id'        => $book['id'],
            'chapter_id'     => $openChapter['id'],
            'force_chapter_id'  => $installChapter['id'],
            'create_time'    => $data['created_at'],
            'updated_time'   => $data['created_at'],
        ]);
    }





    public function syncById(){
        throw new CustomException([
            'code' => 'NO_SUPPORT',
            'message' => '该平台不支持根据ID更新',
        ]);
    }
}

<?php

namespace App\Services\Yw;


use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Models\BookModel;
use App\Sdks\Yw\YwSdk;

class BookService extends YwService
{

    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

        $this->setModel(new BookModel());
    }


    public function syncBook(){

        $productList = $this->getProductList();

        $consoleEchoService = new ConsoleEchoService();

        foreach ($productList as $product){
            $consoleEchoService->echo($product['name']);
            $sdk = new YwSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);
            $cpBookIds = $sdk->getBookIds();

            $count = count($cpBookIds);
            foreach ($cpBookIds as $i => $cpBookId){
                $consoleEchoService->incrOffset();
                try{
                    $consoleEchoService->progress($count,$i,$cpBookId);

                    $info = $sdk->getBookInfo($cpBookId);
                    $this->saveData($info,$product);
                }catch (CustomException $e){

                    // 日志
                    (new ErrorLogService())->catch($e);

                    // 提示
                    $msg = json_decode($e->getMessage(),true);
                    $consoleEchoService->incrOffset();
                    $consoleEchoService->error($msg['data']['result']['msg'] ?? $msg['message']);
                    $consoleEchoService->decrOffset();
                }
                $consoleEchoService->decrOffset();
            }
        }
        $consoleEchoService->echo('');
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

<?php

namespace App\Services\Yw;


use App\Common\Enums\CpTypeEnums;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
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




    public function read($cpBookId){
        $info = $this->model
            ->where('cp_type',CpTypeEnums::YW)
            ->where('cp_book_id',$cpBookId)
            ->first();
        if(empty($info)){
            $info = $this->sync($cpBookId);
        }
        return $info;
    }


    /**
     * @param $cpBookId
     * @return mixed
     */
    public function sync($cpBookId){

        $bookData = new BookData();

        $sdk = new YwSdk($this->product['cp_product_alias'],$this->product['cp_account']['account'],$this->product['cp_account']['cp_secret']);

        $info = $sdk->getBookInfo($cpBookId);

        return $bookData->save([
            'cp_type'       => $this->product['cp_type'],
            'cp_book_id'    => $info['cbid'],
            'name'          => $info['title'],
            'author_name'   => $info['author_name'],
            'all_words'     => $info['all_words'],
            'update_time'   => $info['update_time']
        ]);
    }


    public function updateAll(){
        $lastMinId = 0;
        do{
            $list = (new BookModel())
                ->where('cp_type',CpTypeEnums::YW)
                ->where('id','>',$lastMinId)
                ->skip(0)
                ->take(1000)
                ->orderBy('id')
                ->get();
            foreach ($list as $item){
                try{
                    $this->sync($item['cp_book_id']);

                }catch (CustomException $e){

                    $errInfo = $e->getErrorInfo(true);

                    //10010 小说不存在
                    if(isset($errInfo['data']['result']['code']) && $errInfo['data']['result']['code'] != '10010'){
                        (new ErrorLogService())->catch($e);
                    }
                    echo $errInfo['message']."\n";

                }catch (\Exception $e){
                    (new ErrorLogService())->catch($e);
                    echo $e->getMessage()."\n";

                }

                $lastMinId = $item['id'];
            }
        }while(!$list->isEmpty());

    }
}

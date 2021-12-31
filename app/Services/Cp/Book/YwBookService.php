<?php

namespace App\Services\Cp\Book;


use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Sdks\Yw\YwSdk;


class YwBookService extends AbstractCpBookService
{
    protected $cpType = CpTypeEnums::YW;


    /**
     * @return array
     * @throws CustomException
     */
    public function sync(){

        $cpBookId = $this->getParam('cp_book_id');
        $this->checkProduct();
        $ywSdk = new YwSdk($this->product['cp_product_alias'],$this->product['cp_account']['account'],$this->product['cp_account']['cp_secret']);
        $info = $ywSdk->getBookInfo($cpBookId);

        return $this->bookModelData->save([
            'cp_type'       => $this->product['cp_type'],
            'cp_book_id'    => $info['cbid'],
            'name'          => $info['title'],
            'author_name'   => $info['author_name'],
            'all_words'     => $info['all_words'],
            'update_time'   => $info['update_time']
        ]);
    }


}

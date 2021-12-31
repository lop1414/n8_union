<?php

namespace App\Services\Cp\Book;


use App\Common\Enums\CpTypeEnums;
use App\Common\Tools\CustomException;
use App\Sdks\Fq\FqSdk;


class FqBookService extends AbstractCpBookService
{
    protected $cpType = CpTypeEnums::FQ;


    /**
     * @return array
     * @throws CustomException
     */
    public function sync(){

        $cpBookId = $this->getParam('cp_book_id');
        $this->checkProduct();
        $fqSdk = new FqSdk($this->product['cp_product_alias'],$this->product['cp_secret']);
        $tmp = $fqSdk->getBookInfo($cpBookId);

        if(empty($tmp['result'])) return [];

        $info = $tmp['result'][0];
        return $this->bookModelData->save([
            'cp_type'       => $this->product['cp_type'],
            'cp_book_id'    => $info['book_id'],
            'name'          => $info['book_name'],
            'author_name'   => $info['author'],
            'all_words'     => $info['word_count'],
            'update_time'   => null
        ]);
    }


}

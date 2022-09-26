<?php

namespace App\Services\Cp\Book;


use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;
use App\Common\Sdks\Fq\FqSdk;

class FqBookService implements CpBookInterface
{

    public function getCpType(): string
    {
        return CpTypeEnums::FQ;
    }

    public function read(ProductModel $product,string $cpId): array
    {

        $sdk = new FqSdk($product['cp_account']['account'],$product['cp_account']['cp_secret']);

        $res = $sdk->getBookInfo($cpId);
        $data = $res['result'][0];
        $info = [
            'cp_type'       => $product['cp_type'],
            'cp_book_id'    => $data['book_id'],
            'name'          => $data['book_name'],
            'author_name'   => $data['author'],
            'all_words'     => $data['word_count'],
            'update_time'   => null
        ];

        return $info;
    }
}

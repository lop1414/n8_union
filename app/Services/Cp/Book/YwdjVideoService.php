<?php

namespace App\Services\Cp\Book;


use App\Common\Enums\CpTypeEnums;
use App\Common\Sdks\YwFx\YwFxSdk;
use App\Models\ProductModel;


class YwdjVideoService implements CpBookInterface
{

    public function getCpType(): string
    {
        return CpTypeEnums::YWDJ;
    }


    public function read(ProductModel $product,string $cpId): array
    {
        $sdk = new YwFxSdk($product['cp_product_alias'],$product['cp_account']['account'],$product['cp_account']['cp_secret']);

        $data = $sdk->getVideoInfo($cpId);
        return [
            'cp_type'       => $product['cp_type'],
            'cp_book_id'    => $data['video_id'],
            'name'          => $data['video_name'],
            'author_name'   => $data['author_name'],
            'all_words'     => 0,
            'update_time'   => $data['update_time']
        ];
    }
}

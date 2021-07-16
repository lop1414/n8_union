<?php

namespace App\Console\Commands\Yw;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Datas\ProductData;
use App\Models\ProductModel;
use App\Services\Check\ChannelClaimService;
use App\Services\Yw\BookService;

class UpdateBookCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'yw:update_book';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '阅文更新书籍信息';

    protected $consoleEchoService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }



    public function handle(){

        $this->lockRun(function (){
            $productInfo = (new ProductModel())
                ->where('cp_type',CpTypeEnums::YW)
                ->where('type',ProductTypeEnums::KYY)
                ->orderBy('cp_product_alias')
                ->first();
            (new BookService())
                ->setProduct($productInfo)
                ->updateAll();
        },'yw:update_book', 60*60,['log' => true]);
    }


}

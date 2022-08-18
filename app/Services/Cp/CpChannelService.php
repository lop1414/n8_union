<?php

namespace App\Services\Cp;


use App\Datas\ChannelData;
use App\Models\BookModel;
use App\Models\ChapterModel;
use App\Services\Cp\Channel\BmKyyChannelService;
use App\Services\Cp\Channel\FqKyyChannelService;
use App\Services\Cp\Channel\MbDyMiniProgramChannelService;
use App\Services\Cp\Channel\MbWeChatMiniProgramChannelService;
use App\Services\Cp\Channel\QyH5ChannelService;
use App\Services\Cp\Channel\TwAppChannelService;
use App\Services\Cp\Channel\TwH5ChannelService;
use App\Services\Cp\Channel\TwKyyChannelService;
use App\Services\Cp\Channel\YwH5ChannelService;
use App\Services\Cp\Channel\YwKyyChannelService;
use App\Services\Cp\Channel\ZyKyyChannelService;
use App\Services\ProductService;
use App\Services\Cp\Channel\CpChannelInterface;

class CpChannelService
{

    private $param;

    private $service;

    private $modelData;


    public function __construct(CpChannelInterface $service)
    {
        $this->service = $service;
        $this->modelData = new ChannelData();
    }


    /**
     * @return string[]
     * 获取书城渠道服务列表
     */
    static public function getServices(): array
    {
        return [
            YwKyyChannelService::class,
            YwH5ChannelService::class,
            TwAppChannelService::class,
            BmKyyChannelService::class,
            FqKyyChannelService::class,
            QyH5ChannelService::class,
            TwKyyChannelService::class,
            TwH5ChannelService::class,
            ZyKyyChannelService::class,
            MbDyMiniProgramChannelService::class,
            MbWeChatMiniProgramChannelService::class,
        ];
    }


    public function getParam($key)
    {
        if(empty($this->param[$key])){
            return null;
        }
        return $this->param[$key];
    }


    public function setParam($key,$data)
    {
        $this->param[$key] = $data;
    }


    public function __call($name, $arguments)
    {
        return $this->service->$name(...$arguments);
    }


    public function sync()
    {
        $productIds =  $this->getParam('product_ids');
        $where = $productIds
            ? ['product_ids' => $this->getParam('product_ids')]
            : ['cp_type'   => $this->service->getCpType(),'type'=> $this->service->getType()];

        $productList = ProductService::get($where);

        $startDate = $this->getParam('start_date');
        $endDate = $this->getParam('end_date');
        $cpId = $this->getParam('cp_id');

        foreach ($productList as $product){
            $date = $startDate;
            do{
                $data = $this->service->get($product,$date,$cpId);

                if(!empty($data)){
                    foreach ($data as $item){
                        $this->modelData->save($item);
                    }
                }
                $date = date('Y-m-d', strtotime('+1 day',strtotime($date)));
            }while($date <= $endDate);
        }
    }


    /**
     * @param string $name
     * @param BookModel $book
     * @param ChapterModel $chapter
     * @param ChapterModel|null $forceChapter
     * @return string
     * 创建渠道
     */
    public function create(string $name, BookModel $book, ChapterModel $chapter, ?ChapterModel $forceChapter): string
    {

        $products = ProductService::get(['product_id' =>$this->getParam('product_id')]);
        $product = $products[0];

        $cpChannelId = $this->service->create($product,$name,$book,$chapter,$forceChapter);
        return $cpChannelId;
    }
}

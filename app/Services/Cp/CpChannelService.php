<?php

namespace App\Services\Cp;

use App\Models\BookModel;
use App\Models\ChapterModel;
use App\Models\CpAdminAccountModel;
use App\Services\Cp\Channel\BmKyyChannelService;
use App\Services\Cp\Channel\FqKyyChannelService;
use App\Services\Cp\Channel\HsDjGzhChannelService;
use App\Services\Cp\Channel\MbDyMiniProgramChannelService;
use App\Services\Cp\Channel\MbWeChatMiniProgramChannelService;
use App\Services\Cp\Channel\QyH5ChannelService;
use App\Services\Cp\Channel\TwAppChannelService;
use App\Services\Cp\Channel\TwH5ChannelService;
use App\Services\Cp\Channel\TwKyyChannelService;
use App\Services\Cp\Channel\YwH5ChannelService;
use App\Services\Cp\Channel\YwKyyChannelService;
use App\Services\Cp\Channel\ZyH5ChannelService;
use App\Services\Cp\Channel\ZyKyyChannelService;
use App\Services\ProductService;
use App\Services\Cp\Channel\CpChannelInterface;

class CpChannelService
{

    private $param;

    private $service;


    public function __construct(CpChannelInterface $service)
    {
        $this->service = $service;
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
            ZyH5ChannelService::class,
            ZyKyyChannelService::class,
            MbDyMiniProgramChannelService::class,
            MbWeChatMiniProgramChannelService::class,
            HsDjGzhChannelService::class
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


    /**
     * 获取接口数据
     * @return array
     */
    public function getByApi(): array
    {
        $productIds =  $this->getParam('product_ids');
        $where = $productIds
            ? ['product_ids' => $this->getParam('product_ids')]
            : ['cp_type'   => $this->service->getCpType(),'type'=> $this->service->getType()];

        $productList = ProductService::get($where);

        $startDate = $this->getParam('start_date');
        $endDate = $this->getParam('end_date');
        $cpId = $this->getParam('cp_id');

        $data = [];
        foreach ($productList as $product){
            $date = $startDate;
            do{
                $items = $this->service->get($product,$date,$cpId);
                $data = array_merge($data,$items);

                $date = date('Y-m-d', strtotime('+1 day',strtotime($date)));
            }while($date <= $endDate);
        }
        return $data;
    }


    /**
     * @param string $name
     * @param BookModel $book
     * @param ChapterModel $chapter
     * @param ChapterModel|null $forceChapter
     * @return string
     * 创建渠道
     */
    public function create(string $name, BookModel $book, ChapterModel $chapter, ?ChapterModel $forceChapter, ?CpAdminAccountModel $cpAdminAccount): string
    {

        $products = ProductService::get(['product_id' =>$this->getParam('product_id')]);
        $product = $products[0];

        return $this->service->create($product,$name,$book,$chapter,$forceChapter,$cpAdminAccount);
    }

    /**
     * 是否可以创建渠道
     * @return bool
     */
    public function isCanApiCreate(): bool
    {
        if(!method_exists($this->service,'create')){
            return false;
        }
        return true;
    }
}

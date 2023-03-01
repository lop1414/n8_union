<?php

namespace App\Services;



use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Datas\ProductData;
use App\Models\ProductModel;
use App\Services\Cp\CpProductService;
use App\Services\Cp\Product\CpProductInterface;
use Illuminate\Container\Container;


class ProductService extends BaseService
{
    static protected $productMap;

    static protected $productMapByAlias;

    private $modelData;


    public function __construct()
    {
        parent::__construct();
        $this->modelData = new ProductData();
    }


    static public function readToType($id){
        $product = self::read($id);
        return $product['type'];
    }


    static public function read($id){
        if(empty(self::$productMap[$id])){
            self::$productMap[$id] = (new ProductData())->setParams(['id' => $id])->read();
        }
        return self::$productMap[$id];
    }



    static public function readByAlias($alias,$cpType){
        $key = $cpType.'-'.$alias;
        if(empty(self::$productMapByAlias[$key])){
            self::$productMap[$key] = (new ProductData())
                ->setParams(['cp_product_alias'  => $alias, 'cp_type' => $cpType])
                ->read();
        }
        return self::$productMap[$key];
    }


    /**
     * @return mixed
     * 获取产品列表
     */
    static public function get(array $where)
    {

        $list = (new ProductModel())
            ->where('status',StatusEnum::ENABLE)
            ->when(isset($where['product_id']),function ($query) use ($where){
                return $query->where('id',$where['product_id']);
            })
            ->when(isset($where['product_ids']),function ($query) use ($where){
                return $query->whereIn('id',$where['product_ids']);
            })
            ->when(isset($where['cp_type']),function ($query) use ($where){
                return $query->where('cp_type',$where['cp_type']);
            })
            ->when(isset($where['type']),function ($query) use ($where){
                return $query->where('type',$where['type']);
            })
            ->get();

        return $list;
    }


    /**
     * @param array $data
     * @return array
     * 保存
     */
    public function save(array $data): array
    {
        $info = $this->modelData->save($data);
        return $info->toArray();
    }


    public function sync($param){
        $cpTypeParam = $param['cp_type'];
        if(!empty($cpTypeParam)){
            Functions::hasEnum(CpTypeEnums::class,$cpTypeParam);
        }
        $cpAccountId = $param['cp_account_id'] ?? 0;

        $container = Container::getInstance();

        $services = CpProductService::getServices();
        foreach ($services as $service){

            $container->bind(CpProductInterface::class,$service);
            $cpProductService = $container->make(CpProductService::class);

            $cpType = $cpProductService->getCpType();

            if(empty($cpTypeParam) || $cpTypeParam == $cpType){
                $cpProductService->setParam('cp_type',$cpTypeParam);
                $cpProductService->setParam('cp_account_id',$cpAccountId);
                $cpProductService->sync();
            }
        }
    }
}

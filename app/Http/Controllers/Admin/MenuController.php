<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\StatusEnum;
use App\Common\Services\SystemApi\CenterApiService;
use App\Common\Tools\CustomException;
use App\Enums\MenuLevelEnums;
use App\Models\MenuLevelModel;

class MenuController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new MenuLevelModel();

        parent::__construct();
    }

    public function getMenuList(){
        $centerApiService = new CenterApiService();
        $list = $centerApiService->apiGetMenu();
        return array_column($list,null,'id');
    }


    public function sync(){

        $list = $this->getMenuList();
        $tmp = $this->model->get();
        if($tmp->isEmpty()){
            $existIds = [];
        }else{
            $existIds = array_column($tmp->toArray(),'menu_id','menu_id');
        }

        foreach ($list as $menu){
            if(!in_array($menu['id'],$existIds)){
                $model = new MenuLevelModel();
                $model->menu_id = $menu['id'];
                $model->level = MenuLevelEnums::DEFAULT;
                $model->status = StatusEnum::ENABLE;
                $model->save();
            }
        }

        return $this->ret(true, $this->curdService->getModel());
    }


    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryAfter(function(){
            $menuMap = $this->getMenuList();

            foreach ($this->curdService->responseData['list'] as $item){
                $item->menu_info = $menuMap[$item->menu_id] ?? [];
            }
        });
    }

    /**
     * 列表预处理
     */
    public function getPrepare(){


        $this->curdService->getQueryBefore(function(){
            $level = MenuLevelEnums::DEFAULT;
            if(isset($this->curdService->requestData['cp_type']) && !empty($this->curdService->requestData['cp_type'])){
                $level = MenuLevelEnums::CP_TYPE;
            }

            if(isset($this->curdService->requestData['business']) && !empty($this->curdService->requestData['business'])){
                $level = MenuLevelEnums::BUSINESS;
            }

            if(isset($this->curdService->requestData['product_id']) && !empty($this->curdService->requestData['product_id'])){
                $level = MenuLevelEnums::PRODUCT;
            }

            $this->curdService->customBuilder(function ($builder) use ($level){
                $builder->whereIn('level',[MenuLevelEnums::COMMON,$level]);
            });

        });


        $this->curdService->getQueryAfter(function(){
            $menuMap = $this->getMenuList();

            foreach ($this->curdService->responseData as $item){
                $item->menu_info = $menuMap[$item->menu_id] ?? [];
            }
        });
    }


    /**
     * 详情预处理
     */
    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $menuMap = $this->getMenuList();

            $this->curdService->responseData->menu_info = $menuMap[$this->curdService->responseData->menu_id];
        });
    }


    /**
     * 保持验证规则
     */
    public function saveValidRule(){
        $this->curdService->addField('menu_id')->addValidRule('required');
        $this->curdService->addField('level')->addValidEnum(MenuLevelEnums::class);
        $this->curdService->addColumns(['menu_id']);
    }

    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){
            if($this->curdService->getModel()->uniqueExist([
                'menu_id' => $this->curdService->handleData['menu_id']
            ])){
                throw new CustomException([
                    'code' => 'DATA_EXIST',
                    'message' => '菜单设置已存在'
                ]);
            }
        });
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){
            if(
                (
                    $this->curdService->getModel()->menu_id != $this->curdService->handleData['menu_id']
                    || $this->curdService->getModel()->level != $this->curdService->handleData['level']
                )
                && $this->curdService->getModel()->uniqueExist([
                'menu_id' => $this->curdService->handleData['menu_id'],
                'level' => $this->curdService->handleData['level']
            ])){
                throw new CustomException([
                    'code' => 'DATA_EXIST',
                    'message' => '菜单设置已存在'
                ]);
            }
        });
    }
}

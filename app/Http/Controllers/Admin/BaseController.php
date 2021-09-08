<?php


namespace App\Http\Controllers\Admin;


use App\Common\Controllers\Admin\AdminController;
use App\Common\Helpers\Functions;
use App\Common\Services\SystemApi\CenterApiService;
use App\Datas\N8GlobalUserData;


class BaseController extends AdminController
{

    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'created_at';


    public $adminUser;


    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->adminUser = Functions::getGlobalData('admin_user_info');

    }


    /**
     * 有数据权限
     * @return bool
     */
    public function isDataAuth(){
        if($this->adminUser['is_admin']) return true;

        return false;
    }

    /**
     * @return array|mixed
     * 获取授权管理员ID
     */
    public function getPermissionAdminIds(){

        return $this->adminUser['permission_admin_ids'];
    }

    /**
     * @return string
     * 获取授权管理员ID
     */
    public function getPermissionAdminIdsStr(){
        return implode(',',$this->getPermissionAdminIds());
    }



    public function getAdminUserMap($filter = []){
        $adminUsers = (new CenterApiService())->apiGetAdminUsers($filter);
        $tmp = array_column($adminUsers,null,'id');
        // 兼容没有admin_id
        $tmp[0] = ['name' => ''];
        return $tmp;
    }



    /**
     * @param null $fn
     * 分页列表筛选 用户 公共处理
     */
    public function selectUserCommonFilter($fn = null){
        $this->curdService->addField('product_id')->addValidRule('required');

        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $builder->where('product_id',$this->curdService->requestData['product_id']);

                $openId = $this->curdService->requestData['open_id'] ?? '';
                if(!empty($openId)){
                    $globalUser = (new N8GlobalUserData())
                        ->setParams([
                            'product_id' => $this->curdService->requestData['product_id'],
                            'open_id'   => $openId
                        ])
                        ->read();
                    $n8Guid = !empty($globalUser) ? $globalUser['n8_guid'] : 0;
                    $builder->where('n8_guid',$n8Guid);

                }

                if(!empty($fn)){
                    $fn($builder);
                }
            });
        });
    }
}

<?php

namespace App\Services;


use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\SystemApi\CenterApiService;

class AdminUserService extends BaseService
{
    protected $adminUser;

    protected $adminUserMap = array();

    public function __construct()
    {
        parent::__construct();
        $this->adminUser = Functions::getGlobalData('admin_user_info');
    }

    /**
     * @return bool
     * 是否为管理员
     */
    public function isAdmin(): bool
    {
        if($this->adminUser['is_admin']) return true;
        return false;
    }

    /**
     * @return bool
     * 是否为书城人员
     */
    public function isCp(): bool
    {
        if($this->adminUser['is_cp']) return true;
        return false;
    }

    /**
     * @return array
     * 下属管理员ID
     */
    public function getChildrenAdminIds(): array
    {
        return $this->adminUser['children_admin_ids'];
    }

    /**
     * @return array
     * 获取有权限的管理员ID
     */
    public function getHasAuthAdminIds(): array
    {
        $adminIds = array(
            $this->adminUser['admin_user']['id']
        );

        //市场助手
        if($this->isSupport()){
            $adminIds = array_merge($adminIds,$this->getGroupAdminIds());
        }

        //已授权
        $adminIds = array_merge($adminIds,$this->getPermissionAdminIds());

        return $adminIds;
    }

    /**
     * @return bool
     * 是否为助理
     */
    public function isSupport(): bool
    {
        if($this->adminUser['admin_user']['is_support']) return true;
        return false;
    }

    /**
     * @return array
     * 获取组内管理员ID
     */
    public function getGroupAdminIds(): array
    {
        return $this->adminUser['group_admin_ids'];
    }

    /**
     * @return array
     * 获取授权管理员ID
     */
    public function getPermissionAdminIds(): array
    {
        return $this->adminUser['permission_admin_ids'];
    }

    /**
     * @param int|null $adminId
     * @return string
     * @throws \App\Common\Tools\CustomException
     * 获取管理员名称
     */
    public function readName(int $adminId = null): string
    {
        $adminInfo = $this->read($adminId);
        return $adminInfo['name'];
    }

    /**
     * @param int|null $adminId
     * @return array
     * @throws \App\Common\Tools\CustomException
     * 获取管理员详情
     *  -- 默认获取当前登录的
     */
    public function read(int $adminId = null): array
    {
        if(is_null($adminId)){
            $adminId = $this->readId();
        }
        $adminUserMap =  $this->getAdminUserMap();
        return $adminUserMap[$adminId];
    }

    /**
     * @return int
     * 获取当前管理员ID
     */
    public function readId(): int
    {
        return $this->adminUser['admin_user']['id'];
    }

    /**
     * @param array $filter
     * @return array
     * @throws \App\Common\Tools\CustomException
     * 获取管理员映射数组
     */
    public function getAdminUserMap(array $filter = []): array
    {
        if(!$this->adminUserMap){
            $adminUsers = (new CenterApiService())->apiGetAdminUsers($filter);
            $this->adminUserMap = array_column($adminUsers,null,'id');
            // 兼容没有admin_id
            $this->adminUserMap[0] = ['name' => ''];
        }

        return $this->adminUserMap;
    }

}

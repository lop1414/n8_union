<?php

namespace App\Services;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\N8GlobalUserData;
use App\Datas\OpenUserData;
use App\Enums\UserSourceEnum;
use App\Services\Weixin\MiniProgram\WeixinMiniProgramAuthService;

class OpenUserService extends BaseService
{
    /**
     * @param $productId
     * @param $openId
     * @return |null
     * @throws CustomException
     * 获取全局用户
     */
    public function getGlobalUser($productId, $openId){
        $n8GlobalUserData = new N8GlobalUserData();
        $n8GlobalUser = $n8GlobalUserData->setParams([
            'open_id' => $openId,
            'product_id' => $productId,
        ])->read();
        return $n8GlobalUser;
    }

    /**
     * @param $n8Guid
     * @return |null
     * @throws CustomException
     * 按 guid 获取全局用户
     */
    public function getGlobalUserByGuid($n8Guid){
        $n8GlobalUserData = new N8GlobalUserData();
        $n8GlobalUser = $n8GlobalUserData->setParams([
            'n8_guid' => $n8Guid,
        ])->read();
        return $n8GlobalUser;
    }

    /**
     * @param $userSource
     * @param $appId
     * @param $openId
     * @return |null
     * @throws CustomException
     * 获取第三方用户
     */
    public function getOpenUser($userSource, $appId, $openId){
        $openUserData = new OpenUserData();
        $openUser = $openUserData->setParams([
            'user_source' => $userSource,
            'source_app_id' => $appId,
            'source_open_id' => $openId,
        ])->read();
        return $openUser;
    }

    /**
     * @param $param
     * @return string|null
     * @throws CustomException
     * 获取第三方 open_id
     */
    public function getSourceOpenId($param){
        $this->validRule($param, [
            'user_source' => 'required',
            'source_app_id' => 'present',
        ]);

        $sourceOpenId = null;
        if($param['user_source'] == UserSourceEnum::WEIXIN_MINI_PROGRAM){
            $this->validRule($param, [
                'js_code' => 'required',
            ]);

            $weixinMiniProgramAuthService = new WeixinMiniProgramAuthService();
            $weixinMiniProgramAuthService->setApp($param['source_app_id']);
            if(Functions::isLocal()){
                $sourceOpenId = "oZLu95TzGQPy6aP4KNiIBEUz_bHo";
            }else{
                $sourceOpenId = $weixinMiniProgramAuthService->getOpenIdByJsCode($param['js_code']);
            }
        }else{
            throw new CustomException([
                'code' => 'PLEASE_WRITE_GET_SOURCE_OPEN_ID_BY_USER_SOURCE_CODE',
                'message' => '请书写按用户来源获取第三方open_id代码',
                'log' => true,
                'data' => [
                    'user_source' => $param['user_source'],
                ],
            ]);
        }

        if(empty($sourceOpenId)){
            throw new CustomException([
                'code' => 'GET_SOURCE_OPEN_ID_FAIL',
                'message' => '获取来源openid失败',
            ]);
        }

        return $sourceOpenId;
    }

    /**
     * @param $param
     * @return mixed
     * @throws CustomException
     * 绑定
     */
    public function bind($param){
        // 验证
        $this->validRule($param, [
            'user_source' => 'required',
            'source_app_id' => 'present',
            'product_id' => 'required',
            'cp_open_id' => 'required',
        ]);
        Functions::hasEnum(UserSourceEnum::class, $param['user_source']);

        // 获取第三方 open_id
        $sourceOpenId = $this->getSourceOpenId($param);

        // 获取第三方用户
        $openUser = $this->getOpenUser($param['user_source'], $param['source_app_id'], $sourceOpenId);
        if(!empty($openUser)){
            throw new CustomException([
                'code' => 'OPEN_USER_EXIST',
                'message' => '用户ID已绑定',
            ]);
        }

        $n8GlobalUser = $this->getGlobalUser($param['product_id'], $param['cp_open_id']);
        if(empty($n8GlobalUser)){
            throw new CustomException([
                'code' => 'NOT_FOUND_GLOBAL_USER',
                'message' => '找不到全局用户信息',
            ]);
        }

        $openUserData = new OpenUserData();
        $ret = $openUserData->create(
            $param['user_source'],
            $param['source_app_id'],
            $sourceOpenId,
            $n8GlobalUser['n8_guid']
        );

        return $ret;
    }

    /**
     * @param $param
     * @return |null
     * @throws CustomException
     * 信息
     */
    public function info($param){
        // 验证
        $this->validRule($param, [
            'user_source' => 'required',
            'source_app_id' => 'present',
        ]);
        Functions::hasEnum(UserSourceEnum::class, $param['user_source']);

        // 获取第三方 open_id
        $sourceOpenId = $this->getSourceOpenId($param);

        // 获取第三方用户
        $openUser = $this->getOpenUser($param['user_source'], $param['source_app_id'], $sourceOpenId);

        // 获取全局用户
        $openUser['n8_global_user'] = $this->getGlobalUserByGuid($openUser['n8_guid']);
        $openUser['cp_open_id'] = $openUser['n8_global_user']['open_id'];

        return $openUser;
    }
}

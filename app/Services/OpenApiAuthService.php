<?php

namespace App\Services;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;

class OpenApiAuthService extends BaseService
{


    /**
     * 构建签名
     *
     * @param $param
     * @param $secret
     * @return string
     */
    public function makeSign($param,$secret){
        // sign字段不参与签名
        unset($param['sign']);

        // 按参数名字典排序
        ksort($param);

        // 参数拼接字符串
        $splicedString = '';
        foreach ($param as $paramKey => $paramValue) {
            $splicedString .= $paramKey . $paramValue;
        }

        // 签名
        return strtoupper(md5($secret. $splicedString));
    }


    /**
     * 验证
     *
     * @param $param
     * @param $secret
     * @return bool
     * @throws CustomException
     */
    public function valid($param,$secret){
        if(empty($param['timestamp']) || empty($param['sign'])){
            throw new CustomException([
                'code' => 'PARAM_MISSING',
                'message' => '参数缺失',
            ]);
        }

        // 是否调试
        $isDebug = Functions::isDebug();

        if(!$isDebug && TIMESTAMP - $param['timestamp'] > 300){
            throw new CustomException([
                'code' => 'TIMESTAMP_EXPIRED',
                'message' => '请求已失效',
            ]);
        }

        // 签名
        $sign = $this->makeSign($param,$secret);
        if(Functions::isProduction() && $sign != $param['sign']){
            $ret = [
                'code' => 'SIGN_ERROR',
                'message' => '签名错误',
                'log' => true,
            ];

            throw new CustomException($ret);
        }

        return true;
    }
}

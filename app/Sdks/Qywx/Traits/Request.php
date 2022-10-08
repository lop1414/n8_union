<?php

namespace App\Sdks\Qywx\Traits;

use App\Common\Tools\CustomException;

trait Request
{
    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return bool|mixed|string
     * @throws CustomException
     * 公共请求
     */
    public function publicRequest($url, $param = [], $method = 'GET', $header = [], $option = []){

        if(strtoupper($method) == 'GET'){
            $url .= '?'. http_build_query($param);
        }else{
            $header = array_merge([
                'Content-Type: application/json; charset=utf-8',
            ], $header);
            $param = json_encode($param);
        }

        $result = $this->curlRequest($url, $param, $method, $header, $option);

        $result = json_decode($result,true);

        if(empty($result) || !empty($result['errcode'])){
            // 错误提示
            $errorMessage = $result['errmsg'] ?? '企业微信请求错误';

            // 隐藏密钥
            if(isset($param['secret'])){
                unset($param['secret']);
            }

            throw new CustomException([
                'code' => 'QYWX_REQUEST_ERROR',
                'message' => $errorMessage,
                'log' => true,
                'data' => [
                    'url' => $url,
                    'header' => $header,
                    'param' => $param,
                    'result' => $result,
                ],
            ]);
        }

        return $result;
    }

    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return bool|string
     * @throws CustomException
     * CURL请求
     */
    private function curlRequest($url, $param = [], $method = 'GET', $header = [], $option = []){
        $ch = $this->buildCurl($url, $param, $method, $header, $option);

        $result = curl_exec($ch);

        //$info = curl_getinfo($ch);

        $errno = curl_errno($ch);

        if(!!$errno){
            throw new CustomException([
                'code' => 'CURL_REQUEST_ERROR',
                'message' => 'CURL请求错误',
                'log' => true,
                'data' => [
                    'url' => $url,
                    'header' => $header,
                    'param' => $param,
                    'result' => $result,
                    'error' => $errno,
                ],
            ]);
        }

        curl_close($ch);

        return $result;
    }

    /**
     * @param $url
     * @param array $param
     * @param string $method
     * @param array $header
     * @param array $option
     * @return false|resource
     * 构建curl
     */
    private function buildCurl($url, $param = [], $method = 'GET', $header = [], $option = []){
        $method = strtoupper($method);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $header = array_merge($header, ['Connection: close']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if(stripos($url, 'https://') === 0){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
        }

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
        }

        $timeout = $option['timeout'] ?? 30;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        return $ch;
    }
}

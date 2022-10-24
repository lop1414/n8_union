<?php


namespace App\Http\Controllers\Open;


use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ResponseCodeEnum;
use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use App\Common\Services\DataToQueueService;
use App\Enums\ReadSignTypeEnum;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ZyController extends BaseController
{

    /**
     * 众阅阅读标记
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function readSign(Request $request){
        $requestData = $request->all();
        $requestData['created_time'] = date('Y-m-d H:i:s');
        // 必传参数
        $this->validRule($requestData,[
            'id'       =>  'required',
            'package_id'   =>  'required'
        ]);

        $service = new DataToQueueService(QueueEnums::USER_READ_SIGN_ACTION);
        switch ($requestData['is_sign']){
            case 1:
                $readSignType = ReadSignTypeEnum::SIGN_1;
                break;
            case 2:
                $readSignType = ReadSignTypeEnum::SIGN_2;
                break;
            case 3:
                $readSignType = ReadSignTypeEnum::SIGN_3;
                break;
            default:
                $readSignType = '';
        }

        $product = ProductService::readByAlias($requestData['package_id'],CpTypeEnums::ZY);
        $service->push([
            'open_id'   => $requestData['id'],
            'product_alias' => $requestData['package_id'],
            'product_id' => $product['id'],
            'cp_type' => CpTypeEnums::ZY,
            'read_sign_type' => $readSignType,
            'created_time' => $requestData['created_time']
        ]);

        return $this->_response(0, '成功');
    }


}

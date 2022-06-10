<?php


namespace App\Http\Controllers\Admin;


use App\Common\Tools\CustomException;
use App\Datas\ProductWeixinMiniProgramData;
use App\Models\ProductModel;
use App\Models\ProductWeixinMiniProgramModel;
use App\Models\WeixinMiniProgramModel;
use Illuminate\Http\Request;

class ProductWeixinMiniProgramController extends BaseController
{


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ProductWeixinMiniProgramModel();
        $this->modelData = new ProductWeixinMiniProgramData();

        parent::__construct();
    }


    public function save(Request $request){
        $requestData = $request->all();
        $this->validRule($requestData,[
            'product_id' => 'required',
            'weixin_mini_program_id' => 'required',
            'url' => 'required',
            'path' => 'required',
        ]);

        $product = (new ProductModel())->where('id',$requestData['product_id'])->first();
        if(empty($product)){
            throw new CustomException([
                'code' => 'PRODUCT_NOT_EXISTS',
                'message' => "产品不存在",
            ]);
        }

        $weixinMiniProgram = (new WeixinMiniProgramModel())->where('id',$requestData['weixin_mini_program_id'])->first();
        if(empty($weixinMiniProgram)){
            throw new CustomException([
                'code' => 'WEIXIN_MINI_PROGRAM_NOT_EXISTS',
                'message' => "微信小程序不存在",
            ]);
        }

        $info = (new ProductWeixinMiniProgramData())->save($requestData);
        return $this->success($info);
    }

}

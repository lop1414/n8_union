<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Tools\CustomException;
use App\Models\ProductModel;
use App\Services\OpenApiAuthService;
use Illuminate\Http\Request;

class OpenApiController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * 验证
     *
     * @param Request $request
     * @return mixed
     * @throws CustomException
     */
    public function auth(Request $request){
        $req = $request->all();

        $this->validRule($req,[
            'product_id'    =>  'required'
        ]);

        $productInfo = (new ProductModel())
            ->where('id',$req['product_id'])
            ->first();


        if(empty($productInfo)){
            throw new CustomException([
                'code' => 'PARAM_ERROR',
                'message' => 'product_id 参数无效',
                'log' => true,
            ]);
        }


        // 验证
        (new OpenApiAuthService())->valid($req,$productInfo->secret);

        return $this->success();
    }
}

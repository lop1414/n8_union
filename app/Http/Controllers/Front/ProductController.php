<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Models\ProductModel;
use Illuminate\Http\Request;

class ProductController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * 列表
     *
     * @param Request $request
     * @return mixed
     */
    public function get(Request $request){
        $reqData = $request->all();

        $model = new ProductModel();
        $product = $model
            ->makeVisible('cp_secret')
            ->where($reqData)
            ->get();

        return $this->success($product);
    }
}

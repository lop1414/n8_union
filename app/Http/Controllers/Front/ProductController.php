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
        $id = $request->get('id');
        $type = $request->get('type');
        $cp_type = $request->get('cp_type');
        $cp_product_alias = $request->get('cp_product_alias');

        $model = new ProductModel();
        $product = $model
            ->makeVisible('cp_secret')
            ->when($id,function ($query,$id){
                return $query->where('id',$id);
            })
            ->when($type,function ($query,$type){
                return $query->where('type',$type);
            })
            ->when($cp_type,function ($query,$cp_type){
                return $query->where('cp_type',$cp_type);
            })
            ->when($cp_product_alias,function ($query,$cp_product_alias){
                return $query->where('cp_product_alias',$cp_product_alias);
            })
            ->get();

        return $this->success($product);
    }
}

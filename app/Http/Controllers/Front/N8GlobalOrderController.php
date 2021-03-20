<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\N8GlobalOrderService;
use Illuminate\Http\Request;

class N8GlobalOrderController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }




    public function read(Request $request){

        $req = $request->all();
        $this->validRule($req,[
            'n8_goid' =>  'required'
        ]);

        $info = (new N8GlobalOrderService())->read($req['n8_goid']);

        return $this->success($info);
    }



    public function readByOrderId(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'product_id' =>  'required',
            'order_id'   =>  'required',
        ]);

        $info = (new N8GlobalOrderService())->readByOrderId($req['product_id'],$req['order_id']);
        return $this->success($info);
    }


}

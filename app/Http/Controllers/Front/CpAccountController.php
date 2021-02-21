<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Models\CpAccountModel;
use Illuminate\Http\Request;

class CpAccountController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     */
    public function read(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'id'    =>  'required'
        ]);



        $model = new CpAccountModel();
        $product = $model
            ->where('id',$req['id'])
            ->first();

        return $this->success($product);
    }
}

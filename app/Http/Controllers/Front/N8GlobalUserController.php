<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\N8GlobalUserService;
use Illuminate\Http\Request;

class N8GlobalUserController extends FrontController
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
            'n8_guid' =>  'required',
        ]);

        $info = (new N8GlobalUserService())->read($req['n8_guid']);

        return $this->success($info);
    }



    public function readByOpenId(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'product_id' =>  'required',
            'open_id'    =>  'required',
        ]);

        $info = (new N8GlobalUserService())->readByOpenId($req['product_id'],$req['open_id']);
        return $this->success($info);
    }





}

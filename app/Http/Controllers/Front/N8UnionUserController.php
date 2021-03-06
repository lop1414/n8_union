<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Traits\ValidRule;
use App\Services\N8UnionUserService;
use Illuminate\Http\Request;

class N8UnionUserController extends FrontController
{
    use ValidRule;

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function create(Request $request){
        $requestData = $request->all();

        $this->validRule($requestData,[
            'n8_guid'      => 'required',
            'channel_id'   => 'required',
            'created_time' => 'required'
        ]);


        $ret = (new N8UnionUserService())->create($requestData);

        return $this->ret($ret,$ret);
    }


}

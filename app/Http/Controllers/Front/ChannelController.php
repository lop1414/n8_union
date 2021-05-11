<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Models\ChannelModel;
use Illuminate\Http\Request;

class ChannelController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function create(Request $request){
        $req = $request->all();

        $this->validRule($req,[
            'product_id'    =>  'required',
            'cp_channel_id'      =>  'required',
        ]);

        $model = (new ChannelModel())
            ->where('product_id',$req['product_id'])
            ->where('cp_channel_id',$req['cp_channel_id'])
            ->first();


        if(empty($model)){
            $model = new ChannelModel();
        }

        $model->product_id = $req['product_id'];
        $model->cp_channel_id = $req['cp_channel_id'];
        $model->name = $req['name'];
        $model->book_id = $req['book_id'];
        $model->chapter_id = $req['chapter_id'];
        $model->force_chapter_id = $req['force_chapter_id'];
        $model->create_time = $req['create_time'];
        $model->updated_time = $req['updated_time'];
        $model->save();

        return $this->success($model);
    }
}

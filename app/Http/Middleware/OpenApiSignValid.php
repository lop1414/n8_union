<?php

namespace App\Http\Middleware;

use App\Common\Enums\CpTypeEnums;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Common\Traits\ValidRule;
use App\Datas\ProductData;
use App\Services\OpenApiAuthService;
use Closure;

class OpenApiSignValid
{
    use ValidRule;


    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws CustomException
     */
    public function handle($request, Closure $next)
    {
        $req = $request->all();

        $this->validRule($req,[
            'product_alias'    =>  'required',
            'cp_type'          =>  'required',
        ]);

        Functions::hasEnum(CpTypeEnums::class, $req['cp_type']);

        $product = (new ProductData())
            ->setParams([
                'cp_product_alias'  => $req['product_alias'],
                'cp_type'           => $req['cp_type']
            ])
            ->read();

        if(empty($product)){
            throw new CustomException([
                'code' => 'PARAM_ERROR',
                'message' => 'product_alias 参数无效',
                'log' => true,
            ]);
        }

        // 验证
        (new OpenApiAuthService())->valid($req,$product['secret']);

        $request->offsetSet('product_id',$product['id']);
        return $next($request);
    }
}

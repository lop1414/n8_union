<?php

namespace App\Services\UserActionMatch;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\AdvClickSourceEnum;
use App\Common\Enums\MatcherEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Tools\CustomException;
use Illuminate\Support\Facades\DB;

class UserActionMatchService extends BaseService
{

    /**
     * @var
     * 广告商标识
     */
    protected $advAlias;


    /**
     * @var
     * 时间区间
     */
    protected $timeRange;


    /**
     * @var int
     * 每页数量
     */
    protected $pageSize = 10;



    protected $convertType;


    protected $advClickSourceMap;


    /**
     * @var
     * 匹配周期
     * 5分钟匹配一次
     */
    protected $matchCycle = 60 * 5;



    public function __construct(){
        parent::__construct();
        $this->setAdvClickSource();
    }


    /**
     * 归因方 映射 点击数据来源
     */
    public function setAdvClickSource(){
        $this->advClickSourceMap = array(
            MatcherEnum::SYS => AdvClickSourceEnum::ADV_CLICK_API,
            MatcherEnum::SECOND_VERSION => AdvClickSourceEnum::N8_TRANSFER,
            MatcherEnum::CP => AdvClickSourceEnum::N8_TRANSFER,
        );
    }


    public function setAdvAlias($alias){
        Functions::hasEnum(AdvAliasEnum::class,$alias);
        $this->advAlias = $alias;
    }



    public function setTimeRange($startTime,$endTime){
        Functions::checkTimeRange($startTime,$endTime);

        $this->timeRange = [
            'start' => $startTime,
            'end'   => $endTime
        ];
    }




    public function run(){

        try{
            DB::beginTransaction();

            $advAlias = strtolower($this->advAlias);
            if(!method_exists($this,$advAlias)){
                echo "未定义{$advAlias}方法！";
            }

            $this->$advAlias();

            DB::commit();

        }catch (CustomException $e){

            DB::rollBack();


            //日志
            (new ErrorLogService())->catch($e);


            // echo
            (new ConsoleEchoService())->error("自定义异常 {code:{$e->getCode()},msg:{$e->getMessage()}}");
        }catch (\Exception $e){

            DB::rollBack();

            //日志
            (new ErrorLogService())->catch($e);

            // echo
            (new ConsoleEchoService())->error("异常 {code:{$e->getCode()},msg:{$e->getMessage()}}");
        }
    }


    public function getQuery(){}



    public function ocean(){}


    /**
     * @param $fn
     * 数据分页执行
     */
    public function modelListPage($fn){

        $query = $this->getQuery();
        $total = $query->count();
        $totalPage = ceil($total / $this->pageSize);

        $page = 1;

        do{
            $offset = ($page - 1) * $this->pageSize;

            $list = $query->skip($offset)->take($this->pageSize)->get();

            //执行fn
            $fn($list);

            $page += 1;
        }while($page <= $totalPage);
    }


    /**
     * @param $data
     * @return array
     * 获取匹配所需设备信息
     */
    public function getDeviceInfo($data){
        return array(
            'ip'                    => $data['ip'] ?? '',
            'ua'                    => $data['ua'] ?? '',
            'muid'                  => $data['muid'] ?? '',
            'oaid'                  => $data['oaid'] ?? '',
            'device_brand'          => $data['device_brand'] ?? '',
            'device_manufacturer'   => $data['device_manufacturer'] ?? '',
            'device_model'          => $data['device_model'] ?? '',
            'device_product'        => $data['device_product'] ?? '',
            'device_os_version_name'=> $data['device_os_version_name'] ?? '',
            'device_os_version_code'=> $data['device_os_version_code'] ?? '',
            'device_platform_version_name' => $data['device_platform_version_name'] ?? '',
            'device_platform_version_code' => $data['device_platform_version_code'] ?? '',
            'android_id'            => $data['android_id'] ?? '',
            'request_id'            => $data['request_id'] ?? ''
        );
    }


    /**
     * @param $matcherEnum
     * @return mixed|string
     * 获取归因方 对应的 点击数据来源枚举
     */
    public function getAdvClickSourceEnum($matcherEnum){
        return $this->advClickSourceMap[$matcherEnum] ?? '';
    }


    /**
     * @return false|string
     * 获取匹配周期时间
     */
    public function getMatchCycleTime(){
        return  date('Y-m-d H:i:s',TIMESTAMP - $this->matchCycle);
    }

}

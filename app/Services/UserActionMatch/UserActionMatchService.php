<?php

namespace App\Services\UserActionMatch;

use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\AdvClickSourceEnum;
use App\Common\Enums\ConvertTypeEnum;
use App\Common\Enums\MatcherEnum;
use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Services\ConsoleEchoService;
use App\Common\Services\ErrorLogService;
use App\Common\Services\SystemApi\AdvBdApiService;
use App\Common\Services\SystemApi\AdvKsApiService;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Common\Services\SystemApi\AdvUcApiService;
use App\Common\Tools\CustomException;
use App\Datas\BookData;
use App\Datas\N8UnionUserData;
use App\Services\ProductService;
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


    /**
     * @var
     * 转化类型
     */
    protected $convertType;


    /**
     * @var
     * 广告商点击数据来源映射
     */
    protected $advClickSourceMap;


    protected $unionUserData;


    /**
     * @var
     * 匹配周期
     * 5分钟匹配一次
     */
    protected $matchCycle = 60 * 5;



    public function __construct(){
        parent::__construct();
        $this->setAdvClickSource();
        $this->unionUserData = new N8UnionUserData();
    }


    /**
     * 归因方 映射 点击数据来源
     */
    public function setAdvClickSource(){
        $this->advClickSourceMap = array(
            MatcherEnum::SYS => [AdvClickSourceEnum::ADV_CLICK_API,AdvClickSourceEnum::N8_AD_PAGE],
            MatcherEnum::SECOND_VERSION => [AdvClickSourceEnum::N8_TRANSFER],
            MatcherEnum::CP => [AdvClickSourceEnum::N8_TRANSFER],
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

        $query = $this->getQuery();
        do{
            try {

                DB::beginTransaction();

                $list = $query->skip(0)->take($this->pageSize)->get();
                $convert = [];

                //处理匹配数据
                foreach ($list as $item){
                    $id = $this->convertType == ConvertTypeEnum::REGISTER ? $item['id'] : $item['uuid'];
                    $unionUser = $this->unionUserData->setParams([
                        'id'   => $id
                    ])->read();


                    $tmp = $this->getConvertMatchData($item,$unionUser);

                    // 无需匹配
                    if(!$this->isCanMatch($item,$unionUser)){
                        $this->updateActionData([
                            'click_id'   => 0,
                            'convert_id' => $tmp['convert_id']
                        ]);
                        continue;
                    }

                    $extend = $item->extend ? $item->extend->toArray() : [];
                    array_push($convert,array_merge($tmp,$extend));
                }


                // 匹配
                if(!empty($convert)) {
                    echo "\r   匹配数:".count($convert)."\n";

                    $matchList = [];
                    if($this->advAlias == AdvAliasEnum::OCEAN){
                        // 巨量匹配
                        $matchList = (new AdvOceanApiService())->apiConvertMatch($convert);

                    }elseif ($this->advAlias == AdvAliasEnum::BD){
                        // 百度匹配
                        $matchList = (new AdvBdApiService())->apiConvertMatch($convert);
                    }elseif ($this->advAlias == AdvAliasEnum::KS){
                        // 快手匹配
                        $matchList = (new AdvKsApiService())->apiConvertMatch($convert);
                    }elseif ($this->advAlias == AdvAliasEnum::UC){
                        // UC匹配
                        $matchList = (new AdvUcApiService())->apiConvertMatch($convert);
                    }

                    // 匹配结果处理
                    foreach ($matchList as $match){

                        $this->updateActionData($match);
                    }
                }

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

        }while(!$list->isEmpty());

    }


    public function getQuery(){
        throw new CustomException([
            'code' => 'PLEASE_WRITE_CODE',
            'message' => '请书写代码 getQuery',
        ]);
    }


    // 是否可以匹配
    public function isCanMatch($item,$unionUser){
        //注册匹配不上 无需再匹配
        if(empty($unionUser['click_id'])){
            echo "没有click id 不进行匹配 \n";
            return  false;
        }

        return true;
    }



    // 更新行为数据
    public function updateActionData($match){
        throw new CustomException([
            'code' => 'PLEASE_WRITE_CODE',
            'message' => '请书写代码 updateActionData',
        ]);
    }



    /**
     * @param $item
     * @param $unionUser
     * @return array
     * 转化匹配数据
     */
    /**
     * @param $item
     * @param $unionUser
     * @return array
     * @throws CustomException
     * 转化匹配数据
     */
    public function getConvertMatchData($item,$unionUser){
        return array(
            'convert_type' => $this->convertType,
            'convert_id'   => $item['id'],
            'convert_at'   => $item['action_time'],
            'convert_times'=> 1,
            'click_id'     => $unionUser['click_id'],
            'n8_union_user'=>  $this->filterUnionUser($item,$unionUser)
        );
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
        return $this->advClickSourceMap[$matcherEnum] ?? [];
    }


    /**
     * @return false|string
     * 获取匹配周期时间
     */
    public function getMatchCycleTime(){
        return  date('Y-m-d H:i:s',TIMESTAMP - $this->matchCycle);
    }


    /**
     * @param $item
     * @param $unionUser
     * @return array
     * @throws CustomException
     */
    public function filterUnionUser($item,$unionUser){
        $book = $this->readBook($unionUser->book_id);
        return  [
            'guid'  => $item['n8_guid'],
            'channel_id' => $item['channel_id'],
            'created_at' => $item['created_time'],
            'click_source'  => $this->getAdvClickSourceEnum($item['matcher']),
            'product_type'  => ProductService::readToType($unionUser['product_id']),
            'cp_type'       => $book['cp_type'],
            'cp_book_id'    => $book['cp_book_id'],
            'book_name'     => $book['name'],
        ];
    }


    /**
     * @param $id
     * @return mixed|null
     * @throws CustomException
     * 获取数据信息
     */
    public function readBook($id){
        $info = [];

        if($id){
            $info = (new BookData())->setParams(['id' => $id])->read();
        }
        return $info;
    }

}

<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\PlatformEnum;
use App\Common\Tools\CustomException;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;
use App\Models\UserExtendModel;
use Jenssegers\Agent\Agent;

class N8UnionUserData extends BaseData
{


    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = true;


    /**
     * @var array
     * 字段
     */
    protected $fields = [];


    /**
     * @var array
     * 唯一键数组
     */
    protected $uniqueKeys = [
        ['n8_guid','channel_id']
    ];


    /**
     * @var int
     * 缓存有效期
     */
    protected $ttl = 60*60*24*3;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct(N8UnionUserModel::class);
    }



    public function create($data){

        try{
            //默认值
            $channel = [
                'book_id'    => 0,
                'chapter_id' => 0,
                'force_chapter_id' => 0,
            ];
            $channelExtend = [
                'admin_id'  => 0,
                'adv_alias' => AdvAliasEnum::UNKNOWN
            ];

            if(!empty($data['channel_id'])){
                $channel = (new ChannelData())->setParams(['id' => $data['channel_id']])->read();
                if(empty($channel)){
                    throw new CustomException([
                        'code'      => 'CHANNEL_NOT_EXIST',
                        'message'   => '渠道信息不存在',
                        'log'       => true,
                        'data'      => $data
                    ]);
                }

                $channelExtendTmp = (new ChannelExtendData())->setParams(['channel_id' => $data['channel_id']])->read();
                if(!empty($channelExtendTmp)){
                    $channelExtend = $channelExtendTmp;
                }
            }


            $product = (new ProductData())->setParams(['id' => $data['product_id']])->read();
            if(empty($product)){
                throw new CustomException([
                    'code'      => 'PRODUCT_NOT_EXIST',
                    'message'   => '产品不存在',
                    'log'       => true,
                    'data'      => $data
                ]);
            }


            $ua = $data['ua'] ?: $this->getUserUa($data['n8_guid']);
            $platform = '';
            if(!empty($ua)){
                $agent = new Agent();
                $agent->setUserAgent($ua);
                $platform = $agent->isiOS() ? PlatformEnum::IOS : PlatformEnum::ANDROID;
            }

            $ret = (new N8UnionUserModel())->create([
                'n8_guid'       => $data['n8_guid'],
                'product_id'    => $data['product_id'],
                'channel_id'    => $data['channel_id'],
                'created_time'  => $data['action_time'],
                'book_id'       => $channel['book_id'],
                'chapter_id'    => $channel['chapter_id'],
                'force_chapter_id' => $channel['force_chapter_id'],
                'platform'      => $platform,
                'admin_id'      => $channelExtend['admin_id'],
                'adv_alias'     => $channelExtend['adv_alias'],
                'matcher'       => $product['matcher'],
                'created_at'    => date('Y-m-d H:i:s')
            ]);

            (new N8UnionUserExtendModel())->create([
                'uuid'                  => $ret->id,
                'ip'                    => $data['ip'],
                'ua'                    => $data['ua'],
                'muid'                  => $data['muid'],
                'oaid'                  => $data['oaid'],
                'device_brand'          => $data['device_brand'],
                'device_manufacturer'   => $data['device_manufacturer'],
                'device_model'          => $data['device_model'],
                'device_product'        => $data['device_product'],
                'device_os_version_name'=> $data['device_os_version_name'],
                'device_os_version_code'=> $data['device_os_version_code'],
                'device_platform_version_name' => $data['device_platform_version_name'],
                'device_platform_version_code' => $data['device_platform_version_code'],
                'android_id'            => $data['android_id'],
                'request_id'            => $data['request_id']
            ]);

            $ret->extend;

            return $ret;

        }catch (\Exception $e){
            if($e->getCode() == 23000){
                throw new CustomException([
                    'code'      => 'UUID_EXIST',
                    'message'   => '用户已存在',
                    'log'       => true,
                    'data'      => $data
                ]);
            }else{
                throw $e;
            }
        }

    }


    public function update($where = [],$update = []){
        if(empty($update)) return;

        $this->model
            ->where($where)
            ->update($update);

        // 删除缓存
        $this->setParams($where)->clear();
    }


    public function getUserUa($n8Guid){
        $info = (new UserExtendModel())->where('n8_guid',$n8Guid)->first();
        return $info['ua'];
    }
}

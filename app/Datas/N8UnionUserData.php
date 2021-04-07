<?php


namespace App\Datas;


use App\Common\Datas\BaseData;
use App\Common\Tools\CustomException;
use App\Models\N8UnionUserExtendModel;
use App\Models\N8UnionUserModel;

class N8UnionUserData extends BaseData
{


    /**
     * @var bool
     * 缓存开关
     */
    protected $cacheSwitch = false;


    /**
     * @var array
     * 字段
     */
    protected $fields = ['id','n8_guid','channel_id'];


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
            $channel = (new ChannelData())->setParams(['id' => $data['channel_id']])->read();
            if(empty($channel)){
                throw new CustomException([
                    'code'      => 'CHANNEL_NOT_EXIST',
                    'message'   => '渠道信息不存在',
                    'log'       => true,
                    'data'      => $data
                ]);
            }

            $ChannelExtend = (new ChannelExtendData())->setParams(['channel_id' => $data['channel_id']])->read();
            if(empty($ChannelExtend)){
                throw new CustomException([
                    'code'      => 'CHANNEL_EXTEND_NOT_EXIST',
                    'message'   => '渠道扩展信息不存在',
                    'log'       => true,
                    'data'      => $data
                ]);
            }

            $ret = (new N8UnionUserModel())->create([
                'n8_guid'       => $data['n8_guid'],
                'channel_id'    => $data['channel_id'],
                'created_time'  => $data['action_time'],
                'book_id'       => $channel['book_id'],
                'chapter_id'    => $channel['chapter_id'],
                'force_chapter_id' => $channel['force_chapter_id'],
                'admin_id'      => $ChannelExtend['admin_id'],
                'adv_alias'     => $ChannelExtend['adv_alias'],
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
}

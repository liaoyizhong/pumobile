<?php

namespace app\images\service;

use app\images\interfaces\BasicInterface;
use OSS\OssClient;
use app\common\enums\ResponseCode;

/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 14:58
 */
class AliService implements BasicInterface
{
    private $accessKeyId = "LTAI6JVNEbAQ2CCS";
    private $accessKeySecret = "rDDwPK4mejJQJSbEObD8ueBIrNefO9";
    private $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
    const BUCKET = "xiaopu01"; //事先建好的空间

    public function sendImages($file)
    {
        try {
            $isImage = preg_match('/^image\//',current($file)['type']);
            if(!$isImage){
                return[FALSE,'参数类型不正确'];
            }
            preg_match('/.*\/(.*)/',current($file)['type'],$matches);
            $name = $this->createHashCode().'.'.$matches[1];
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $resturn = $ossClient->uploadFile(self::BUCKET,$name,current($file)['tmp_name']);
            if(isset($resturn['info']['http_code']) && $resturn['info']['http_code'] == 200){
                $model = new \app\images\model\ImagesModel();
                $model->hash_code = $name;
                $model->url = '';
                if(!$model->save()){
                    return [FALSE,'数据保存失败'];
                }
                return [TRUE,'保存成功',$name];
            }else{
                return [FALSE,'云端保存失败'];
            }
        } catch (OssException $e) {
            return [FALSE,$e->getMessage()];
        }
    }

    public function getImagesUrl($hashCode)
    {
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $signedUrl = $ossClient->signUrl(self::BUCKET, $hashCode, 3600);
            return[TRUE,'成功',$signedUrl];
        } catch (OssException $e) {
            return[FALSE,$e->getMessage()];
        }
    }

    public function getUrl($hashCode)
    {
        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $signedUrl = $ossClient->signUrl(self::BUCKET, $hashCode, 3600);
            return $signedUrl;
        } catch (OssException $e) {
            return "";
        }
    }

    public function createHashCode()
    {
        return md5(mt_rand().time().mt_rand());
    }
}
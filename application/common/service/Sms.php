<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/9/5 23:15
 * description :
 */

namespace app\common\service;


use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Exception\ClientException;
use Aliyun\Core\Profile\DefaultProfile;
use app\lib\exception\SmsException;
use think\Cache;
use think\Config;

// 加载区域结点配置
\Aliyun\Core\Config::load();

class Sms
{

    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $signName = '';
    private $expires_in = 0;
    private $resendTime = 60;

    /**
     * 构造器
     *
     * @param string $accessKeyId 必填，AccessKeyId
     * @param string $accessKeySecret 必填，AccessKeySecret
     */
    public function __construct()
    {
        $this->accessKeyId = Config::get('sms.access_key_id');
        $this->accessKeySecret = Config::get('sms.access_key_secret');
        $this->signName = Config::get('sms.sign_name');
        $this->expires_in = (int)Config::get('sms.expires_in');
        $this->resendTime = (int)Config::get('sms.resend_time');
    }

    private function _init()
    {
        // 短信API产品名
        $product = "Dysmsapi";

        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        // 初始化用户Profile实例
        $profile = DefaultProfile::getProfile($region, $this->accessKeyId, $this->accessKeySecret);

        // 增加服务结点
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

        // 初始化AcsClient用于发起请求
        $this->acsClient = new DefaultAcsClient($profile);
    }

    /**
     * 发送短信函数
     *
     * @param $templateCode
     * @param $phoneNumbers
     * @param null $templateParam
     * @param null $outId
     * @return mixed|\SimpleXMLElement
     */
    protected function sendSms($templateCode, $phoneNumbers, $templateParam = null, $outId = null)
    {
        $this->_init();

        try {
            // 初始化SendSmsRequest实例用于设置发送短信的参数
            $request = new SendSmsRequest();

            // 必填，设置雉短信接收号码
            $request->setPhoneNumbers($phoneNumbers);

            // 必填，设置签名名称
            $request->setSignName($this->signName);

            // 必填，设置模板CODE
            $request->setTemplateCode($templateCode);

            // 可选，设置模板参数
            if ($templateParam) {
                $request->setTemplateParam(json_encode($templateParam));
            }

            // 发起访问请求
            $acsResponse = $this->acsClient->getAcsResponse($request);
//            $acsResponse = (object)['Code'=>'OK', 'RequestId'=>'1'];
            // 更新短信日志
            if ('OK' === $acsResponse->Code) {
                // success

                // 写入缓存
                $this->cacheVerifyCode($phoneNumbers, $templateParam['code']);
                return $acsResponse->RequestId;
            } else {
                // failed
                return false;
            }

        } catch (ClientException $e) {
            echo '<pre>';var_dump( $e->getErrorMessage());echo '</pre>';exit();
            throw new SmsException([
                'errorCode' => $e->getErrorCode(),
                'msg' => $e->getErrorMessage()
            ]);
        } catch (\ServerException $e) {
            throw new SmsException([
                'errorCode' => $e->getErrorCode(),
                'msg' => $e->getErrorMessage()
            ]);
        }
    }

    /**
     * 生成短信码,默认6位
     */
    protected function createVerifyCode($length = 6)
    {
        $verifyCode = '';
        mt_srand(( double )microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $verifyCode .= mt_rand(0, 9);
        }
        return $verifyCode;
    }

    /**
     * 将值写入到cache中，一般以手机号码作为key
     *
     * @param $key
     * @param $value
     */
    protected function cacheVerifyCode($mobile, $code)
    {
        $cacheData = [
            'code' => $code,
            'times' => $_SERVER['REQUEST_TIME'] + $this->resendTime, //重复发送时间
        ];
        $result = Cache::set($mobile, $cacheData, $this->expires_in);
        if (!$result) {
            throw new \Exception('服务器缓存异常');
        }
    }

    /**
     * 判断并将验证码写入缓存
     * 1. 判断是否在60S内重复发送的，是则直接退出
     *
     * @param $mobile
     * @param bool $code
     * @return bool
     */
    public function verifyCode($mobile, $code = '')
    {

        $isExisit = Cache::has($mobile);
        $cache = Cache::get($mobile);

        if (!$code) {   //判断是否在规定时间内重复发送
            if ($isExisit) {
                if ($cache['times'] > time()) {
                    throw new SmsException([
                        'msg' => '您发送短信频率过快',
                        'errorCode' => '1015'
                    ]);
                }
            }
            return true;
        } else {  //判断验证码是否输入正确
            if ($isExisit) {
                if ($cache['code'] == $code) {
                    return true;
                }
            }
            throw new SmsException([
                'msg' => '短信验证码错误或已过期',
                'errorCode' => '1016'
            ]);
        }
    }
}
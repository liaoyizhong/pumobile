<?php
namespace app\test\controller;
use app\common\controller\Basic as BasicController;

use app\common\service\FrontEndSms;
use app\lib\enum\SmsTemplateEnum;
use OSS\OssClient;

/**
 * User: liaoyizhong
 * Date: 2017/11/10/010
 * Time: 16:40
 */

class Test extends BasicController
{
    public function zipImage()
    {
        $endpoint = "http://oss-cn-hangzhou.aliyuncs.com";
        $accessKeyId = "LTAI6JVNEbAQ2CCS";
        $accessKeySecret = "rDDwPK4mejJQJSbEObD8ueBIrNefO9";
        $bucket = "xiaopu01";
        $object = "8d0efa03850ee547026ba2eb8dc61977.jpeg";
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        /**
         * 生成一个带签名的可用于浏览器直接打开的url, URL的有效期是3600秒
         */
        $timeout = 3600;
        $options = array(
            OssClient::OSS_PROCESS => "image/resize,m_lfit,h_100,w_100" );
        $signedUrl = $ossClient->signUrl($bucket, $object, $timeout, "GET", $options);
        echo $signedUrl;
    }

    public function smsTest()
    {
        $sms = new FrontEndSms();
        $result = $sms->sendCommonSms('180276260812',
            SmsTemplateEnum::CODESMS_LOGIN);

        if(!$result){

        }
        return json('', 201);
    }

    public function checkSms()
    {
        $sms = new FrontEndSms();
        $sms->verifyCode($this->request->post("mobile"),$this->request->post("code"));
    }

    public function mqTest()
    {
        $conn_args = array(
            'host'=>'127.0.0.1',  //rabbitmq 服务器host
            'port'=>5672,         //rabbitmq 服务器端口
            'login'=>'jsapi',     //登录用户
            'password'=>'123456',   //登录密码
            'vhost'=>'/'         //虚拟主机
        );
        $e_name = 'e_demo';
        $q_name = 'aoa';
        $k_route = 'key_1';

        $conn = new \AMQPConnection($conn_args);
        if(!$conn->connect()){
            die('Cannot connect to the broker');
        }
        $channel = new \AMQPChannel($conn);
        $ex = new \AMQPExchange($channel);
        $ex->setName($e_name);
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        $ex->setFlags(AMQP_DURABLE);
        $status = $ex->declareExchange();

        $q = new \AMQPQueue($channel);
        $q->setName($q_name);
        $qu = $q->declareQueue();
        $result = $ex->publish('push message here222',$k_route);
        echo '<pre>';var_dump($result);echo '</pre>';exit();
    }

    public function getmqTest()
    {
        $conn_args = array(
            'host'=>'127.0.0.1',  //rabbitmq 服务器host
            'port'=>5672,         //rabbitmq 服务器端口
            'login'=>'jsapi',     //登录用户
            'password'=>'123456',   //登录密码
            'vhost'=>'/'         //虚拟主机
        );
        $e_name = 'e_demo';
        $q_name = 'aoa';
        $k_route = 'key_1';

        $conn = new \AMQPConnection($conn_args);
        if(!$conn->connect()){
            die('Cannot connect to the broker');
        }
        $channel = new \AMQPChannel($conn);
        $ex = new \AMQPExchange($channel);
        $ex->setName($e_name);
        $ex->setType(AMQP_EX_TYPE_DIRECT);
        $ex->setFlags(AMQP_DURABLE);

        $q = new \AMQPQueue($channel);
        $q->setName($q_name);
        $q->bind($e_name, $k_route);
        
        $arr = $q->get();
        var_dump($arr);exit;
        $res = $q->ack($arr->getDeliveryTag());
        $msg = $arr->getBody();
        var_dump($msg);
    }
}
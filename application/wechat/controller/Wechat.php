<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/13
 * Time: 9:41
 */

namespace app\wechat\controller;

use app\common\controller\Basic;
use app\common\controller\Common;
use app\common\logic\ErrorLogLogic;
use app\lib\exception\FailMessage;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use app\lib\exception\WechatException;
use think\Config;
use think\Db;
use think\Loader;
use think\Session;
use think\Controller;
use think\Request;

class Wechat extends Basic
{
    const SHOWURL = 'http://www.lubanfaxian.com/wechat/index.html';

    protected $app;

    public function __construct()
    {
        $config = Config::get("wechat");
        $this->options['app_id'] = $config['app_id'];
        $this->options['secret'] = $config['secret'];
        $this->options['token'] = $config['token'];
        $this->options['aes_key'] = $config['aes_key'];
        $this->options['oauth'] = $config['oauth'];
        $this->options['log'] = $config['log'];
        if (!$this->app) {
            $this->app = new Application($this->options);
        }
    }

    /**
     * 微信服务器对接访问路径
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function reception()
    {
        $app = new Application($this->options);
        // 从项目实例中得到服务端应用实例。
        $server = $app->server;
        $server->setMessageHandler(function ($message) {
            Session::set('openid', $message->FromUserName);
            switch ($message->MsgType) {
                case 'event':
                    Session::set('openid', $message->FromUserName);
                    return $this->doEvent();
                    break;
                case 'text':
                    Session::set('openid', $message->FromUserName);
                    return $this->doMsg();//消息
                    break;
                default :
                    //TODO:默认消息
                    break;
            }
        });
        $response = $server->serve();
        $response->send(); // Laravel 里请使用：return $response;
    }


    /**
     * 消息反馈
     * @return Ambigous <NULL, \EasyWeChat\Message\Text>
     */
    public function doMsg()
    {
        $server = $this->app->server;
        $server->setMessageHandler(function ($message) {
            return new \EasyWeChat\Message\Transfer();
        });
        $result = $server->serve();
        echo $result;
    }

    /**
     * 事件反馈
     * @return Ambigous <NULL, \EasyWeChat\Message\Text>
     */
    public function doEvent()
    {
        $server = $this->app->server;
        $message = $server->getMessage();
        $fromusername = $message['FromUserName'];//openid
        $create_at = date('Y-m-d H:i:s', $message['CreateTime']);
        $eventType = $message['Event'];
        $response = null;
        Session::set('openid', $fromusername);
        //记录事件
        switch ($eventType) {
            case 'subscribe' ://关注事件
                $logic = Loader::model('\app\home\logic\WxUserLogic','logic');
                $old = $logic->getRow(["open_id"=>$fromusername]);
                if(!$old){
                    $params['open_id'] = $fromusername;
                    $logic->save($params);
                }
                $user = $this->app->user->get($fromusername);
                \app\common\logic\ErrorLogLogic::save($user);
                break;
            case 'unsubscribe' :
                ErrorLogLogic::save('wechat unsubscribe');
                break;
            case 'CLICK' ://点击菜单事件
                ErrorLogLogic::save('wechat click');
                break;
            case 'SCAN' ://用户已扫码
                // code...
                break;
            case 'VIEW' ://点击链接菜单事件
                // code...
                break;
            default :
                // code...
                break;
        }

        return $response;
    }


    public function bangding(Request $request)
    {

    }

    /**
     * 授权回调地址
     */
    public function oauthCallback()
    {
        \app\common\logic\ErrorLogLogic::save($_SERVER['SERVER_NAME']);
        \app\common\logic\ErrorLogLogic::save($_SERVER['REQUEST_URI']);
        $oauth = $this->app->oauth;

// 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        echo '<pre>';var_dump($user);echo '</pre>';exit();
    }

    public function oauthStart()
    {
//        $oauth = $this->app->oauth;
//
//        $response = $this->app->oauth->scopes(['snsapi_userinfo'])
//            ->redirect();
        $response = $this->app->oauth->redirect()->send();
    }

    /*
    * 创建微信日志
    */
    public function log($message)
    {
        // 创建日志频道
        $log = new Logger('wechat');
        $log->pushHandler(new StreamHandler(LOG_PATH . '/wechat/' . date('Ymd') . '.log', Logger::INFO));
        // 添加日志记录
        $log->addInfo($message);
    }

}

<?php
/**
 * Created by PhpStorm.
 * User: I
 * Date: 2017/10/25
 * Time: 23:06
 * 参考鲁班发现用的微信登录文件
 */

namespace app\api\controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;
use app\lib\validate\TokenValidate;
use app\lib\exception\FailMessage;
use app\lib\exception\WechatException;
use app\api\logic\Wechat as WechatLogic;
use think\Session;
use think\Request;
use think\Config;
use think\Db;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class WechatApi extends BaseController
{
    protected $beforeActionList = [
        'checkLoginState'=>[
            'except'=>['login','bangding']
        ],
    ];
    protected $options = [
        'oauth' => [
            'scopes'   => ['snsapi_userinfo'],
            'callback' => 'http://www.lubanfaxian.com/wechat/oauth_callback',
        ],
    ];
    protected $app;

    public function _initialize()
    {
        $config = Config::get("wxaccount.lbfx");
        $this->options['app_id'] = $config['app_id'];
        $this->options['secret'] = $config['secret'];
        $this->options['token'] = $config['token'];
        $this->options['aes_key'] = $config['aes_key'];
        if(!$this->app){
            $this->app = new Application($this->options);
        }
    }


    /**
     *检查微信登录状态
     *
     */
    protected function checkLoginState(){
        $target_url = Request::instance()->path();
        Session::set('target_url','/'.$target_url);
        $openid = Session::get('openid');

        if(!$openid){
                $response = $this->app->oauth->scopes(['snsapi_userinfo'])
                    ->redirect();
                $response->send();
        }
        if($openid){
            $uid = Db::table('wxuser')->where('openid',$openid)->value('uid');
            if(!$uid){
                $this->redirect('http://www.lubanfaxian.com/wechatapi/login');
            }

        }



    }

    /*
     * 微信绑定平台
     */
    public function bangding(Request $request){
//        echo 111;exit;
        $return = [
            'msg'=>'绑定成功',
            'status'=>1,
            'errorCode'=>0
        ];
        $data['mobile'] = $request->post('mobile');
        $data['password'] = $request->post('password');
        $validate = new TokenValidate();
        $result = $validate->check($data); // 参数校验
        if(true !== $result){
            $return['status'] = 0;
            $return['msg'] = $validate->getError();
            return json($return,200);
        }

        $user = Session::get('wechat_user');
        if(!$user){
            $return['status'] = 0;
            $return['msg'] = '获取用户信息失败！';
            return json($return,200);
        }
        $user = unserialize($user);
        $openid = $user->getId();
//        $openid = Session::get('openid');
        $res = (new WechatLogic())->bingding($request->post('mobile'),$request->post('password'),$openid);

        if($res['status'] == 0){
            self::log("绑定失败,openid：".$openid);
            $return['status'] = 0;
            $return['msg'] = $res['msg'];
        }
        self::log("绑定成功,openid：".$openid);
        return json($return,200);
//        $this->redirect('http://www.lubanfaxian.com');
    }

    /*****
     * 绑定页面
     ***/
    public function login(){

        $target_url = Session::get('target_url');
        $target_url = (empty($target_url)||!isset($target_url))? '/wechatapi/userCenter':$target_url;
//        $oauth = $this->app->oauth;
//        // 未登录
//        if (empty(Session::get('wechat_user'))) {
////            Session::set('target_url','wechat/test');
//            return $oauth->redirect();
//        }
//        $user = unserialize(Session::get('wechat_user'));
//        $openid = $user->getId();
//        $uid = Db::table('wxuser')->where('openid',$openid)->value('uid');
//        if($uid){
//            $this->redirect('http://www.lubanfaxian.com');
//        }
        $this->assign('target_url',$target_url);
        return view('wechat/binding');
        try{
            session_start();
            $oauth = $this->app->oauth;
            $user = $oauth->user();
            $openid = $user->getId();
//            echo $openid;
            Session::set('wechat_user',serialize($user));
            Session::set('openid',$openid);
        }catch(\Exception $e) {
//            $openid = Session::get('openid');
//            print_r($e);
            return json('获取用户信息失败！',400);

        }
        if($openid){
            $uid = Db::table('wxuser')->where('openid',$openid)->value('uid');
            if($uid){
                $this->redirect('http://www.lubanfaxian.com');
            }
            return view('wechat/binding');
        }
//        return view('wechat/binding-phone');
    }

    /**
     * 用户中心
     */
    public function userCenter(){
        $this->redirect('http://www.lubanfaxian.com');
    }

    /**
     * 审批
     */
    public function approval(){
        echo "这是审批页面";
        echo Session::get('openid');
    }

    /**
     * 数据集
     */
    public function dataFeed(){
        echo "这是数据集页面";
    }

    /**
    *** 创建微信日志****
    *
    **/
    public function log($message){
        // 创建日志频道
        $log = new Logger('wechat');
        $log->pushHandler(new StreamHandler(LOG_PATH.'/wechat/'.date('Ymd').'.log', Logger::INFO));
        // 添加日志记录
        $log->addInfo($message);
    }
}
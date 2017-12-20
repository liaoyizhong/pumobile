<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/11/2 16:41
 * description :
 */

namespace app\api\controller;

use app\api\logic\Wxuser as WxuserLogic;
use app\common\controller\Common;
use app\lib\enum\UserRoleEnum;
use app\lib\exception\WechatException;
use think\Config;
use think\Request;
use think\Session;
use app\api\model\Wxuser as WxuserModel;

class WeChatBase extends Common
{
    // openID
    protected $openID = '';
    // 用户ID
    protected $uid = 0;
    // 用户名
    protected $username = '';
    // 当前角色
    protected $scope = '';
    // 跳转地址
    protected $targetUrl = '';
    // Request
    protected $request = null;

    public function _initialize()
    {
        $this->request = Request::instance();

        $this->getOpenID(); // 所有微信进来都需要读openID
    }

    protected function getUid()
    {
        $wxUserLogic = new WxuserLogic();
        $wxUser = $wxUserLogic->getUidByOpenID($this->openID);

        if(empty($wxUser['uid'])){
            throw new WechatException([
                'errorCode' => 10003,
                'msg' => '未绑定帐户'
            ]);
        }

        $this->uid = (int)$wxUser['uid'];
        $this->scope = (int)$wxUser['type'];
    }

    protected function checkSupplierAuth()
    {
        if (!(UserRoleEnum::SUPPLIER_ROLE == $this->scope)) {
            throw new WechatException([
                'msg' => '您没有权限操作'
            ]);
        }
    }

    protected function checkBuyerAuth()
    {
        if (!(UserRoleEnum::BUYER_ROLE == $this->scope)) {
            throw new WechatException([
                'msg' => '您没有权限操作'
            ]);
        }
    }

    public function getOpenID()
    {
//       return $this->openID = 'o7qmq048jIRwKkmMM0DjiIiRw5BQ';
        $isWeixinBrowser = isWeixinBrowser();

        if (!$isWeixinBrowser) {
            throw new WechatException([
                'msg' => '请在微信环境中打开链接',
            ]);
        }

        $openID = Session::get('openid');

        if($openID){
            return $this->openID = $openID; // 直接取出
        }

        $code = $this->request->get('code');
        if(empty ($code) && empty($openID)){
            throw new WechatException([
                'code' => 401,
                'errorCode' => '10001',
                'msg' => '未授权',
            ]);
        }

        $state = $this->request->get('state');
        if ($state != Config::get('wechat.token')) {
            throw new WechatException([
                'msg' => '异常token',
            ]);
        }

        $param = [];
        $param ['appid'] = Config::get('wechat.app_id');
        $param ['secret'] = Config::get('wechat.secret');
        $param ['code'] = $code;
        $param ['grant_type'] = 'authorization_code';
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);

        $content = file_get_contents($url);
        $content = json_decode($content, true);

        if(empty($content ['openid'])){
            throw new WechatException();
        }

        $openID = $content ['openid'];

        Session::set('openid', $openID);
        return $this->openID = $openID;
    }

    public function oauthWeixin()
    {
        $isWeixinBrowser = isWeixinBrowser();

        if (!$isWeixinBrowser) {
            throw new WechatException([
                'msg' => '请在微信环境中打开链接',
            ]);
        }

        $param = [];
        $param ['appid'] = Config::get('wechat.app_id');
        $param ['redirect_uri'] = 'http://www.lubanfaxian.com/api/wechat/openid';
        $param ['response_type'] = 'code';
        $param ['scope'] = 'snsapi_base';
        $param ['state'] = 'lbfx';
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query ( $param ) . '#wechat_redirect';

        header ( 'Location: ' . $url ); // 拉起授权
    }

    public function destroy()
    {
        Session::delete('openid');

        return 'ok';
    }
}

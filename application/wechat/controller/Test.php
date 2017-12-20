<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/19/019
 * Time: 14:35
 */

namespace app\wechat\controller;


use app\common\controller\Basic;
use app\common\logic\ErrorLogLogic;

class Test extends Basic
{
    const ID = 'wxe016d95590aa3b88';
    const SECRET = 'cf4ee5b01efa6583dc8033cb6234ad29';
    const URI = 'http://puwap.lubanfenqi.com/test/oauthCallback';

    public function oauthStart()
    {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . self::ID . '&redirect_uri=' . self::URI . '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
        header("Location:" . $url);
    }

    public function oauthCallBack()
    {
        $code = $_GET['code'];
        ErrorLogLogic::save('code:'.$code);
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . self::ID . '&secret=' . self::SECRET . '&code=' . $code . '&grant_type=authorization_code';
        ErrorLogLogic::save('url:'.$url);
        header("Location:" . $url);
    }
}
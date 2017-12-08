<?php

namespace app\managers\logic;

use app\common\logic\BasicLogic;
use app\common\service\FrontEndSms;
use app\lib\enum\SmsTemplateEnum;
use app\managers\model\ManagersModel;
use Firebase\JWT\JWT;
use think\Cache;

/**
 * User: liaoyizhong
 * Date: 2017/12/8/008
 * Time: 10:23
 */
class LoginLogic extends BasicLogic
{
    private $need_nonce = true;

    const TOKENPRE = 'PuMobile';

    // 是否需要随机字符串
    // Token有效时间
    private $expires_in = 3600;
    // Token加密串
    private $secret_key = 'XiaoPuJia';

    /**
     * 管理员短信登录
     * @param $params
     * @return array
     */
    public function doLogin($params)
    {
        //校对用户
        $model = new ManagersModel();
        $model = $model->where('phone', $params['phone'])->find();
        if (!$model) {
            return [FALSE, '帐号不正确', ''];
        }
        //验证token
        $cache = Cache::get($params['phone']);

        if ($cache['code'] != $params['code']) {
            return [FALSE, "验证码错误", ""];
        }

        $token = $this->generateJWT(array("uid" => $model->id, "phone" => $model->phone));
        if ($token) {
            header('token:' . $token);
        }
        $msg = array(
            'user_id' => $model->id,
            'role' => \app\managers\model\ManagersModel::ROLEMANAGER
        );
        if (Cache::set($token, $msg, 7200)) {
            Cache::rm($params['phone']);
            return [TRUE, "登录成功", $token];
        } else {
            return [FALSE, "登录失败", ''];
        }
    }

    protected function createUniqidNonce()
    {
        return md5(mt_rand().self::TOKENPRE.time());
    }

    /**
     * 生成JWT令牌
     *
     * @param $payload
     * @return string
     */
    protected function generateJWT($payload = [])
    {
        if ($this->need_nonce) {
            // 需要随机字符串
            $payload['nonce'] = $this->createUniqidNonce();
        }
        // 过期时间 = 当前请求时间 + token过期时间
        $payload['exp'] = $_SERVER['REQUEST_TIME'] + $this->expires_in;
        $jwt = JWT::encode($payload, $this->secret_key); // 生成jwt

        return $jwt;
    }

}
<?php

namespace app\users\services;

use app\common\services\BasicService;
use app\users\models\UsersModel;
use Firebase\JWT\JWT;

/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 14:48
 */
class UsersService extends BasicService
{
    const PASSWORDPRE = 'jiaju';

    const TOKENPRE = 'XiaoPu';

    // 是否需要随机字符串
    private $need_nonce = true;
    // Token有效时间
    private $expires_in = 3600;
    // Token加密串
    private $secret_key = 'XiaoPuJia';

    /**
     * @param $id
     * @return null|static
     */
    public function get($id)
    {
        return UsersModel::get($id);
    }

    public function checkLogin($params)
    {
        if(!isset($params['name']) || !isset($params['password'])){
            return [FALSE,'缺必要参数'];
        }
        $model = new UsersModel();
        $passwordToken = self::PASSWORDPRE;
        $mdPassword = (string)md5($passwordToken.$params['password']);
        $row = $model->where(function($query)use($params,$mdPassword){
            $query->where('nick_name',$params['name'])->where('password',$mdPassword);
        })->whereOr(function($query)use($params,$mdPassword){
            $query->where('phone',$params['name'])->where('password',$mdPassword);
        })->find();
        if(!count($row)){
            return [FALSE,"登录失败，帐号密码不正确"];
        }
        $token = $this->generateJWT(array("uid"=>$row['id'], "username"=>$row['nick_name']));
        $redis = new \think\cache\driver\Redis();
        $redis->set($token, $row['id'], 7200);
        return [TRUE, "登录成功", ['token' => $token]];
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
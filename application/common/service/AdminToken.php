<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/8/18 09:22
 * description :
 */

namespace app\common\service;


use app\admin\logic\Admin as AdminLogic;
use app\lib\enum\UserRoleEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;

class AdminToken extends Token
{
    /**
     * 按照规定组合成Token并生成JWT
     *
     * @param $account
     * @param $password
     * @return string JWT key
     * @throws TokenException
     */
    public function generateToken($userInfo = [])
    {
        // 校验帐户密码是否正确
        $account = AdminLogic::checkSuper($userInfo['account'], $userInfo['password']);
        if (empty($account)) {
            throw new TokenException([
                'msg' => '非法授权',
                'errorCode' => 1002
            ]);
        }
        // 组合拼装参数
        $payload = $this->prepareTokenValue($account);
        // 生成并缓存Token
        return $this->generateJWT($payload);
    }

    public function refreshToken($uid, $last_jwt = [])
    {
        $this->verifyNonce($last_jwt, true); // 验证nonce，全部交由后台自行确认是否需要校验

        // 查询当前用户信息
        $account = AdminLogic::getSuperAccount($uid);
        if (empty($account)) {
            throw new TokenException([
                'msg' => '非法授权',
                'errorCode' => 1002
            ]);
        }
        // 组合拼装参数
        $payload = $this->prepareTokenValue($account);
        // 生成并缓存Token
        return $this->generateJWT($payload);
    }

    /**
     * 组合管理员Token值
     *
     * @param $account
     * @return mixed
     */
    protected function prepareTokenValue($account)
    {
        $cachedValue['uid'] = (int)$account['id'];
        $cachedValue['username'] = $account['username'];
        $cachedValue['scope'] = UserRoleEnum::SUPPER_ROLE;

        return $cachedValue;
    }

    /**
     * 校验管理员Token是否合法
     *
     * @param string $token
     * @return mixed|array
     * @throws ForbiddenException
     * @throws TokenException
     */
    public function verifyToken($token = '')
    {
        if (!$token) {
            $token = $this->getCurrentTokenByKey();
        }



        if (!is_array($token)) {
            return "无效Token";
//            throw new TokenException([
//                'msg' => '无效Token'
//            ]);
        }

       /* if (!array_key_exists('uid', $token)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        if (!array_key_exists('scope', $token)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        if (UserRoleEnum::SUPPER_ROLE !== $token['scope']) {
            throw new ForbiddenException([
                'msg' => '您没有权限'
            ]);
        }*/

        $this->verifyNonce($token); // 仅判断,不使用

        return $token;
    }
}
<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/9/13 17:00
 * description :
 */

namespace app\common\service;


class CommonToken extends Token
{
    /**
     * 校验Token是否合法
     *
     * @param string $token
     * @return mixed|object|string
     * @throws ForbiddenException
     * @throws TokenException
     */
    public function verifyToken($token = '')
    {
        if (!$token) {
            $token = $this->getCurrentTokenByKey();
        }

        parent::verifyToken($token);

        return $token;
    }

    public function generateToken($userInfo = []){}
    public function refreshToken($uid, $last_jwt = []){}
    protected function prepareTokenValue($account){}
}
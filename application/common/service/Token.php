<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/8/16 14:37
 * description :
 * Token标准类
 */

namespace app\common\service;


use app\lib\exception\ParamsException;
use app\lib\exception\TokenException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use think\Cache;
use think\Config;
use think\Request;

abstract class Token
{
    // 是否需要随机字符串
    private $need_nonce = true;
    // Token有效时间
    private $expires_in = 0;
    // Token加密串
    private $secret_key = '';

    public function __construct()
    {
        $this->need_nonce = Config::get('token.need_nonce_str');
        $this->expires_in = (int)Config::get('token.expires_in');
        $this->secret_key = Config::get('token.secret_key');
    }

    /**
     * 按照规定组合成Token并生成JWT
     *
     * @param array $userInfo
     * @return mixed
     */
    abstract public function generateToken($userInfo = []);

    /**
     * 重新刷新Token
     *
     * @param $uid
     * @param $scope
     * @return mixed
     */
    abstract public function refreshToken($uid);

    /**
     * 校验Token是否合法
     *
     * @param string $token
     * @return mixed|array
     * @throws ForbiddenException
     * @throws TokenException
     */
    //abstract public function verifyToken($token = '');

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
        if (!is_array($token)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        if (!array_key_exists('uid', $token)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        if (!array_key_exists('scope', $token)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        $this->verifyNonce($token); // 仅判断

        return $token;
    }

    /**
     * 封装Token值
     *
     * @param $account
     * @return mixed
     */
    abstract protected function prepareTokenValue($account);

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
            $payload['nonce'] = createUniqidNonceStr();
        }
        // 过期时间 = 当前请求时间 + token过期时间
        $payload['exp'] = $_SERVER['REQUEST_TIME'] + $this->expires_in;
        $jwt = JWT::encode($payload, $this->secret_key); // 生成jwt

        return $jwt;
    }

    /**
     * 检查JWT是否有效并有效则返回Token payload数组
     *
     * @param string $token
     * @return mixed|array
     * @throws TokenException
     */
    protected function verifyJWT($token = '')
    {
        if (!$token) {
            $token = Request::instance()
                ->header('token');
            if (empty($token)) {
                throw new ParamsException([
                    'msg' => 'Token不能为空'
                ]);
            }
        }

        try {

            $jwt = JWT::decode($token, $this->secret_key, array('HS256'));

            if (empty($jwt)) {
                throw new TokenException();
            }

            $jwt = (array)$jwt;

        } catch (ExpiredException $e) {
            throw new TokenException([
                'msg' => 'Token已过期或无效Token'
            ]);
        } catch (\UnexpectedValueException $e) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        } catch (SignatureInvalidException $e) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        } catch (BeforeValidException $e) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        return $jwt;
    }

    /**
     * 从token中取出相应的key对应的值
     *
     * @param string $key
     * @return mixed|object
     * @throws TokenException
     */
    public function getCurrentTokenByKey($key = '')
    {
        $tokenValues = $this->verifyJWT();

        if (empty($tokenValues)) {
            throw new TokenException();
        }

        if ($key) {
            if (array_key_exists($key, $tokenValues)) {
                return $tokenValues[$key];
            } else {
                throw new TokenException('未找到相应的Token');
            }
        } else {
            return $tokenValues;
        }

    }

    /**
     * 校验nonce
     * 先判断传入数组是否存在nonce，不存在则直接抛出错误
     * 存在则去缓存中查找是否存在，存在表示该nonce已经使用过，抛出错误
     *
     * @param array $jwt jwt体的参数
     * @param bool $use 使用标识
     * @return bool
     * @throws TokenException
     */
    protected function verifyNonce($jwt = [], $use = false)
    {
        if (!$this->need_nonce) {
            return true;
        }

        if (!is_array($jwt)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        // 验证随机字符串
        if (!array_key_exists('nonce', $jwt)) {
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        }

        $nonceStr = $jwt['nonce'];
        if (Cache::has($nonceStr)) { // 存在表示已用过
            throw new TokenException([
                'msg' => '无效Token'
            ]);
        } else {
            if ($use) {
                // 标识已使用
                $result = Cache::set($nonceStr, 1, $this->expires_in);
                if (!$result) {
                    throw new \Exception('服务器缓存异常');
                }
            }
        }

        return true;
    }
}

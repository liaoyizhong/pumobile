<?php
namespace app\common\logic;

use Firebase\JWT\JWT;
/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 14:48
 */

abstract class BasicLogic
{
    private $need_nonce = true;

    const TOKENPRE = 'PuMobile';

    // 是否需要随机字符串
    // Token有效时间
    private $expires_in = 3600;
    // Token加密串
    private $secret_key = 'XiaoPuJia';

    /**
     * @param $id
     * @return ResidencesModel|null
     */
    public function get($id)
    {
        $className = get_called_class();
        $className = str_replace('logic','model',$className);
        $className = preg_replace('/Logic$/','Model',$className);
        if(!class_exists($className)){
            return FALSE;
        }
        return $className::get(["id"=>$id,"is_delete"=>2]);
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteModel($id)
    {
        $model = $this->get($id);
        if (!$model) {
            return [FALSE, '查找的数据不存'];
        }
        if ($model->is_delete == 1) {
            return [FALSE, '数据已经被删除过'];
        }
        if ($model->save(["is_delete" => 1])) {
            return [TRUE, '刪除成功'];
        } else {
            return [FALSE, '删除失败'];
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
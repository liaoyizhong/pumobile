<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/8/16 09:39
 * description : 通用错误处理类
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    // HTTP 状态码
    protected $code = 400;
    // 错误消息
    protected $msg = 'BAD REQUEST OR INVALID PARAMETERS';
    // 错误码
    protected $errorCode = 1000;

    /**
     * 确保传递进来的错误体包含以下三个属性，且可支持仅覆盖其中一个属性：
     * code -
     * msg -
     * errorCode -
     *
     * BaseException constructor.
     * @param array $params
     */
    public function __construct($params = [])
    {
        if (!is_array($params)) {
            return false;
        }

        if (array_key_exists('code', $params)) {
            $this->code = isPositiveInteger($params['code']) ? $params['code'] : $this->code;
        }

        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }

        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = isPositiveInteger($params['errorCode']) ? $params['errorCode'] : $this->errorCode;
        }
    }

    public function __set($name, $value)
    {
        if (!in_array($name, ['code', 'msg', 'errorCode'])) {
            throw new Exception('Params Exception');
        }

        if (in_array($name, ['code', 'errorCode'])) {
            // code & errorCode 必须是正整数
            if (!isPositiveInteger($value)) {
                throw new Exception('Params Exception');
            }
        }

        $this->$name = $value;
    }

    public function __get($name)
    {
        if (!in_array($name, ['code', 'msg', 'errorCode'])) {
            throw new Exception('Params Exception');
        }

        return $this->$name;
    }
}
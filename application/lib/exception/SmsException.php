<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/9/5 23:28
 * description :
 */

namespace app\lib\exception;


class SmsException extends BaseException
{
    // HTTP 状态码
    protected $code = 400;
    // 错误消息
    protected $msg = '发送信息失败';
    // 错误码
    protected $errorCode = 1014;
}
<?php
namespace app\common\enums;
/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 16:36
 */

class ResponseCode
{
    const SUCCESS = 0; //成功

    const PARAMS_MISS = 1001; //缺少参数
    const PARAMS_INVALID = 1002; //无效参数

    const LOGIC_ERROR = 2001; //逻辑出错

    const UNKNOW_ERROR = 3001; //未知错误

    const DATA_MISS = 4001; //查不到数据
    const DATA_ERROR = 4002; //数据执行出错

    const CLASS_EXISTS = 5001; //类不存在
}
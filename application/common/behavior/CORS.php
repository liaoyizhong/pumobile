<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/9/1 11:27
 * description :
 */

namespace app\common\behavior;


class CORS
{
    // 处理跨域问题 ps: 容易产生安全问题，需要确认是否所有来源都可访问接口
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET,DELETE,PUT');
        if(request()->isOptions()){
            exit();
        }
    }
}
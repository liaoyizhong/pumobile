<?php

namespace app\home\logic;

use app\common\logic\BasicLogic;
use app\home\controller\WxUser;
use app\home\model\WxUserModel;

/**
 * User: liaoyizhong
 * Date: 2017/12/18/018
 * Time: 17:29
 */
class WxUserLogic extends BasicLogic
{
    public function save($params)
    {
        $model = new WxUserModel();
        $model->open_id = $params['open_id'];
        $model->union_id = isset($params['union_id']) ? $params['union_id'] : "";
        $time = date("Y-m-d H:i:s", time());
        $model->createtime = $time;
        $model->updatetime = $time;
        if ($model->save()) {
            return [TRUE, '成功'];
        } else {
            return [FALSE, '失败'];
        }
    }

    public function getRow($params)
    {
        $model = new WxUserModel();
        if(isset($params['open_id']) && $params['open_id']){
            $model->where('open_id',$params['open_id']);
        }
        if($params['union_id'] && $params['union_id']){
            $model->where('union_id',$params['union_id']);
        }
        $model->findOrFail();
    }
}
<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:40
 */

namespace app\customer\controller;

use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;

class CustomerRecord extends Basic
{
    public function save()
    {
        $json = file_get_contents("php://input");
        $params = json_decode($json, true);

        $check = $this->validate($params,'CustomerRcordValidate');
        if($check !== TRUE){
            $this->showResponse(ResponseCode::PARAMS_INVALID,$check,[],array('status'=>HeaderStatus::BADREQUEST));
        }
        $logic = \think\Loader::model('CustomerRecordLogic','logic');
        $logic->save($params);
    }
}
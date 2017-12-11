<?php
namespace app\customer\controller;
use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;

/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 16:36
 */

class Customer extends Basic
{
    public function save()
    {
        $json = file_get_contents("php://input");
        $params = json_decode($json, true);
        $check = $this->validate($params,'CustomerValidate');
        if($check !== TRUE) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $check, [], array("status" => HeaderStatus::BADREQUEST));
        }
        $logic = \think\Loader::model('\app\residences\logic\ResidencesDesignLogic','logic');
        $result = $logic->checkExists($params['design_id'],$params['residences_id']);
        if(!$result){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '户型与楼盘不对应',[],array("status"=>HeaderStatus::BADREQUEST));
        }
        $logic = \think\Loader::model('CustomerLogic','logic');
        $result = $logic->save($params);
        if($result[0]){
            return $this->showResponse(ResponseCode::SUCCESS,$result[1],[],array("status" => HeaderStatus::SUCCESS));
        }else{
            return $this->showResponse(ResponseCode::DATA_ERROR,$result[1],[],array("status" => HeaderStatus::UNPROCESABLEENTITY));
        }

    }

    public function index()
    {

    }

    public function read($id)
    {

    }

    public function delete($id)
    {
        $logic = \think\Loader::model('CustomerLogic','logic');
        $result = $logic->delete($id);

    }


}
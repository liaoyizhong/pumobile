<?php
namespace app\customers\controller;
use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use think\Loader;

/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 16:36
 */

class Customers extends Basic
{
    protected $beforeActionList = [
        'checkManagerLogin' => ['only'=>'save,index'],
        'checkCustmoerLogin' => ['only'=>'listProcess']
    ];
    /**
     * @return \think\response\Json
     */
    public function save()
    {
        $params = $this->getParams(self::METHODPOST);
        $check = $this->validate($params,'CustomerValidate');
        if($check !== TRUE) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $check, [], array("status" => HeaderStatus::BADREQUEST));
        }
        $logic = Loader::model('\app\residences\logic\ResidencesDesignLogic','logic');
        $result = $logic->checkExists($params['design_id'],$params['residences_id']);
        if(!$result){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '户型与楼盘不对应',[],array("status"=>HeaderStatus::BADREQUEST));
        }
        $customersLogic = Loader::model('CustomersLogic','logic');
        $result = $customersLogic->save($params);
        if($result[0]){
            return $this->showResponse(ResponseCode::SUCCESS,$result[1],[],array("status" => HeaderStatus::SUCCESS));
        }else{
            return $this->showResponse(ResponseCode::DATA_ERROR,$result[1],[],array("status" => HeaderStatus::UNPROCESABLEENTITY));
        }

    }

    /**
     *   后台我的客户-列表
     * * @return \think\response\Json
     */
    public function index()
    {
        $params['size'] = $this->request->get('size','10');
        $params['page'] = $this->request->get('page','1');
        $params['manager_id'] = $this->userId;
        $logic = Loader::model('CustomersLogic','logic');
        try{
            $lists = $logic->customerList($params);
        }catch (\exception $e){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,'读取失败',[],array('status'=>HeaderStatus::BADREQUEST));
        }
        return $this->showResponse(ResponseCode::SUCCESS,'读取成功',$lists,array('status'=>HeaderStatus::SUCCESS));
    }

    public function listProcess()
    {
        $params['phone'] = $this->phone;
        $logic = Loader::model('CustomersLogic','logic');

        try{
            $result = $logic->listByResidences($params);
        }catch (\exception $e){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,'读取失败',[],array('status'=>HeaderStatus::BADREQUEST));
        }
        return $this->showResponse(ResponseCode::SUCCESS,'读取成功',$result,array('status'=>HeaderStatus::SUCCESS));
    }

    public function read($id)
    {

    }

    public function delete($id)
    {
        $logic = Loader::model('CustomersLogic','logic');
        $result = $logic->delete($id);
        if($result[0]){
            return $this->showResponse(ResponseCode::SUCCESS,$result[1],'',array('status'=>HeaderStatus::SUCCESS));
        }else{
            return $this->showResponse(ResponseCode::LOGIC_ERROR,$result[1],'',array('status'=>HeaderStatus::BADREQUEST));
        }
    }


}
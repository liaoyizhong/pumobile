<?php

namespace app\customers\controller;

use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use app\common\logic\ErrorLogLogic;
use app\customers\model\CustomersModel;
use think\Loader;

/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 16:36
 */
class Customers extends Basic
{
    protected $beforeActionList = [
        'checkManagerLogin' => ['only' => 'save,index,read,update'],
        'checkCustmoerLogin' => ['only' => 'listProcess']
    ];

    /**
     * @return \think\response\Json
     */
    public function save()
    {
        $params = $this->getParams(self::METHODPOST);
        $check = $this->validate($params, 'CustomerValidate');
        if ($check !== TRUE) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, $check, [], array("status" => HeaderStatus::BADREQUEST));
        }
        $logic = Loader::model('\app\residences\service\ResidencesDesignService', 'service');
        $result = $logic->checkExists($params['design_id'], $params['residences_id']);
        if (!$result) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '户型与楼盘不对应', [], array("status" => HeaderStatus::BADREQUEST));
        }
        $customersLogic = Loader::model('CustomersLogic', 'logic');
        $result = $customersLogic->save($params);
        if ($result[0]) {
            return $this->showResponse(ResponseCode::SUCCESS, $result[1], [], array("status" => HeaderStatus::SUCCESS));
        } else {
            return $this->showResponse(ResponseCode::DATA_ERROR, $result[1], [], array("status" => HeaderStatus::UNPROCESABLEENTITY));
        }

    }

    public function update($id)
    {
        $params = $this->getParams(self::METHODPUT);
        $params['id'] = $id;
        $logic = Loader::model('CustomersLogic','logic');
        $result = $logic->update($params);
        if($result[0]){
            return $this->showResponse(ResponseCode::SUCCESS,$result[1],['id'=>$result[2]],array('status'=>HeaderStatus::SUCCESS));
        }else{
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,$result[1],'',array('stauts'=>HeaderStatus::BADREQUEST));
        }
    }

    /**
     *   后台我的客户-列表
     * * @return \think\response\Json
     */
    public function index()
    {
        $params['size'] = $this->request->get('size', '10');
        $params['page'] = $this->request->get('page', '1');
        $params['manager_id'] = $this->userId;
        $logic = Loader::model('CustomersLogic', 'logic');
        try {
            $lists = $logic->customerList($params);
        } catch (\exception $e) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '读取失败', [], array('status' => HeaderStatus::BADREQUEST));
        }
        return $this->showResponse(ResponseCode::SUCCESS, '读取成功', $lists, array('status' => HeaderStatus::SUCCESS));
    }

    /**
     * 我家进度业主视觉
     * @param string $id
     * @return \think\response\Json
     */
    public function listProcess($id = '')
    {
        $params['phone'] = isset($this->phone) ? $this->phone : ""; //验证的地方进行了获取和赋值
        $params['id'] = $id;
        return $this->process($params);
    }

    /**
     * 邻居进度视觉
     * @param string $id
     * @return \think\response\Json
     */
    public function listProcessNeighbor($id = '')
    {
        $params['id'] = $id;
        $params['neighbor'] = 1;
        return $this->process($params);
    }

    /**
     * 客户信息
     * @param $id
     * @return \think\response\Json
     */
    public function read($id)
    {
        try {
            $logic = Loader::model('CustomersLogic', 'logic');
            $result = $logic->detail($id);
            if($result[0]){
                return $this->showResponse(ResponseCode::SUCCESS, '', $result[1], array('status' => HeaderStatus::SUCCESS));
            }else{
                return $this->showResponse(ResponseCode::SUCCESS, $result[2], [], array('status' => HeaderStatus::SUCCESS));
            }
        } catch (\Exception $e) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '', [], array('status' => HeaderStatus::BADREQUEST));
        }
    }

    public function delete($id)
    {
        $logic = Loader::model('CustomersLogic', 'logic');
        $result = $logic->delete($id);
        if ($result[0]) {
            return $this->showResponse(ResponseCode::SUCCESS, $result[1], '', array('status' => HeaderStatus::SUCCESS));
        } else {
            return $this->showResponse(ResponseCode::LOGIC_ERROR, $result[1], '', array('status' => HeaderStatus::BADREQUEST));
        }
    }

    /**
     * @param $params
     * @return \think\response\Json
     */
    protected function process($params)
    {
        $logic = Loader::model('CustomersLogic', 'logic');
        
        try {
            if(isset($params['neighbor']) && $params['neighbor']){
                $result = $logic->listByNeighbor($params);
            }else{
                $result = $logic->listByProcess($params);
            }
        } catch (\exception $e) {
            ErrorLogLogic::save($e->getMessage());
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '读取失败', [], array('status' => HeaderStatus::BADREQUEST));
        }
        return $this->showResponse(ResponseCode::SUCCESS, '读取成功', $result, array('status' => HeaderStatus::SUCCESS));
    }


}
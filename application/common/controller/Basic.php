<?php

namespace app\common\controller;

use app\common\enums\HeaderStatus;
use app\common\enums\LoginRole;
use app\common\enums\ResponseCode;
use app\common\enums\ResponseVersion;
use app\managers\model\ManagersModel;
use think\Cache;
use \think\Controller as thinkController;
use think\Loader;

/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 11:46
 */
abstract class Basic extends thinkController
{
    protected $userId;
    protected $role;
    protected $phone;

    const METHODPOST = 'post';
    const METHODPUT = 'put';
    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @param array $header   配置参数
     *                            status:header返回状态,
     *                            version:版本信息,
     * @return \think\response\Json
     */
    public function showResponse($code = ResponseCode::SUCCESS, $message = '', $data = array(), $header = array())
    {
        if(isset($data['list'])){
            $array['err_code'] = $code;
            $array['msg'] = $message;
            $array["total"] = isset($data['total'])?$data['total']:"";
            $array["per_page"] = isset($data['per_page'])?$data['per_page']:"";
            $array["current_page"] = isset($data['current_page'])?$data['current_page']:"";
            $array["last_page"] = isset($data['last_page'])?$data['last_page']:"";
            $array['data'] = $data['list'];
        }elseif(isset($data['token'])){
            $array = [
                'err_code' => ResponseCode::SUCCESS,
                'msg' => $message,
                'token' => $data['token'],
                'data' => []
            ];
        }else{
            $array = [
                'err_code' => $code,
                'msg' => $message,
                'data' => $data
            ];
        }

        $array['request_url'] = $_SERVER['REQUEST_URI'];

        if(isset($header['status'])){
            $status = $header['status'];
            unset($header['status']);
        }else{
            $status = 200;
        }
        if(!isset($header['version'])){
            $header['version'] = ResponseVersion::V1;
        }

        return json($array,$status,$header);
    }

    protected function _initialize()
    {
        $this->adminToken = Loader::model('AdminToken','service');
    }


    /**
     *  检查manager登录
     */
    public function checkManagerLogin()
    {
        if (!isset($_SERVER['HTTP_TOKEN'])) {
            $array = [
                'err_code' => ResponseCode::PARAMS_MISS,
                'msg' => '缺token',
                'data' => []
            ];
            http_response_code(HeaderStatus::FORBIDDEN);
            echo json_encode($array,true);exit;
        }
        
        $userInfo = Cache::get($_SERVER['HTTP_TOKEN']);
        if(!$userInfo){
            $array = [
                'err_code' => ResponseCode::DATA_MISS,
                'msg' => '没有登录',
                'data' => []
            ];
            http_response_code(HeaderStatus::UNAUTHORIZED);
            echo json_encode($array,true);exit;
        }

        $this->userId = $userInfo['user_id'];
        $this->role = $userInfo['role'];

        if($this->role != LoginRole::ROLEMANAGER){
            $array = [
                'err_code' => ResponseCode::DATA_MISS,
                'msg' => '没有权限',
                'data' => []
            ];
            http_response_code(HeaderStatus::UNAUTHORIZED);
            echo json_encode($array,true);exit;
        }

        if (!$this->userId) {
            $array = [
                'err_code' => ResponseCode::DATA_MISS,
                'msg' => '无效token',
                'data' => []
            ];
            http_response_code(HeaderStatus::FORBIDDEN);
            echo json_encode($array,true);exit;
        }
    }

    /**
     *  检查customer登录
     */
    public function checkCustmoerLogin()
    {
        if (!isset($_SERVER['HTTP_TOKEN'])) {
            $array = [
                'err_code' => ResponseCode::PARAMS_MISS,
                'msg' => '缺token',
                'data' => []
            ];
            http_response_code(HeaderStatus::FORBIDDEN);
            echo json_encode($array,true);exit;
        }

        $arr = Cache::get($_SERVER['HTTP_TOKEN']);
        $this->userId = $arr['user_id'];
        $this->role = $arr['role'];
        $this->phone = $arr['phone'];
        if($this->role != LoginRole::ROLECUSTOMER){
            $array = [
                'err_code' => ResponseCode::DATA_MISS,
                'msg' => '没有权限',
                'data' => []
            ];
            http_response_code(HeaderStatus::UNAUTHORIZED);
            echo json_encode($array,true);exit;
        }
        if (!$this->userId) {
            $array = [
                'err_code' => ResponseCode::DATA_MISS,
                'msg' => '无效token',
                'data' => []
            ];
            http_response_code(HeaderStatus::FORBIDDEN);
            echo json_encode($array,true);exit;
        }
    }

    public function getParams($method)
    {
        switch($method){
            case self::METHODPOST:
                $json = file_get_contents("php://input");
                $params = json_decode($json, true);
                return $params;
            case self::METHODPUT:
                return $this->request->put();
        }
    }
}
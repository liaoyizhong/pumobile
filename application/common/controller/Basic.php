<?php

namespace app\common\controller;

use app\common\enums\ResponseCode;
use app\common\enums\ResponseVersion;
use \think\Controller as thinkController;

/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 11:46
 */
abstract class Basic extends thinkController
{
    private $userId;

    public function showResponse($code = ResponseCode::SUCCESS, $message = '', $data = array(),$header = array())
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

    /**
     *  检查登录
     */
    public function checkLogin()
    {
        if (!isset($_SERVER['HTTP_TOKEN'])) {
            $array = [
                'err_code' => ResponseCode::PARAMS_MISS,
                'msg' => '缺token',
                'data' => []
            ];
            echo json_encode($array,true);exit;
        }
        $redis = new \think\cache\driver\Redis();
        $this->userId = $redis->get($_SERVER['HTTP_TOKEN']);
        if (!$this->userId) {
            $array = [
                'err_code' => ResponseCode::DATA_MISS,
                'msg' => '无效token',
                'data' => []
            ];
            echo json_encode($array,true);exit;
        }
    }
}
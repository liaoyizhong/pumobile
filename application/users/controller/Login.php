<?php

namespace app\users\controller;

use \app\common\controller\Basic as BasicController;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use app\common\enums\ResponseVersion;
use think\Request;

class login extends BasicController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     * 登录
     */
    public function save()
    {
        $json = file_get_contents("php://input");
        $params = json_decode($json, true);
        $result = $this->validate($params,'User');
        if($result !== TRUE){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,$result,'',array('status'=> HeaderStatus::BADREQUEST,'version'=>ResponseVersion::V1));
        }
        $service = \think\Loader::model('\app\users\logic\UsersLogic','logic');
        $model = $service->checkLogin($params);
        if($model[0]){
            return $this->showResponse(ResponseCode::SUCCESS,$model[1],$model[2],array('status'=> HeaderStatus::SUCCESS,'version'=>ResponseVersion::V1));
        }else{
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,$model[1],'',array('status'=> $model[3],'version'=>ResponseVersion::V1));
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
    
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}

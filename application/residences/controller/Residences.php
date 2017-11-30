<?php

namespace app\residences\controller;

use app\common\controller\Basic as BasicController;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use app\common\enums\ResponseVersion;

class Residences extends BasicController
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $params = [];
        if(isset($_GET['page'])){
            $params['page'] = $_GET['page'];
        }
        if(isset($_GET['size'])){
            $params['size'] = $_GET['size'];
        }
        if(isset($_GET['is_hidden'])){
            $params['where']['is_hidden'] = $_GET['is_hidden'];
        }
        $logic = \think\Loader::model("ResidencesLogic", "logic");
        $model = $logic->listMenu($params);
        if (!$model) {
            return $this->showResponse(ResponseCode::DATA_MISS, '查不到该数据',[],array('status'=>HeaderStatus::NOTFOUND));
        }

        return $this->showResponse(ResponseCode::SUCCESS, '', $model);
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
     * 保存新建的资源
     *
     * @return \think\Response
     */
    public function save()
    {
        $json = file_get_contents("php://input");
        $params = json_decode($json, true);
        if(!isset($params['main']['name']) || !isset($params['main']['region_id']) || !isset($params['main']['address']))
        {
            return $this->showResponse(ResponseCode::PARAMS_MISS, '缺必要参数',[],array("status"=>HeaderStatus::BADREQUEST,"version"=>ResponseVersion::V1));
        }
        return $this->execSave($params);
    }

    /**
     * 显示指定的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $logic = \think\Loader::model('\app\residences\logic\ResidencesLogic', "logic");
        $model = $logic->get($id);
        if (!$model) {
            return $this->showResponse(ResponseCode::DATA_MISS, '查不到该数据',[],array("status"=>HeaderStatus::NOTFOUND));
        }
        $data = $logic->getRow($model);
        return $this->showResponse(ResponseCode::SUCCESS, '', $data);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {

    }

    /**
     * 保存更新的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $json = file_get_contents("php://input");
        $params = json_decode($json, true);
        $params['main']['id'] = $id;
        return $this->execSave($params);
    }

    public function hide($id,$ishide)
    {
        $logic = \think\Loader::model('ResidencesLogic','logic');
        $params['id'] = $id;
        $params['is_hidden'] = $ishide;
        $result = $logic->changeIsHidden($params);
        if($result[0]){
            return $this->showResponse(ResponseCode::SUCCESS, $result[1],[],array('status'=>HeaderStatus::SUCCESS));
        }else{
            return $this->showResponse(ResponseCode::SUCCESS, $result[1],[],array('status'=>HeaderStatus::BADREQUEST));
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $logic = \think\Loader::model('\app\residences\logic\ResidencesLogic', "logic");
        $result = $logic->deleteModel($id);
        if ($result[0]) {
            return $this->showResponse(ResponseCode::SUCCESS, $result[1],[],array('status'=>HeaderStatus::NOCONTENT));
        } else {
            return $this->showResponse(ResponseCode::DATA_ERROR, $result[1],[],array('status'=>HeaderStatus::INTERNALSERVERERROR));
        }
    }

    /**
     * @param $params
     */
    protected function execSave($params)
    {
        //开启事务
        \think\Db::startTrans();
        try {
            $logic = \think\Loader::model('\app\residences\logic\ResidencesLogic', "logic");
            $result = $logic->saveModel($params['main']);
            if(!$result[0]){
                \think\Db::rollback();
                return $this->showResponse($result[2],$result[1],[],array("status"=>HeaderStatus::NOTFOUND));
            }
            //保存设计方案
            if (isset($params['design']) && count($params['design'])) {
                if(isset($params['main']['id'])){
                    //先清空原来的设计方案
                    $logic->deleteDesigns();
                }
                foreach ($params['design'] as $key => $item) {
                    $designParams[$key] = $item;
                    $designParams[$key]['residences_id'] = $result[1];
                }
                $designService = \think\Loader::model('\app\residences\logic\ResidencesDesignLogic', "logic");
                if ($designService->saveAll($designParams)) {
                    \think\Db::commit();
                    return $this->showResponse(ResponseCode::SUCCESS, '保存成功',['id'=>$result[1]],array("status"=>HeaderStatus::CREATED));
                } else {
                    \think\Db::rollback();
                    return $this->showResponse(ResponseCode::DATA_ERROR, '保存失败',[],array("status"=>HeaderStatus::UNPROCESABLEENTITY));
                }
            } else {
                \think\Db::commit();
                return $this->showResponse(ResponseCode::SUCCESS, '保存成功',['id'=>$result[1]],array("status"=>HeaderStatus::CREATED));
            }
        }catch (\exception $e){
            \think\Db::rollback();
            return $this->showResponse(ResponseCode::DATA_ERROR, '保存失败',[],array("status"=>HeaderStatus::INTERNALSERVERERROR));
        }
    }
}

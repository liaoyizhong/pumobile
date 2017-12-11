<?php
namespace app\customer\logic;
use app\common\logic\BasicLogic;
use app\customer\model\CustomerModel;

/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 15:53
 */

class CustomerLogic extends BasicLogic
{
    private $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function save($params)
    {
        try{
            $model = new CustomerModel();
            $model->region_id = $params['region_id'];
            $model->residences_id = $params['residences_id'];
            $model->design_id = $params['design_id'];
            $model->house_num = $params['house_num'];
            $model->family_name = $params['family_name'];
            $model->name = $params['name'];
            $model->sex = $params['sex'];
            $model->starttime = $params['starttime'];
            $model->endtime = $params['endtime'];
            $time = date("Y-m-d H:i:s");
            $model->createtime = $time;
            $model->updatetime = $time;
            $model->save();
            return [TRUE,'保存成功'];
        }catch (\Exception $e){
            return [FALSE, $e->getMessage()];
        }
    }

    public function update($params)
    {
        $model = $this->get($this->id);
        if($model){
            return [FALSE,'查不到数据'];
        }
        if(isset($params['region_id']))$model->region_id = $params['region_id'];
        if(isset($params['design_id']))$model->design_id = $params['design_id'];
        if(isset($params['house_num']))$model->house_num = $params['house_num'];
        if(isset($params['family_name']))$model->family_name = $params['family_name'];
        if(isset($params['name']))$model->name = $params['name'];
        if(isset($params['sex']))$model->sex = $params['sex'];
        if(isset($params['starttime']))$model->starttime = $params['starttime'];
        if(isset($params['endtime']))$model->endtime = $params['endtime'];
        $time = date("Y-m-d H:i:s");
        $model->endtime = $time;
    }

    public function delete($id)
    {
        $model = $this->get($id);
        $model->is_delete = 1;
        return $model->save();
    }

    public function index()
    {

    }

    public function read($id)
    {
        $this->get($id);
    }


}
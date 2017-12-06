<?php
namespace app\customers\logic;
use app\common\logic\BasicLogic;
use app\customers\model\CustomersModel;
use app\customers\model\CustomersRecordsModel;

/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 15:53
 */

class CustomersLogic extends BasicLogic
{
    private $id;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function save($params)
    {
        try{
            $model = new CustomersModel();
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
        $recordModel = new CustomersRecordsModel();
        $count = $recordModel->where('customers_id',$id)->where('is_delete','2')->count();
        if($count){
            return [FALSE,'客户下有直播信息，不能删除'];
        }
        $model = $this->get($id);
        $model->is_delete = 1;
        if($model->save()){
            return [TRUE, '删除成功'];
        }else{
            return [FALSE,'删除失败'];
        }
    }

    public function listModels($params)
    {
        $model = new CustomersModel();
        $model->where('is_delete',2);

        if(isset($params['page']) && isset($params['size']) && $params['page'] && $params['size']){
            $list = $model->paginate($params['size'],'',array('page'=>$params['page']));
        }
        return $list;

    }

    public function customerList($params){
        $list = $this->listModels($params);
        $customerId = '';
        $return = [];
        foreach($list as $key=>$value){
            $records = $value->records;
            if(count($records)){
                $createTime = end($records)->createtime;
                $return[$key]['last_release'] = ceil((time()-strtotime($createTime))/86400);
            }


        }


    }

    public function read($id)
    {
        $this->get($id);
    }


}
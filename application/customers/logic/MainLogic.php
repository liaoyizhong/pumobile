<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/12/012
 * Time: 16:33
 */

namespace app\customers\logic;


use app\common\logic\BasicLogic;
use app\customers\model\CustomersModel;

abstract class MainLogic extends BasicLogic
{
    public function listModels($params = array())
    {
        $model = new CustomersModel();
        $model->where('is_delete',2);

        if(isset($params['manager_id']) && $params['manager_id']){
            $model->where('manager_id',$params['manager_id']);
        }

        if(isset($params['id']) && $params['id']){
            $model->where('id', $params['id']);
        }

        if(isset($params['phone']) && $params['phone']){
            $model->where('phone',$params['phone']);
        }

        if(isset($params['residence_id']) && $params['residence_id']){
            $model->where('residence_id',$params['residence_id']);
        }

        if(isset($params['page']) && isset($params['size']) && $params['page'] && $params['size']){
            $list = $model->paginate($params['size'],'',array('page'=>$params['page']));
        }else{
            $list = $model->select();
        }

        return $list;

    }

    /**
     * 获取我家进度头部导航
     * @return array
     */
    public function getTop()
    {
        $list = $this->listModels();
        $topList = [];
        foreach ($list as $key => $item) {
            $design = $item->desgin;
            $residence = $item->residence;
            $topList[$key]['value'] = $item->id;
            $topList[$key]['label'] = $bodyList[$key]['name'] = $residence->name.$design->ridgepole.'栋'.$design->cell.'单元';
        }
        return $topList;
    }
}
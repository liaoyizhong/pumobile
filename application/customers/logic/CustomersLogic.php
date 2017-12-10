<?php
namespace app\customers\logic;
use app\common\logic\BasicLogic;
use app\customers\model\CustomersModel;
use app\customers\model\CustomersRecordsModel;
use app\images\service\AliService;
use think\Cache;

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
        //获取操作员信息
        $arr = Cache::get($_SERVER['HTTP_TOKEN']);

        try{
            $model = new CustomersModel();
            $model->region_id = $params['region_id'];
            $model->residence_id = $params['residences_id'];
            $model->design_id = $params['design_id'];
            $model->house_num = $params['house_num'];
            $model->family_name = $params['family_name'];
            $model->name = $params['name'];
            $model->sex = $params['sex'];
            $model->starttime = $params['starttime'];
            $model->endtime = $params['endtime'];
            $model->phone = $params['phone'];
            $model->manager_id = $arr['user_id'];
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

        if(isset($params['manager_id']) && $params['manager_id']){
            $model->where('manager_id',$params['manager_id']);
        }

        if(isset($params['phone']) && $params['phone']){
            $model->where('phone',$params['phone']);
        }

        if(isset($params['page']) && isset($params['size']) && $params['page'] && $params['size']){
            $list = $model->paginate($params['size'],'',array('page'=>$params['page']));
        }else{
            $list = $model->select();
        }

        return $list;

    }

    /**
     * 后台我的客户-列表
     * @param $params
     * @return array
     */
    public function customerList($params){
        $list = $this->listModels($params);

        $return = [];
        foreach($list as $key=>$value){
            $design = $value->desgin;
            $return[$key]['house_name'] = $design['ridgepole'].'栋'.$design['cell'].'单元'.$design['house_type'];
            $return[$key]['name'] = $value['family_name'].$value['name'];
            $return[$key]['schedule'] = $value->createtime.'-'.$value->endtime;
            $records = $value->records;
            if(count($records)){
                $createTime = end($records)->createtime;
                $return[$key]['last_release_text'] = ceil((time()-strtotime($createTime))/86400).'天前';
            }else{
                $return[$key]['last_release_text'] = '还没有直播内容';
            }
        }
        
        return $return;
    }

    public function listByResidences($params){
        $list = $this->listModels($params);
        $AliService = new AliService();
        $topList = $bodyList = [];
        $time = time();
        foreach($list as $key=>$value){
            $design = $value->desgin;
            $residence = $value->residence;
            $records = $value->records;

            $topList[$key][] = $bodyList[$key]['name'] = $residence->name.$design->ridgepole.'栋'.$design->cell.'单元';
            $bodyList[$key]['starttime_text'] = date("m-d",strtotime($value->starttime)).'开工';
            $bodyList[$key]['endtime_text'] = date("m-d",strtotime($value->endtime)).'验收';
            $bodyList[$key]['process_text'] = '第'.ceil(($time - strtotime($value->starttime)) /86400).'天 水电安装';

            $bodyList[$key]['record']= [];
            foreach($records as $rekey=>$item){
                $images = $item->images;
                $bodyList[$key]['record'][$rekey]['content'] = $item['content'];
                $bodyList[$key]['record'][$rekey]['images'] = [];
                foreach($images as $imKey=>$imValue){
                    $bodyList[$key]['record'][$rekey]['images'][] = $AliService->getUrl($imValue['image_hash_code']);
                }
            }
        }
        return ['top'=>$topList,'body'=>$bodyList];
    }

    public function read($id)
    {
        $this->get($id);
    }


}
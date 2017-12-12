<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:30
 */

namespace app\customers\logic;

use app\common\logic\BasicLogic;
use app\customers\model\CustomersRecordsImagesModel;
use app\customers\model\CustomersRecordsModel;
use app\residences\model\ResidencesDesignImagesModel;

class CustomersRecordsLogic extends MainLogic
{
    public function save($params)
    {
        $model = new CustomersRecordsModel();
        $model->customers_id = $params['customers_id'];
        $model->content = $params['content'];
        return $model->save();
    }

    /**
     * 装修直播
     * @param $params
     * @return array
     */
    public function listByResidence($params)
    {
        $topList = $this->getTop();
        $bodyList = [];

        $model = new CustomersRecordsModel();
        $list = $model->listByResidence($params);
        $design_url = isset(current($list)['design_url'])?current($list)['design_url']:"";
        $ids = array_column($list, 'id');
        $imageModel = new CustomersRecordsImagesModel();
        $imageList = $imageModel->whereIn('customers_records_id',$ids)->select();

        foreach ($list as $key=>$item){
            $bodyList[$key]['content'] = $item['content'];
            $bodyList[$key]['name'] = $item['name'];
            $bodyList[$key]['lastTime'] = $item['createtime'];
            $bodyList[$key]['image_hash_code'] = [];
            foreach ($imageList as $imKey=>$imitem){
                if($imitem->customers_records_id == $item['id']){
                    $bodyList[$key]['image_hash_code'][] = $imitem->image_hash_code;
                }
            }
        }
        return ['top'=>$topList,'design_url'=>$design_url,'body'=>$bodyList];
    }

    /**
     * @param $residences_design_id
     * @return string
     */
    public function getDesignImage($residences_design_id)
    {
        $aliService = \think\Loader::model('\app\images\service\AliService','logic');
        $model = new ResidencesDesignImagesModel();
        $result = $model->where('residences_design_id',$residences_design_id)->where('type',2)->find();
        if($result){
            $url = $aliService->getUrl($result->image_hash_code);
        }else{
            $url = '';
        }
        return $url;
    }
}
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
use think\Loader;

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
        $design_url = isset(current($list)['design_url']) ? current($list)['design_url'] : "";
        $ids = array_column($list, 'id');
        $imageModel = new CustomersRecordsImagesModel();
        $imageList = $imageModel->whereIn('customers_records_id', $ids)->select();

        foreach ($list as $key => $item) {
            $bodyList[$key]['content'] = $item['content'];
            $bodyList[$key]['name'] = $item['name'];
            $bodyList[$key]['lastTime'] = $item['createtime'];
            $bodyList[$key]['image_hash_code'] = [];
            foreach ($imageList as $imKey => $imitem) {
                if ($imitem->customers_records_id == $item['id']) {
                    $bodyList[$key]['image_hash_code'][] = $imitem->image_hash_code;
                }
            }
        }
        return ['top' => $topList, 'design_url' => $design_url, 'body' => $bodyList];
    }

    /**
     * 根据户型设计取出一张设计图片
     * @param $residences_design_id
     * @return string
     */
    public function getDesignImage($residences_design_id)
    {
        $aliService = \think\Loader::model('\app\images\service\AliService', 'logic');
        $model = new ResidencesDesignImagesModel();
        $result = $model->where('residences_design_id', $residences_design_id)->where('type', 2)->find();
        if ($result) {
            $url = $aliService->getUrl($result->image_hash_code);
        } else {
            $url = '';
        }
        return $url;
    }

    /**
     * 查找指定id 的customers
     * @param $id
     * @return array
     */
    public function read($id)
    {
        $aliService = Loader::model('\app\images\service\AliService','service');
        $result = [];
        $model = $this->get($id);
        if(!$model){
            return [FALSE,$result];
        }
        $result['content'] = $model->content;
        $customer = $model->customer;
        if($customer){
            $residence = $customer->residence;
            $design = $customer->design;
            $result['name'] = isset($residence->name)?$residence->name:"";
            $result['name'] .= isset($design->ridgepole) && isset($design->cell)?$design->ridgepole.'栋'.$design->cell.'单元':"";
        }else{
            $result['name'] = '';
        }

        $images = $model->images()->where('is_delete',2)->select();
        //先取出有设置order的图片
        $imagesOne = $imagesTwo = [];
        foreach($images as $imKey=>$imValue){
            if($imValue->order_num){
                $imagesOne[$imValue->order_num] = $aliService->getUrl($imValue->image_hash_code);
            }else{
                $imagesTwo[$imKey] = $aliService->getUrl($imValue->image_hash_code);
            }
        }
        sort($imagesOne);
        foreach($imagesTwo as $key=>$item){
            array_push($imagesOne,$item);
        }
        $result['images'] = $imagesOne;
        return $result;
    }
}
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
use app\images\factory\ImagesFactory;
use app\residences\model\ResidencesDesignImagesModel;
use app\residences\model\ResidencesModel;
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
     * 装修直播列表
     * @param $params
     * @return array
     */
    public function listByResidence($params)
    {
        $bodyList = $topList = [];
        $aliService = ImagesFactory::createImage();
        $residenceModel = new ResidencesModel();
        $residenceList = $residenceModel->where('is_delete','2')->select();
        foreach ($residenceList as $key => $item) {
            $topList[$key]['value'] = $item->id;
            $topList[$key]['label'] = $item->name;
        }

        $model = new CustomersRecordsModel();
        $list = $model->listByResidence($params);
        $design_url = "";
        $ids = array_column($list, 'id');
        $imageModel = new CustomersRecordsImagesModel();
        $imageList = $imageModel->whereIn('customers_records_id', $ids)->select();

        $time = time();
        foreach ($list as $key => $item) {
            $bodyList[$key]['customer_name'] = $item['family_name'];
            $bodyList[$key]['customer_name'] .= $item['sex'] == 1 ? "先生":"女士";
            $bodyList[$key]['content'] = $item['content'];
            $bodyList[$key]['customer_id'] = $item['customers_id'];
            $bodyList[$key]['residence_name'] = $item['residence_name'];
            $bodyList[$key]['house_name'] = '';
            $bodyList[$key]['house_name'] .= $item['ridgepole']?$item['ridgepole'].'栋':"";
            $bodyList[$key]['house_name'] .= $item['cell']?$item['cell'].'单元':"";
            $bodyList[$key]['house_name'] .= $item['house_type']?$item['house_type']:"";
            $bodyList[$key]['craetetime'] = $item['createtime'];

            $last = $time - strtotime($item['createtime']);
            if($last<3600){
                $bodyList[$key]['last_time_text'] = round($last/60).'分钟';
            }elseif($last<86400){
                $bodyList[$key]['last_time_text'] = round($last/3600).'小时';
            }else{
                $bodyList[$key]['last_time_text'] = round($last/86400).'天';
            }

            $bodyList[$key]['image_url'] = [];
            foreach ($imageList as $imKey => $imitem) {
                if ($imitem->customers_records_id == $item['id']) {
                    $bodyList[$key]['image_url'][] = $aliService->getUrl($imitem->image_hash_code);
                }
            }
        }
        return ['top' => $topList, 'design_url' => $design_url, 'body' => $bodyList];
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
            $result['name'] .= isset($design->house_type)?$design->house_type:"";
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
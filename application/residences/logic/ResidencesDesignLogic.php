<?php
/**
 * User: liaoyizhong
 * Date: 2017/11/16/016
 * Time: 15:36
 */

namespace app\residences\logic;
use app\common\logic\BasicLogic;
use app\residences\logic\ResidencesDesignImagesLogic as ImagesLogic;
use app\residences\model\ResidencesDesignModel;

class ResidencesDesignLogic extends BasicLogic
{
    public function saveAll($params){
        if(!count($params)){
            return false;
        }

        //这里如果有办法处理关联批量保存的话就可以改变循环的保存方式
        $main = [];
        try{
            foreach ($params as $key=>$item){
                $model = new ResidencesDesignModel();
                $main['ridgepole'] = $item['ridgepole'];
                $main['cell'] = $item['cell'];
                $main['house_type'] = $item['house_type'];
                $main['design_url'] = $item['design_url'];
                $main['residences_id'] = $item['residences_id'];
                if(!$model->save($main)){
                    return FALSE;
                }
                //保存对应的图片
                $designId = $model->getLastInsID();
                $picModel = new ResidencesDesignImagesLogic();
                if(isset($item['pic_hash_codes']) && count($item['pic_hash_codes'])){
                    foreach($item['pic_hash_codes'] as $pKey=>$value){
                        if(isset($value[ImagesLogic::TYPEHOUSETXT])){
                            $imageParams[$pKey]['image_hash_code'] = $value[ImagesLogic::TYPEHOUSETXT];
                            $imageParams[$pKey]['type'] = ImagesLogic::TYPEHOUSE;
                        }elseif(isset($value[ImagesLogic::TYPEDECORATIONTXT])){
                            $imageParams[$pKey]['image_hash_code'] = $value[ImagesLogic::TYPEDECORATIONTXT];
                            $imageParams[$pKey]['type'] = ImagesLogic::TYPEDECORATION;
                        }
                        $imageParams[$pKey]['residences_design_id'] = $designId;
                    }
                    $picModel->saveAll($imageParams);
                }
            }
        }catch (\exception $e){
            return FALSE;
        }
        return TRUE;
    }
}
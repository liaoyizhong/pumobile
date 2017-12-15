<?php

namespace app\customers\logic;

use app\common\logic\BasicLogic;
use app\customers\model\CustomersModel;
use app\customers\model\CustomersRecordsModel;
use app\images\service\AliService;
use think\Cache;
use think\Db;
use think\Loader;

/**
 * User: liaoyizhong
 * Date: 2017/12/1/001
 * Time: 15:53
 */
class CustomersLogic extends MainLogic
{
    public function save($params)
    {
        //获取操作员信息
        $arr = Cache::get($_SERVER['HTTP_TOKEN']);
        if (strtotime($params['endtime']) < strtotime($params['starttime'])) {
            return [FALSE, '结束时间不能比较开始时间早'];
        }
        try {
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
            if ($model->save()) {
                return [TRUE, '保存成功'];
            } else {
                return [FALSE, '保存失败'];
            }
        } catch (\Exception $e) {
            return [FALSE, $e->getMessage()];
        }
    }

    public function update($params)
    {
        try {
            $model = $this->get($params['id']);
            if (!$model) {
                return [FALSE, '查不到数据'];
            }
            //检测开始结束时间
            if (isset($params['starttime']) && isset($params['starttime'])) {
                if (strtotime($params['endtime']) < strtotime($params['starttime'])) {
                    return [FALSE, '结束时间不能比较开始时间早'];
                }
            } elseif (isset($params['starttime']) && !isset($params['starttime'])) {
                if (strtotime($params['endtime']) < strtotime($model->starttime)) {
                    return [FALSE, '结束时间不能比较开始时间早'];
                }
            } elseif (!isset($params['starttime']) && isset($params['starttime'])) {
                if (strtotime($model->endtime) < strtotime($params['starttime'])) {
                    return [FALSE, '结束时间不能比较开始时间早'];
                }
            }

            $check = TRUE;
            $designService = Loader::model('\app\residences\service\ResidencesDesignService','service');
            if(isset($params['design_id']) && isset($params['residences_id'])){
                $check = $designService->checkExists($params['design_id'],$params['residences_id']);
            }elseif(isset($params['design_id']) && !isset($params['residences_id'])){
                $check = $designService->checkExists($params['design_id'],$model->residence_id);
            }elseif(!isset($params['design_id']) && isset($params['residences_id'])){
                $check = $designService->checkExists($model->design_id,$params['residences_id']);
            }
            if(!$check){
                return [FALSE,'户型与楼盘不对应'];
            }

            if (isset($params['region_id'])) $model->region_id = $params['region_id'];
            if (isset($params['design_id'])) $model->design_id = $params['design_id'];
            if (isset($params['residence_id'])) $model->residence_id = $params['residences_id'];
            if (isset($params['house_num'])) $model->house_num = $params['house_num'];
            if (isset($params['family_name'])) $model->family_name = $params['family_name'];
            if (isset($params['name'])) $model->name = $params['name'];
            if (isset($params['sex'])) $model->sex = $params['sex'];
            if (isset($params['starttime'])) $model->starttime = $params['starttime'];
            if (isset($params['endtime'])) $model->endtime = $params['endtime'];
            if (isset($params['phone'])) $model->phone = $params['phone'];
            $time = date("Y-m-d H:i:s");
            $model->updatetime = $time;
            if ($id = $model->save()) {
                return [TRUE, '保存成功', $id];
            } else {
                return [FALSE, '保存失败', ''];
            }
        } catch (\Exception $e) {
            return [FALSE, $e->getMessage(), ''];
        }
    }

    protected function checkProcessTime($start, $end)
    {
        if (strtotime($end) < strtotime($start)) {
            return [FALSE, '结束时间不能比较开始时间早'];
        }
    }

    public function delete($id)
    {
        $recordModel = new CustomersRecordsModel();
        $count = $recordModel->where('customers_id', $id)->where('is_delete', '2')->count();
        if ($count) {
            return [FALSE, '客户下有直播信息，不能删除'];
        }
        $model = $this->get($id);
        $model->is_delete = 1;
        if ($model->save()) {
            return [TRUE, '删除成功'];
        } else {
            return [FALSE, '删除失败'];
        }
    }


    /**
     * 后台我的客户-列表
     * @param $params
     * @return array
     */
    public function customerList($params)
    {
        $list = $this->listModels($params);
        $return = [];
        $time = time();
        foreach ($list as $key => $value) {
            $residence = $value->residence;
            $design = $value->design;
            $return[$key]['id'] = $value->id;
            $return[$key]['house_name'] = isset($residence->name) ? $residence->name : "";
            $return[$key]['house_name'] .= isset($design['ridgepole']) && $design['cell'] ? $design['ridgepole'] . '栋' . $design['cell'] . '单元' . $design['house_type'] : "";
            $return[$key]['name'] = $value['family_name'] . $value['name'];
            $startTimeStamp = strtotime($value->starttime);
            $endTimeStamp = strtotime($value->endtime);
            $return[$key]['schedule'] = date('Y.m.d', $startTimeStamp) . '-' . date('Y.m.d', strtotime($endTimeStamp));
            if ($startTimeStamp > $time) {
                $return[$key]['status'] = CustomersModel::PROCESSBEFORE;
                $return[$key]['status_text'] = '尚未施工';
            } elseif ($startTimeStamp < $time && $endTimeStamp > $time) {
                $return[$key]['status'] = CustomersModel::PROCESSING;
                $return[$key]['status_text'] = '正在施工';
            } else {
                $return[$key]['status'] = CustomersModel::PROCESSAFTER;
                $return[$key]['status_text'] = '施工完成';
            }
            $records = $value->records;
            if (count($records)) {
                $createTime = end($records)->createtime;
                $return[$key]['last_release_text'] = ceil((time() - strtotime($createTime)) / 86400) . '天前';
            } else {
                $return[$key]['last_release_text'] = '还没有直播内容';
            }
        }

        return $return;
    }

    /**
     * 我家进度--业主视觉
     * @param $params
     * @return array
     */
    public function listByProcess($params)
    {
        $bodyList = [];
        $topList = $this->getTop(array("phone" => $params["phone"]));

        $list = $this->listModels($params);

        $AliService = new AliService();
        $time = time();
        foreach ($list as $key => $value) {
            $design = $value->design;
            $residence = $value->residence;
            $records = $value->records;
            $bodyList[$key]['name'] = isset($residence->name) ? $residence->name : "";
            $bodyList[$key]['name'] .= isset($design->ridgepole) && isset($design->cell) ? $design->ridgepole . '栋' . $design->cell . '单元' : "";
            $startTimeStamp = strtotime($value->starttime);
            $endTimeStamp = strtotime($value->endtime);

            $bodyList[$key]['starttime_text'] = date("m.d", $startTimeStamp) . '开工';
            $bodyList[$key]['endtime_text'] = date("m.d", $endTimeStamp) . '验收';

            $bodyList[$key]['time_span'] = ($endTimeStamp - $startTimeStamp) / 86400;
            if ($time < $startTimeStamp) {
                $bodyList[$key]['time_percentage'] = 0;
                $bodyList[$key]['process_text'] = '未开始';
            } elseif ($time > $endTimeStamp) {
                $bodyList[$key]['time_percentage'] = 100;
                $bodyList[$key]['process_text'] = '已完成';
            } else {
                $bodyList[$key]['time_percentage'] = round(((($time - $startTimeStamp) / 86400) / $bodyList[$key]['time_span']) * 100);
                $bodyList[$key]['process_text'] = '第' . ceil(($time - $startTimeStamp) / 86400) . '天';
            }


            $bodyList[$key]['record'] = [];
            foreach ($records as $rekey => $item) {
                $interval = (int)($time - strtotime($item->createtime));
                if ($interval < 3600) {
                    $lastTime = ceil($interval / 60) . '分钟前';
                } elseif ($interval < 86400) {
                    $lastTime = ceil($interval / 3600) . '小时前';
                } else {
                    $lastTime = ceil($interval / 86400) . '天前';
                }
                $images = $item->images;
                $bodyList[$key]['record'][$rekey]['content'] = $item['content'];
                $bodyList[$key]['record'][$rekey]['name'] = isset($residence->name) ? $residence->name : "";
                $bodyList[$key]['record'][$rekey]['lastTime'] = $lastTime;
                $bodyList[$key]['record'][$rekey]['images'] = [];
                foreach ($images as $imKey => $imValue) {
                    $bodyList[$key]['record'][$rekey]['images'][] = $AliService->getUrl($imValue['image_hash_code']);
                }
            }
        }
        return ['top' => $topList, 'body' => $bodyList];
    }

    public function listByNeighbor()
    {

    }

    public function read($id)
    {
        $this->get($id);
    }

    /**
     * 客户信息
     * @param $id
     * @return array|mixed
     */
    public function detail($id)
    {
        $result = [];
        $aliService = Loader::model('\app\images\service\AliService', 'service');
        $model = new CustomersModel();
        $customer = $model->get($id);
        if(!$customer){
            return [FALSE,'找不到信息'];
        }
        $result = $customer->getData();
        if (!$customer) {
            return $result;
        }
        $residence = $customer->residence;
        if(!$residence){
            return [FALSE,'楼盘信息出错'];
        }

        $regionService = Loader::model('\app\region\service\RegionService','service');
        $provinceId = $regionService->switchToProvince($customer->region_id);
        $cityId = $regionService->switchToCity($customer->region_id);
        $result['region_id'] = [$provinceId,$cityId,(string)$customer->region_id];

        $design = $customer->design;
        $result['house_name'] = isset($residence->name) ? $residence->name : '';
        $result['house_name'] .= isset($design->ridgepole) && isset($design->cell) ? $design->ridgepole . '栋' . $design->cell . '单元' : "";

        $time = time();
        $startTimeStamp = strtotime($customer->starttime);
        $endTimeStamp = strtotime($customer->endtime);
        $result['schedule'] = date('Y.m.d', $startTimeStamp) . '-' . date('Y.m.d', $endTimeStamp);
        if ($startTimeStamp > $time) {
            $result['status'] = CustomersModel::PROCESSBEFORE;
            $result['status_text'] = '尚未施工';
        } elseif ($startTimeStamp < $time && $endTimeStamp > $time) {
            $result['status'] = CustomersModel::PROCESSING;
            $result['status_text'] = '正在施工';
        } else {
            $result['status'] = CustomersModel::PROCESSAFTER;
            $result['status_text'] = '施工完成';
        }

        $records = $customer->records;
        if (count($records)) {
            $createTime = end($records)->createtime;
            $result['last_release_text'] = ceil((time() - strtotime($createTime)) / 86400) . '天前';
        } else {
            $result['last_release_text'] = '还没有直播内容';
        }
        $result['sub_text'] = '客户动态';
        $result['records'] = [];
        foreach ($records as $key => $value) {
            $result['records'][$key]['id'] = $value['id'];
            $result['records'][$key]['content'] = $value['content'];
            $result['records'][$key]['createtime'] = $value['createtime'];
            $images = $value->images;
            $result['records'][$key]['image'] = isset(current($images)['image_hash_code']) ? $aliService->getUrl(current($images)['image_hash_code']) : "";
        }

        //带上前段要用的两组数据
        $residencesLogic = \think\Loader::model('\app\residences\logic\ResidencesLogic', "logic");
        $listMenu = $residencesLogic->listMenu($customer->region_id);
        $designLogic = \think\Loader::model('\app\residences\logic\ResidencesLogic','logic');
        $listDesignsMenu = $designLogic->getDesignsMenu($customer->residence_id);

        $result['residence_menu'] = $listMenu;
        $result['design_menu'] = $listDesignsMenu[0]?$listDesignsMenu[2]:[];

        return [TRUE,$result];
    }
}
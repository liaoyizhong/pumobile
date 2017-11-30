<?php

namespace app\region\services;
/**
 * User: liaoyizhong
 * Date: 2017/11/9/009
 * Time: 10:22
 *
 *  region_id为6位，每两位
 */

class RegionService
{
    public function listProvinces()
    {
        $fileAddress = ROOT_PATH . '/application/data/chinaregions/province.json';
        return $this->getFileData($fileAddress);
    }

    public function listCitys()
    {
        $fileAddress = ROOT_PATH . '/application/data/chinaregions/city.json';
        return $this->getFileData($fileAddress);
    }

    public function listAreas()
    {
        $fileAddress = ROOT_PATH . '/application/data/chinaregions/area.json';
        return $this->getFileData($fileAddress);
    }

    /**
     * 用完整的具体到area的regionId转换得到它的province
     * @param $regionId
     * @return string
     */
    public function switchToProvince($regionId)
    {
        $pre = substr($regionId, 0, 2);
        return $pre . '0000';
    }

    /**
     * 转换得到对应的city
     * @param $regionId
     * @return string
     */
    public function switchToCity($regionId)
    {
        $pre = substr($regionId, 0, 4);
        return $pre.'00';
    }

    /**
     * @param $fileAddress
     * @return mixed|string
     */
    protected function getFileData($fileAddress)
    {
        if (!file_exists($fileAddress)) {
            return '';
        }
        $json = file_get_contents($fileAddress);
        $data = json_decode($json, true);
        return $data;
    }

    public function findProvince($id)
    {
        if(!$id){
            return "";
        }
        $pre = substr($id, 0, 2);
        $proId = $pre . '0000';
        $array = $this->listProvinces();
        if(!isset($array[$proId])){
            return "";
        }
        return $array[$proId];
    }

    public function findCity($id)
    {
        if(!$id){
            return "";
        }
        $pre = substr($id, 0, 2);
        $proId = $pre . '0000';
        $pre = substr($id, 0, 4);
        $cityId = $pre.'00';
        $array = $this->listCitys();
        if(!isset($array[$proId][$cityId])){
            return "";
        }
        return $array[$proId][$cityId];
    }

    public function findArea($id)
    {
        if(!$id){
            return "";
        }
        $pre = substr($id, 0, 4);
        $cityId = $pre.'00';
        $array = $this->listAreas();
        if(!isset($array[$cityId][$id])){
            return "";
        }
        return $array[$cityId][$id];
    }

    public function zhongShan()
    {
        $fileAddress = ROOT_PATH . '/application/data/chinaregions/zhongshan.json';
        return $this->getFileData($fileAddress);
    }
}
<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:27
 */

namespace app\customers\model;

use app\common\model\BasicModel;
use think\Db;

class CustomersRecordsModel extends BasicModel
{
    protected $table = 'customers_records';

    public function images()
    {
        return $this->hasMany('CustomersRecordsImagesModel','customers_records_id','id');
    }

    public function customer()
    {
        return $this->hasOne('CustomersModel','id','customers_id');
    }

    /**
     * 根据residence_id查找
     * 装修直播列表
     * @param $params
     * @return mixed
     */
    public function listByResidence($params)
    {
        $sql = 'select re.*,resi.name as residence_name,re.createtime as createtime,de.ridgepole,de.cell,de.house_type,resi.photo_effects from customers_records as re 
        left join customers as cu on re.customers_id = cu.id
        left join residences as resi on resi.id = cu.residence_id 
        left join residences_design as de on de.id = cu.design_id 
        where 1 and re.id is not null and re.is_delete =2 ';
        if($params['residence_id']){
            $sql .= '  and cu.residence_id = '.$params['residence_id'];
        }
        $sql .= '  group by re.id';
        //排序
        $sql .= ' order by re.createtime';
        return Db::Query($sql);
    }


}
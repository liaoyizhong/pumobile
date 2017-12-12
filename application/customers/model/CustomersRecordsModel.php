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

    /**
     * 装修直播
     * @param $params
     * @return mixed
     */
    public function listByResidence($params)
    {
        $sql = 'select re.*,resi.name,re.createtime,de.id,de.design_url from customers_records as re 
        left join customers as cu on re.customers_id = cu.id
        left join residences as resi on resi.id = cu.residence_id 
        left join residences_design as de on de.residences_id = resi.id 
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
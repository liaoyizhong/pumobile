<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateCustomer extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('customers')->setComment("客户信息")
            ->addColumn('region_id','integer',["comment"=>"区域id"])
            ->addColumn('residence_id','integer',["comment"=>"与residences关联"])
            ->addColumn('design_id','integer',["comment"=>"与residences_design关联"])
            ->addColumn('manager_id','integer',["comment"=>"与mangers关联"])
            ->addColumn('house_num','string',['limit'=>200,"comment"=>"房号"])
            ->addColumn('family_name','string',['limit'=>100,"comment"=>"姓"])
            ->addColumn('name','string',['limit'=>200,"comment"=>"名"])
            ->addColumn('sex','integer',['comment'=>'性别'])
            ->addColumn('phone','string',['comment'=>'电话','limit'=>'200'])
            ->addColumn('starttime','datetime',["comment"=>"施工开始日期"])
            ->addColumn('endtime','datetime',["comment"=>"施工结束日期"])
            ->addColumn('createtime','datetime',["default"=>"CURRENT_TIMESTAMP"])
            ->addColumn('updatetime','datetime',["default"=>"CURRENT_TIMESTAMP"])
            ->addColumn('is_delete','integer',["default"=>"2","comment"=>"1=>已经删除,2=>没有删除"])
            ->save();
    }
}

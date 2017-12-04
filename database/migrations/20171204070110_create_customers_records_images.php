<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateCustomersRecordsImages extends Migrator
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
        $this->table('customers_records_images')->setComment("客户直播信息图片")
            ->addColumn('customers_records_id','integer',["comment"=>"与customers_records表关联"])
            ->addColumn('image_hash_code','text',["comment"=>"图片hash_code值"])
            ->addColumn('is_delete','integer',["default"=>"2","comment"=>"1=>已经删除,2=>没有删除"])
            ->save();
    }
}

<?php
namespace app\common\logic;
/**
 * User: liaoyizhong
 * Date: 2017/11/7/007
 * Time: 14:48
 */

abstract class BasicLogic
{
    /**
     * @param $id
     * @return ResidencesModel|null
     */
    public function get($id)
    {
        $className = get_called_class();
        $className = str_replace('logic','model',$className);
        $className = preg_replace('/Logic$/','Model',$className);
        if(!class_exists($className)){
            return FALSE;
        }
        return $className::get(["id"=>$id,"is_delete"=>2]);
    }

    /**
     * @param $id
     * @return array
     */
    public function deleteModel($id)
    {
        $model = $this->get($id);
        if (!$model) {
            return [FALSE, '查找的数据不存'];
        }
        if ($model->is_delete == 1) {
            return [FALSE, '数据已经被删除过'];
        }
        if ($model->save(["is_delete" => 1])) {
            return [TRUE, '刪除成功'];
        } else {
            return [FALSE, '删除失败'];
        }
    }
}
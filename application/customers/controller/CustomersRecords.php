<?php
/**
 * User: liaoyizhong
 * Date: 2017/12/4/004
 * Time: 15:40
 */

namespace app\customers\controller;

use app\common\controller\Basic;
use app\common\enums\HeaderStatus;
use app\common\enums\ResponseCode;
use app\customers\model\CustomersRecordsModel as RecordModel;
use think\Db;
use think\Loader;
use think\Request;

class CustomersRecords extends Basic
{
    protected $beforeActionList = [
        'checkManagerLogin' => ['only' => 'save,update'],
    ];

    /**
     * 保存新建的资源
     *
     */
    public function save()
    {
        $params = $this->getParams(self::METHODPOST);
        $check = $this->validate($params, 'CustomerRecordValidate');
        if ($check !== TRUE) {
            return $this->showResponse(ResponseCode::PARAMS_INVALID, $check, [], array('status' => HeaderStatus::BADREQUEST));
        }

        $record['customers_id'] = $params['customers_id'];
        $record['content'] = $params['content'];
        $logic = Loader::model('CustomersRecordsLogic', 'logic');
        try {
            Db::startTrans();
            if (!$newId = $logic->save($record)) {
                Db::rollback();
                return $this->showResponse(ResponseCode::UNKNOW_ERROR, '保存失败', [], array('status' => HeaderStatus::UNPROCESABLEENTITY));
            }

            if (isset($params['pic_hash_code']) && count($params['pic_hash_code'])) {
                $imageLogic = Loader::model('CustomersRecordsImagesLogic', 'logic');
                $imageParams = [];
                foreach ($params['pic_hash_code'] as $key => $value) {
                    $imageParams[$key]['image_hash_code'] = $value['hash_code'];
                    $imageParams[$key]['customers_records_id'] = $newId;
                    $imageParams[$key]['order_num'] = $key;
                }
                $imageLogic->saveAll($imageParams);
                Db::commit();
                return $this->showResponse(ResponseCode::SUCCESS, '保存成功', [], array('status' => HeaderStatus::SUCCESS));
            }
        } catch (\exception $e) {
            Db::rollback();
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '保存失败', [], array('status' => HeaderStatus::NOTFOUND));
        }
    }

    /**
     * 保存更新的资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function update($id)
    {
        $params = $this->getParams(self::METHODPUT);
        $model = RecordModel::get($id);

        $updateParams['customers_id'] = isset($params['customers_id']) ? $params['customers_id'] : $model->customers_id;
        $updateParams['content'] = isset($params['content']) ? $params['content'] : $model->content;

        try {
            $model->save($updateParams);
            if (isset($params['pic_hash_code']) && count($params['pic_hash_code'])) {
                //先删除之前的图片
                $imageLogic = Loader::model('CustomersRecordsImagesLogic', 'logic');
                $imageLogic->cleanUp($id);
                $imageParams = [];
                foreach ($params['pic_hash_code'] as $key => $value) {
                    $imageParams[$key]['image_hash_code'] = $value['hash_code'];
                    $imageParams[$key]['customers_records_id'] = $id;
                    $imageParams[$key]['order_num'] = $key;
                }
                $imageLogic->saveAll($imageParams);
                Db::commit();
                return $this->showResponse(ResponseCode::SUCCESS, '保存成功', [], array('status' => HeaderStatus::SUCCESS));
            }
        } catch (\exception $e) {
            Db::rollback();
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '保存失败', [], array('status' => HeaderStatus::NOTFOUND));
        }
    }

    /**
     * 装修直播
     * @param $params
     * @return array
     */
    public function listResidence($id = '')
    {
        $params['residence_id'] = $id;
        $logic = Loader::model('CustomersRecordsLogic', 'logic');

        try {
            $result = $logic->listByResidence($params);
        } catch (\exception $e) {
            return $this->showResponse(ResponseCode::UNKNOW_ERROR, '读取失败,错误信息:' . $e->getMessage(), [], array('status' => HeaderStatus::BADREQUEST));
        }
        return $this->showResponse(ResponseCode::SUCCESS, '读取成功', $result, array('status' => HeaderStatus::SUCCESS));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        //
    }

    /**
     *
     * 查找指定id 的customers
     * @param  int $id
     * @return \think\Response
     */
    public function read($id)
    {
        $logic = Loader::model('CustomersRecordsLogic','logic');
        try{
            $result = $logic->read($id);
            return $this->showResponse(ResponseCode::SUCCESS,'',$result,array('status'=>HeaderStatus::SUCCESS));
        }catch (\Exception $e){
            return $this->showResponse(ResponseCode::UNKNOW_ERROR,$e->getMessage(),[],array('status'=>HeaderStatus::BADREQUEST));
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * 删除指定资源
     *
     * @param  int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }


}
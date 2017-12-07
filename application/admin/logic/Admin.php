<?php
/**
 * author      : Gavin <liyn2007@qq.com>
 * createTime  : 2017/8/18 09:30
 * description :
 */

namespace app\admin\logic;


use app\admin\model\Admin as AdminModel;
use app\lib\exception\AdminException;
use app\lib\exception\FailMessage;
use think\Db;
use think\Exception;
use think\Request;

class Admin
{
    public function __construct()
    {
        $this->Admin = new AdminModel();
    }
    /**
     * 根据帐户密码检查是否正确
     *
     * @param $account
     * @param $password
     * @return array|bool|false|\PDOStatement|string|\think\Model
     */
    public static function checkSuper($account, $password)
    {
        $adminAccount = AdminModel::where('username','=', $account)
                ->find();
        if(empty($adminAccount)){
            return false;
        }
        //检验密码
        if(!password_verify($password, $adminAccount['password'])){
            return false;
        }

        return $adminAccount;
    }

    /**
     * 根据用户ID，返回用户帐号信息
     *
     * @param $uid
     * @return array|bool|false|\PDOStatement|string|\think\Model
     */
    public static function getSuperAccount($uid)
    {
        $adminAccount = AdminModel::where('id','=', $uid)
            ->find();
        if(empty($adminAccount)){
            return false;
        }

        return $adminAccount;
    }

    public function create($data = [])
    {
        try {
            $this->Admin->username = $data['username'];
            $this->Admin->email = $data['email'];
            $this->Admin->password = $data['password'];

            $this->Admin->save();
            $id = $this->Admin->id;

            return $id;
        }catch(Exception $e){
            throw $e;
        }
    }

    public function updateById($id, $data = [])
    {
        $admin = AdminModel::getById($id);

        try {
            $admin->username = $data['username'];
            $admin->email = $data['email'];
            if(!empty($data['password'])) {
                $admin->password = $data['password'];
            }

            return $admin->save(); // 更新;
        }catch (Exception $e){
            throw $e;
        }
    }

    public function getById($id)
    {
        $admin = AdminModel::getById($id);
        if(empty($admin)){
            throw new AdminException([
                'code' => 404
            ]);
        }

        return $admin->toArray();
    }

    public function delete($id)
    {
        $this->getById($id);

        // TODO:判断是否被引用
        $result = AdminModel::destroy($id);

        return $result;
    }

    public function updateStatus($id, $status)
    {
        try {
            $admin = AdminModel::getById($id);

            $admin->id = $id;
            $admin->status = (int)$status;

            return $admin->save(); // 更新
        }catch(Exception $e){
            throw $e;
        }
    }

    public function getLists($data = [], $page = 1, $size = 10)
    {
        if($size > 10) $size = 10;

        $query = AdminModel::order('create_time DESC');

        if(!empty($data['username'])){ // 昵称
            $query->where('username', 'like', '%'.$data['username'].'%');
        }

        if(!empty($data['mobile'])){ // 手机号
            $query->where('mobile', 'like', '%'.$data['mobile'].'%');
        }

        if(!empty($data['email'])){ // 邮箱
            $query->where('email', 'like', '%'.$data['email'].'%');
        }

        $results = $query->paginate($size);

        if($results->isEmpty()){
            return $results->toArray();
        }

        $collection = collection($results->items());
        $data = $collection->append(['status_text'])->visible([
            'id', 'username', 'email', 'create_time', 'status'
        ])->toArray();

        $admins = [
            'total' => $results->total(),
            'per_page' => $results->listRows(),
            'current_page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'data' => $data,
        ];

        return $admins;
    }
}

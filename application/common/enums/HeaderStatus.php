<?php
/**
 * User: liaoyizhong
 * Date: 2017/11/24/024
 * Time: 10:34
 */

namespace app\common\enums;


class HeaderStatus
{
    const SUCCESS = 200; //服务器成功返回用户请求的数据。
    const CREATED = 201; //新建或修改数据成功。
    const ACCEPTED = 202; //成功入列
    const NOCONTENT = 204; //用户删除数据成功

    const BADREQUEST = 400; // 指代坏请求（如，参数错误）
    const UNAUTHORIZED = 401; //表示用户没有权限（令牌、用户名、密码错误等）
    const FORBIDDEN = 403; // 表示用户得到授权（与401错误相对），但是访问是被禁止的。
    const NOTFOUND = 404; //用户发出的请求针对的是不存在的记录，服务器没有进行操作
    const NOTACCEPTABLE = 406; //用户请求的格式不可得（比如用户请求JSON格式，但是只有XML格式）。
    const GONE = 410; //用户请求的资源被永久删除，且不会再得到的。
    const UNPROCESABLEENTITY = 422; //[POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。
    const TOOMANYREQUEST = 429; //请求过多
    const INTERNALSERVERERROR = 500; //服务器发生错误，用户将无法判断发出的请求是否成功，服务器端错误，统一用500
}
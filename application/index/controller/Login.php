<?php
namespace app\index\controller;
use think\captcha\Captcha;
use think\Controller;
use think\Db;
class Login  extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function regist()
    {
        return view('regist');
    }
    public function login()
    {
        $name=input('get.name');
        $pwd=input('get.pwd');
        $arr=Db::table('user')->where('user_name',$name)->where('password',$pwd)->select();
        if (empty($arr)){
            echo 'on';
        }else{
            echo 'ok';
        }
    }
}

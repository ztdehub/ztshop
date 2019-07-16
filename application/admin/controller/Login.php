<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\captcha\Captcha;
use gmars\rbac\Rbac;
use think\facade\Session;
class Login extends Controller
{
    public function login(){
        return $this->fetch();
    }
    public function out(){
        Session::clear();
        $this->redirect('login/login');
    }
    public function check(){
        $captcha = new Captcha();
        $value=input('post.value');
        $name=input('post.name');
        $password=input('post.password');
        if( !$captcha->check($value)) {
            $json=['start'=>'0','code'=>'验证码错误'];
        }else{
            $data=['user_name'=>$name,'password'=>$password];
            $arr=Db::table('user')->where($data)->select();
            if (empty($arr)){
                $json=['start'=>'1','code'=>'账户或密码错误'];
            }else{
                $id=$arr[0]['id'];
                Session::set('name',$name);
                $json=['start'=>'3','code'=>'登录成功'];
                $rbacObj = new Rbac();
                $rbacObj->cachePermission($id);
            }
        }
        echo $arr=json_encode($json);
    }
}
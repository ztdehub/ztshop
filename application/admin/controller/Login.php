<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\captcha\Captcha;
class Login extends Controller
{
    public function login(){
        return $this->fetch();
    }
    public function check(){
        $captcha = new Captcha();
        $value=input('post.value');
        $name=input('post.name');
        $password=input('post.password');
        if( !$captcha->check($value)) {
            $json=['start'=>'0','code'=>'验证码错误'];
        }else{
            $data=['name'=>$name,'password'=>$password];
            $arr=Db::table('admin')->where($data)->select();
            if (empty($arr)){
                $json=['start'=>'1','code'=>'账户或密码错误'];
            }
        }
        echo $arr=json_encode($json);
    }
}

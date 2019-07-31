<?php
namespace app\admin\controller;
use Request;
use think\Controller;
use think\Db;
use gmars\rbac\Rbac;

class User extends Common
{
    public function list(){
        return $this->fetch();
    }
    public function permission_add(){
        return $this->fetch();
    }

    public function add()
    {
        $name=input('post.name');
        $password=input('post.password');
        $mobile=input('post.mobile');
        $role=input('post.role');
        $time=date("Y-m-d H:i:s");
        $ass=Db::table('user')->where('user_name','=',$name)->select();
        if (empty($ass)){
            $data=['user_name'=>$name,'password'=>$password,'mobile'=>$mobile,'create_time'=>$time];
            Db::table('user')->insert($data);
            $arr=Db::table('user')->where('user_name','=',$name)->select();
            $id=$arr[0]['id'];
            $rbac=new Rbac();
            $rbac->assignUserRole($id, [$role]);
            $json=['code'=>'1','status'=>'ok','data'=>'添加成功'];
            echo json_encode($json);
        }else{
            $json=['code'=>'0','status'=>'error','data'=>'用户已存在'];
            echo json_encode($json);
        }
    }
    public function role(){
        $arr=Db::table('role')->select();
        $json=['data'=>$arr];
        echo json_encode($json);
    }
    public function show(){
        $arr=Db::table('user_role')->field('user.id,user_role.role_id,user.password,user.user_name,user.mobile,user.last_login_time,user.create_time,user.update_time,role.name')->join('user','user.id = user_role.user_id')->join('role','role.id = user_role.role_id')->select();
        $json=['data'=>$arr];
        echo json_encode($json);
    }

    public function per_sel(){
        $id=input('post.id');
        $arr=Db::table('permission_category')->where('id',$id)->select();
        $json = ['code'=>'1','status' => 'ok', 'data' => $arr];
        echo json_encode($json);
    }

    //修改
    function update(){
        $id=input('post.id');
        $name=input('post.name');
        $password=input('post.password');
        $mobile=input('post.mobile');
        $role=input('post.role');
        $arr=Db::table('user')->where('user_name','=',$name)->select();
        if (empty($arr) || $id==$arr[0]['id']){
            $data=['user_name'=>$name,'password'=>$password,'mobile'=>$mobile];
            Db::table('user')->where('id','=',$id)->update($data);
            $rbac=new Rbac();
            $rbac->assignUserRole($id, [$role]);
            echo '修改成功';
        }else{
            echo '不能修改';
        }
    }
    //批量删除
    public function datadel(){
        $id=input('post.id');
        $pieces =   $a = substr($id,1);
        //去除数组首个字段
        if ($id==''){
            $json=['code'=>'0','status'=>'error','data'=>'请选择删除内容'];
            echo json_encode($json);
        }else{
            //事务
            Db::transaction(function (){
                $id=input('post.id');
                $pieces =   $a = substr($id,1);
                Db::table('user')->where('id','in',$pieces)->delete();
                Db::table('user_role')->where('user_id','in',$pieces)->delete();
            });
            $json=['code'=>'0','status'=>'ok','data'=>'删除成功'];
            echo json_encode($json);
        }
    }
    public function del(){
        $id=input('post.id');
        Db::table('user')->where('id',$id)->delete();
        Db::table('user_role')->where('user_id',$id)->delete();
    }
}

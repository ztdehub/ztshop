<?php
namespace app\admin\controller;
use Request;
use think\Controller;
use think\Db;
use gmars\rbac\Rbac;

class Role extends Common
{
    public function list(){
        return $this->fetch();
    }
    public function permission_add(){
        return $this->fetch();
    }
    public function role_add()
    {
        $id=input('post.id');
        $role=Db::table('role_permission')->where('role_id','=',$id)->select();
        $arr=Db::table('permission')->field(' permission.id,permission.path,permission.description,permission.category_id,permission.name as p_name,permission_category.name')->join('permission_category','permission.category_id = permission_category.id')->select();
        $ass=[];
        foreach ($arr as $value) {
            $ass[$value['name']][]=[$value['p_name'],$value['id']];
        }
        $json=['data'=>$ass,'role'=>$role];
        echo json_encode($json);
    }
    //添加角色权限
    function add(){
        $name=input('post.name');
        $id=input('post.id');
        $description=input('post.description');
        $a = substr($id,1);
        $validate = new \app\admin\validate\Role;
        $result =Request::post();
        if (!$validate->check($result)) {
            $json=['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($json);
            die;
        }
        //去除数组首个字段
        $rbac=new Rbac();
        $arr=Db::table('role')->where('name','=',$name)->select();
        if (empty($arr)){
            $rbac->createRole([
                'name' => $name,
                'description' => $description,
                'status' => 1
            ], $a);
            $json = ['code'=>'1','status' => 'ok', 'data' => '添加成功'];
            echo json_encode($json);
        }else{
            $json = ['code'=>'0','status' => 'error', 'data' =>'角色已经存在'];
            echo json_encode($json);
        }

    }

    public function show(){
        $arr=Db::table('role')->select();
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
        $r_id=input('post.r_id');
        $id=input('post.id');
        $description=input('post.description');
        $name=input('post.name');
        $a = substr($id,1);
        $role=explode(',',$a);
        $arr=Db::table('role')->where('name','=',$name)->select();
        if (empty($arr) || !empty($arr) && $arr[0]['id']==$r_id){
            $data=['name'=>$name,'description'=>$description];
            Db::table('role')->where('id','=',$r_id)->update($data);
            Db::table('role_permission')->where('role_id','=',$r_id)->delete();
            $validate = new \app\admin\validate\Role;
            $result =Request::post();
            if (!$validate->check($result)) {
                $json=['code'=>'2','status'=>'error','data'=>$validate->getError()];
                echo json_encode($json);
                die;
            }
            foreach ($role as $key=>$value){
                Db::query("insert into role_permission  (`role_id`,`permission_id`) value ('$r_id','$value')");
            }
            $json=['code'=>'1','status'=>'ok','data'=>'修改成功'];
            echo json_encode($json);
        }else{
            $json=['code'=>'0','status'=>'error','data'=>'角色名不能重复'];
            echo json_encode($json);
        }
    }
    //批量删除
    public function datadel(){
        $id=input('post.id');
        $pieces = explode(",",$id);
        //去除数组首个字段
        array_shift($pieces);
        if ($id==''){
            $json=['code'=>'0','status'=>'error','data'=>'请选择删除内容'];
            echo json_encode($json);
        }else{
            $rbac=new Rbac();
            $rbac->delRole($pieces);
            $json=['code'=>'0','status'=>'ok','data'=>'删除成功'];
            echo json_encode($json);
        }

    }
    public function del(){
        $id=input('post.id');
        Db::table('role')->where('id',$id)->delete();
        Db::table('role_permission')->where('role_id',$id)->delete();
    }
}

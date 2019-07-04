<?php
namespace app\admin\controller;
use Request;
use think\Controller;
use think\Db;
use gmars\rbac\Rbac;

class Permission extends Common
{
    public function list(){
        return $this->fetch();
    }
    public function permission_add(){
        return $this->fetch();
    }
    public function add()
    {
        $validate = new \app\admin\validate\Permission;
        $rbac = new Rbac();
        $result =Request::post();
        //验证器将数组传过去根据字段分析内容是否符合
            if (!$validate->check($result)) {
                 $json=['code'=>'1','status'=>'error','data'=>$validate->getError()];
                echo json_encode($json);
                die;
            }
        //验证用户名是否重复
        $arr = $rbac->getPermission(['name' => $result['name']]);
        $ass = $rbac->getPermission(['path' => $result['path']]);
        if (empty($arr) && empty($ass)) {
            $rbac->createPermission([
                'name' => $result['name'],
                'description' => $result['description'],
                'status' => 1,
                'type' => 1,
                'category_id' => $result['select'],
                'path' => $result['path'],
            ]);
//            $rbac=new Rbac();
//            $arr=$rbac->getPermissionCategory([]);
            $json = ['code'=>'2','status' => 'ok', 'data' => '添加成功'];
            echo json_encode($json);
        } else {
            $json = ['code'=>'3','status' => 'error', 'data' => '权限或路劲存在不能重复添加'];
            echo json_encode($json);
            die;
        }
    }
    public function show(){
        $arr=Db::table('permission')->field(' permission.id,permission.path,permission.description,permission.category_id,permission.name as p_name,permission_category.name')->join('permission_category','permission.category_id = permission_category.id')->select();
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
        $id=input('post.update_id');
        $update_path=input('post.update_path');
        $update_name=input('post.update_name');
        $update_description=input('post.update_description');
        $update_select=input('post.update_select');
        $validate = new \app\admin\validate\Permission;
        $result =Request::post();
        $rbac = new Rbac();
//        if (!$validate->check($result)) {
//            $json=['code'=>'3','status'=>'error','data'=>$validate->getError()];
//            echo json_encode($json);
//            die;
//        }
        $ajj=Db::query("select id from permission where name = '$update_name' or path = '$update_path'");
        if (empty($ajj)){
            $data=['name'=>$update_name,'category_id'=>$update_select,'path'=>$update_path,'description'=>$update_description];
            Db::table('permission')->where('id','=',$id)->update($data);
            $json=['code'=>'0','status'=>'ok','data'=>'修改成功'];
            echo json_encode($json);
        }else{
            foreach ($ajj as $key => $value){
                if ($value['id']!=$id){
                    $json=['code'=>'0','status'=>'error','data'=>'权限或者路径重复'];
                    echo json_encode($json);
                    die;
                }
            }
            $data=['name'=>$update_name,'category_id'=>$update_select,'path'=>$update_path,'description'=>$update_description];
            Db::table('permission')->where('id','=',$id)->update($data);
            $json=['code'=>'0','status'=>'ok','data'=>'修改成功'];
            echo json_encode($json);
        }
        //取到id和查询出来id相同可修改，名称一样不能修改，路径一样不能修改
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
            $rbac->delPermission($pieces);
            $json=['code'=>'0','status'=>'ok','data'=>'删除成功'];
            echo json_encode($json);
        }

    }
    public function del(){
        $id=input('post.id');
        Db::table('permission')->where('id',$id)->delete();
    }
}

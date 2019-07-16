<?php
namespace app\admin\controller;
use Request;
use think\Controller;
use think\Db;
use gmars\rbac\Rbac;
use think\facade\Session;

class Permissioncategory extends Common
{
    public function list(){
        return $this->fetch();
    }
    public function permission_add(){
        return $this->fetch();
    }
    public function add()
    {
        $validate = new \app\admin\validate\Permissioncategory;
        $rbac = new Rbac();
        $result =Request::post();
        //验证器将数组传过去根据字段分析内容是否符合
        if (!$validate->check($result)) {
            $json=['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($json);
            die;
        }

        //验证用户名是否重复
        $arr = $rbac->getPermissionCategory(['name' => $result['name']]);
        if (empty($arr)) {
            $rbac->savePermissionCategory([
                'name' => $result['name'],
                'description' => $result['description'],
                'status' => 1
            ]);
            $json = ['code'=>'2','status' => 'ok', 'data' => '添加成功'];
            echo json_encode($json);
        } else {
            $json = ['code'=>'3','status' => 'error', 'data' => '权限已存在不能重复添加'];
            echo json_encode($json);
            die;
        }
    }
    public function show(){
        $rbac=new Rbac();
        $arr=$rbac->getPermissionCategory([]);
        $json=['data'=>$arr];
        echo json_encode($json);
    }
    public function per_sel(){
        $id=input('post.id');
        $arr=Db::table('permission_category')->where('id',$id)->select();
        $json = ['code'=>'1','status' => 'ok', 'data' => $arr];
        echo json_encode($json);
    }

    public function update(){
        $validate = new \app\admin\validate\Permissioncategory;
        $result =Request::post();
        $rbac = new Rbac();
        $id=input('post.id');
        if (!$validate->check($result)) {
            $json=['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($json);
            die;
        }
        //验证用户名3 是否重复
        $arr = $rbac->getPermissionCategory(['name' => $result['name']]);
//        var_dump($arr);
        if (empty($arr)) {
            $data=['name'=>$result['name'],'description'=>$result['description']];
            Db::table('permission_category')->where('id',$id)->update($data);
            $json = ['code'=>'2','status' => 'ok', 'data' => '添加成功'];
            echo json_encode($json);
        } else {
            if ($arr[0]['id']==$result['id']){
                $data=['name'=>$result['name'],'description'=>$result['description']];
                Db::table('permission_category')->where('id',$id)->update($data);
                $json = ['code'=>'2','status' => 'ok', 'data' => '添加成功'];
                echo json_encode($json);
            }else{
                $json = ['code'=>'3','status' => 'error', 'data' => '权限已存在不能重复添加'];
                echo json_encode($json);
                die;
            }
        }
    }
    //命令
    public function token(){
        $token = $this->request->token('__token__', 'sha1');
        $json=['code'=>'0','status','error'=>'ok','data'=>$token];
        echo json_encode($json);
        Session::set('token',$token);
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
            $rbac->delPermissionCategory($pieces);
            $json=['code'=>'0','status'=>'ok','data'=>'删除成功'];
            echo json_encode($json);
        }
    }

    public function del(){
        $token=Session::get('token');
        $tokens=input('post.tokens');
        if ($token!=$tokens){
            $json=['code'=>'0','status'=>'error','data'=>'命令不匹配'];
            echo json_encode($json);
            die;
        }
        $id=input('post.id');
        Db::table('permission_category')->where('id',$id)->delete();
        $json=['code'=>'1','status'=>'ok','data'=>'删除成功'];
        echo json_encode($json);
    }
}
<?php
namespace app\admin\controller;
use think\Db;
class Goodsattr extends Common
{
    public function list(){
        return $this->fetch();
    }
    function add(){
        $name=input('post.name');
        $data=['name'=>$name];
        Db::table('attr_category')->insert($data);
    }
    function show(){
        $id=input('post.id');
        $ass=Db::table('goods_attr')->where('goods_id',$id)->find();
        $attr_cate_id=$ass['attr_cate_id'];
        $arr=Db::table('attr_category')->select();
        $json=['data'=>$arr,'date'=>$attr_cate_id];
        echo json_encode($json);
    }
    function attr(){
        $id=input('get.id');
        $arr=Db::table('attr_category')->where('id',$id)->find();
        $name=$arr['name'];
        $this->assign('name', $name);
        $this->assign('id', $id);
        return $this->fetch();
    }
    function attrshow(){
        $id=input('post.id');
        $arr=Db::table('attribute')->where('attrcate_id',$id)->select();
        $json=['data'=>$arr];
        echo json_encode($json);
    }
    function attradd(){
        $name=input('post.name');
        $id=input('post.id');
        $data=['attrcate_id'=>$id,'name'=>$name];
        Db::table('attribute')->insert($data);
    }
    function attrname(){
        $name=input('post.name');
        $id=input('post.id');
        $data=['name'=>$name,'attr_id'=>$id];
        echo$arr=Db::table('attr_details')->insertGetId($data);
    }
}

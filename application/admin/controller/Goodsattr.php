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
    //分类展示
    function attrshow(){
        $id=input('post.id');
        $arr=Db::table('attribute')->where('attrcate_id',$id)->select();
        $json=['data'=>$arr];
        echo json_encode($json);
    }
    //属性分类添加
    function attradd(){
        $name=input('post.name');
        $id=input('post.id');
        $arr=Db::table('attribute')->where('name',$name)->where('attrcate_id',$id)->select();
        if (empty($arr)){
            $data=['attrcate_id'=>$id,'name'=>$name];
            Db::table('attribute')->insert($data);
            echo '添加成功';
        }else{
            echo '不要重复添加';
        }
    }
    //分类属性添加
    function attrname(){
        $name=input('post.name');
        $id=input('post.id');
        $arr=Db::table('attr_details')->where('name',$name)->where('attr_id',$id)->select();
        if (empty($arr)){
            $data=['name'=>$name,'attr_id'=>$id];
            echo$arr=Db::table('attr_details')->insertGetId($data);
        }else{
            echo '属性已存在';
        }

    }
}

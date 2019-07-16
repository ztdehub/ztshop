<?php
namespace app\admin\controller;
use think\Db;
class Goodsimg extends Common
{
    public function list(){
        $id=input('get.id');
        $this->assign('id', $id);
        return $this->fetch();
    }
    function imgadd(){
        $id=input('post.id');
        $file = request()->file('file');
        foreach ($file as $key => $value){
            $info = $value->validate(['size'=>583328,'ext'=>'jpg,png,gif'])->move('./uploads');
            if($info){
                // 成功上传后 获取上传
                $pand =str_replace("\\","/",$info->getSaveName());
                $image = \think\Image::open('./uploads/'.$pand);
                $names= $info->getFilename();
                $big="big/big".$names;
                $middle="middle/middle".$names;
                $small="small/small".$names;
                $data=['goods_id'=>$id,'big_img'=>$big,'middle_img'=>$middle,'small_img'=>$small];
                Db::table('goods_img')->insert($data);
//将图片裁剪为300x300并保存为crop.png
                $image->thumb(300, 300)->save('./uploads/big/big'.$names);
                $image->thumb(200, 200)->save('./uploads/middle/middle'.$names);
                $image->thumb(100, 100)->save('./uploads/small/small'.$names);
            }else{
                echo '文件太大';
                // 上传失败获取错误信息
//                echo $file->getError();
            }
        }
    }
    function show(){
        $id=input('post.id');
        $arr=Db::table('goods_img')->where('goods_id',$id)->select();
        $josn = ['code' => '1', 'status' => 'ok', 'data' => $arr];
        echo json_encode($josn);
    }
    function del(){
        $id=input('post.id');
        $arr=Db::table('goods_img')->where('id',$id)->find();
        unlink("./uploads/".$arr['big_img']);
        unlink("./uploads/".$arr['small_img']);
        unlink("./uploads/".$arr['middle_img']);
        Db::table('goods_img')->where('id',$id)->delete();
    }
}

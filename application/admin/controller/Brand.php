<?php
namespace app\admin\controller;
use think\Db;
use Request;
class Brand extends Common
{
    public function list(){
        return $this->fetch();
    }
    function show(){
        $arr=Db::table('brand')->select();
        $json=['data'=>$arr];
        echo json_encode($json);
    }
    function add()
    {
        $file = request()->file('file');
        $name = input('post.name');
        $description = input('post.description');
        $validate = new \app\admin\validate\Brand;
        $result =Request::post();
        //验证器将数组传过去根据字段分析内容是否符合
        if (!$validate->check($result)) {
            $json=['code'=>'1','status'=>'error','data'=>$validate->getError()];
            echo json_encode($json);
            die;
        }
        if (empty($file)){
            echo '文件不能为空';
            die;
        }
        if ((($_FILES["file"]["type"] == "image/gif")
                || ($_FILES["file"]["type"] == "image/jpeg")
                || ($_FILES["file"]["type"] == "image/jpg")
                || ($_FILES["file"]["type"] == "imagepeg")
                || ($_FILES["file"]["type"] == "image/x-png")
                || ($_FILES["file"]["type"] == "image/png"))
            && ($_FILES["file"]["size"] < 204800)) {

            if ($_FILES["file"]["error"] > 0)
            {
                echo "错误：: " . $_FILES["file"]["error"] . "<br>";
            }
            else
            {
                $info = $file->move( './uploads');
                if($info){
                    $getSaveName=str_replace("\\","/",$info->getSaveName());
                    $data=['name'=>$name,'description'=>$description,'logo'=>$getSaveName];
                    Db::table('brand')->insert($data);
                }else{
                    // 上传失败获取错误信息
                    echo $file->getError();
                }
            }
        }else{
            echo 'no';
        }
    }

    function img_update(){
        $file = request()->file('file');
        $oldimg = input('post.oldimg');
        $id = input('post.id');
        //验证器将数组传过去根据字段分析内容是否符合
        if (empty($file)){
            echo '文件不能为空';
            die;
        }
        if ((($_FILES["file"]["type"] == "image/gif")
                || ($_FILES["file"]["type"] == "image/jpeg")
                || ($_FILES["file"]["type"] == "image/jpg")
                || ($_FILES["file"]["type"] == "imagepeg")
                || ($_FILES["file"]["type"] == "image/x-png")
                || ($_FILES["file"]["type"] == "image/png"))
            && ($_FILES["file"]["size"] < 204800)) {

            if ($_FILES["file"]["error"] > 0)
            {
                echo "错误：: " . $_FILES["file"]["error"] . "<br>";
            }
            else
            {
                $info = $file->move( './uploads');
                if($info){
                    $getSaveName=str_replace("\\","/",$info->getSaveName());
                    $data=['logo'=>$getSaveName];
                    Db::table('brand')->where('id',$id)->update($data);
                    unlink(".".$oldimg);
                    $json=['code'=>'1','status'=>'ok','data'=>'修改成功'];
                    echo json_encode($json);
                }else{
                    // 上传失败获取错误信息
                    echo son_encode($file->getError());
                }
            }
        }else{
            echo 'no';
        }
    }
    function update(){
         $id=input('post.id');
         $name=input('post.name');
         $description=input('post.description');
         $data=['name'=>$name,"description"=>$description];
         Db::table('brand')->where('id',$id)->update($data);
         echo '修改成功';

    }
    function del(){
        $id=input('post.id');
        $logo=input('post.logo');
        unlink("./uploads/".$logo);
        Db::table('brand')->where('id',$id)->delete();
        $json=['code'=>'1','status'=>'error','data'=>'删除成功'];
        echo json_encode($json);
    }
}

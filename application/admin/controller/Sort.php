<?php
namespace app\admin\controller;
use Request;
use think\Controller;
use think\Db;
use gmars\rbac\Rbac;

class Sort extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    private function getTree($arr,$pid = 0, $level = 0){

        static $list = [];
        echo "<ul class='aa'>";
        foreach ($arr as $key => $value){

            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['parent_id'] == $pid){
                //父节点为根节点的节点,级别为0，也就是第一级
//                    $flg = str_repeat('|--',$level);
                // 更新 名称值
                    $value['name'] = $value['name'];
                // 输出 名称
//                    echo $value['name']."<br/>";
                echo "<li class='er' value='".$value['id']."' id='pid".$value['id']."'>".$value['name']." <a class=\"Hui-iconfont\" onclick='del(".$value['id'].")'>&#xe6a6;</a> </li>";
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($arr[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTree($arr, $value['id'], $level+1);
            }
        }
        echo "</ul>";
        return $list;
    }
    function show(){
        $arr = Db::table('sort')->select();
        $this->getTree($arr);
    }

    function delete(){
        $id=input('post.id');
        $arr=Db::table('sort')->where('id',$id)->delete();
        $this->del($id);
    }
    function del($id){
      $arr=Db::table('sort')->where('parent_id',$id)->select();
      // 如果数组是空的就这接删除，否则循环查询出来的数组，根据数组自身id删除自己，在调用在自己的方法找自己id下所属的pid如果找到就继续循环找到的数组根据自身id继续删除直到查到数组为空就停止，直接删除自己本身
      if (empty($arr)){
          Db::table('sort')->where('id',$id)->delete();
      }else{
          foreach ($arr as $key=>$value) {
              $sid=$value['id'];
              echo $sid;
              Db::table('sort')->where('id',$sid)->delete();
              $this->del($value['id']);
          }
        }
      }

    function add(){
        $id=input('post.id');
        $name=input('post.name');
        $arr=Db::table('sort')->where('name',$name)->select();
        if (empty($arr)){
            $data=['parent_id'=>$id,"name"=>$name];
            $id = Db::name('sort')->insertGetId($data);
            $json=['code'=>'0','status'=>'ok','data'=>'添加成功'];
            echo json_encode($json);
        }else{
            $json=['code'=>'0','status'=>'error','data'=>'分类重复添加失败'];
            echo json_encode($json);
        }

    }

}

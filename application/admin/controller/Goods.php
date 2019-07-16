<?php
namespace app\admin\controller;
use think\Db;
use Request;
class Goods extends Common
{
    public function list()
    {
        return $this->fetch();
    }
    function goodsattr(){
        return $this->fetch();
    }
    public function addshow()
    {
        return $this->fetch();
    }
    function add(){
        $result =Request::post();
        Db::table('goods')->insert($result);
    }
    public function show()
    {
        $arr = Db::table('goods')->field(' goods.id,goods.goods_sn,goods.is_show,goods.name,goods.description,goods.repertory,goods.create_time,goods.supply_price,goods.maket_price,goods.price,brand.name as b_name')->Join('brand', 'goods.brand_id = brand.id')->select();
        $josn = ['code' => '1', 'status' => 'ok', 'data' => $arr];
        echo json_encode($josn);
    }
    private function getTree($arr, $pid = 0, $level = 0)
    {

        static $list = [];
        foreach ($arr as $key => $value) {

            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value['parent_id'] == $pid) {
                //父节点为根节点的节点,级别为0，也就是第一级
                $flg = str_repeat('|--',$level);
                // 更新 名称值
                $value['name'] = $flg.$value['name'];
                // 输出 名称
//                    echo $value['name']."<br/>";
                echo "<option class='er' value='" . $value['id'] . "' id='pid" . $value['id'] . "'><a>" . $value['name'] . "<a/>  </option>";
                //把数组放到list中
                $list[] = $value;
                //把这个节点从数组中移除,减少后续递归消耗
                unset($arr[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                $this->getTree($arr, $value['id'], $level + 1);
            }
        }
        return $list;
    }
    function add_show()
    {
        $arr = Db::table('sort')->select();
        $this->getTree($arr);
    }
}


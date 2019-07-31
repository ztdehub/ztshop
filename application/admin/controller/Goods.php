<?php
namespace app\admin\controller;
use think\Db;
use Request;
use Cache;
class Goods extends Common
{
    function get($num = 100000)  // $num为生成汉字的数量
    {
        //$b = '';
        for ($i=0; $i<$num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
              $a = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
              $d = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
              $c = chr(mt_rand(0xB0,0xD0)).chr(mt_rand(0xA1, 0xF0));
              $d=$a.$d.$c;
            // 转码
             $b= iconv('GB2312', 'UTF-8', $d);
             "<br>";
        $add = ['name' => $b,'brand_id'=>6];
        $acc=Db::name('goods')->insert($add);

        }
        //return $b;

    }


    public function list()
    {
        return $this->fetch();
    }
    //货品展示
    function goods(){
        $id=input('get.id');
        $this->assign('id',$id);
        return $this->fetch();
    }

    function goodsshow(){
        $id=input('post.id');
        $arr=Db::table('goods_attr')->field('attr_details.name,attr_details.id,attribute.id as b_id,attribute.name as b_name')->join('attr_details','attr_details.id = goods_attr.attr_details_id')->join('attribute','attribute.id = goods_attr.attr_id')->where('goods_attr.goods_id',$id)->select();
        if (!empty($arr)){
            foreach ($arr as $key => $value){
                $ass[$value['b_name']][]=$value;
            }
            $json=['code'=>1,'data'=>$ass];
        }else{
            $json=['code'=>0,'data'=>'没有选择属性'];
        }
        echo json_encode($json);
    }
    //货品展示
    function attrshow(){
        $id=input("post.id");
        $ass=Db::table('goodsattr')->where('goods_id',$id)->select();
        if (!empty($ass)){
            for ($i=0; $i<count($ass); $i++){
                $goods='';
                $attr_id=$ass[$i]['goods_attr_id'];
                $attr=Db::table('attr_details')->where('id','in',$attr_id)->select();
                for ($j=0; $j<count($attr); $j++){
                    $goods=$goods.' '.$attr[$j]['name'];
                }
                $ass[$i]['key']=$goods;
            }
            $json=['code'=>'1','data'=>$ass];
        }else{
            $json=['code'=>'0','data'=>'没有商品'];
        }
        echo json_encode($json);
    }
    //货品添加
    function goodsadd(){
        $id=input("post.id");
        $number=input('post.number');
        $price=input('post.price');
        $goods_id=input("post.goods_id");
        $goods=substr($goods_id,'1');
        $arr=Db::table('goodsattr')->where('goods_attr_id',$goods)->select();
        if (empty($arr)){
            $data=['goods_id'=>$id,'goods_attr_id'=>$goods,'price'=>$price,'sn_number'=>$number];
            Db::table('goodsattr')->insert($data);
            $json=['code'=>'1','status'=>'ok','data'=>'添加成功'];
        }else{
            $json=['code'=>'0','status'=>'error','data'=>'货品已存在'];
        }
        echo json_encode($json);
    }
    //货品删除
    function goodsdel(){
        $id=input("post.id");
        Db::table('goodsattr')->where('id',$id)->delete();
        $json=['code'=>'1','status'=>'ok','data'=>'删除成功'];
        echo json_encode($json);
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

    //搜索
    public function show()
    {
        //如果搜索商品不存在就将它存入 当存入次数超过十次就将查到的数组存入redis
        //根据搜索内容做存储
        $name=input('get.name');
        // Cache::Hdel('select',$name);die;
            if ($name==''){
                $arr = Db::table('goods')->field(' goods.id,goods.goods_sn,goods.is_show,goods.name,goods.description,goods.repertory,goods.create_time,goods.supply_price,goods.maket_price,goods.price,brand.name as b_name')->Join('brand', 'goods.brand_id = brand.id')->limit('0','300')->select();
            }else{
                $ass=Cache::Hget('number',$name);
                Cache::ZINCRBY ('rank:2018',$ass,$name);
                if (!$ass){
                    Cache::Hset('number',$name,1);
                }else{
                    Cache::Hset('number',$name,$ass+1);
                }
                if($ass>10){
                    $arr=Cache::get($name);
                    if(!$arr){
                        $a = Db::table('goods')->field(' goods.id,goods.goods_sn,goods.is_show,goods.name,goods.description,goods.repertory,goods.create_time,goods.supply_price,goods.maket_price,goods.price,brand.name as b_name')->Join('brand', 'goods.brand_id = brand.id')->where('goods.name','like','%'.$name.'%')->limit('0','300')->select();
                        Cache::set($name,$a);
                    }
                }else{
                    $arr = Db::table('goods')->field(' goods.id,goods.goods_sn,goods.is_show,goods.name,goods.description,goods.repertory,goods.create_time,goods.supply_price,goods.maket_price,goods.price,brand.name as b_name')->Join('brand', 'goods.brand_id = brand.id')->where('goods.name','like','%'.$name.'%')->limit('0','300')->select();
                }
            }
        $haxi=Cache::ZREVRANGE ('rank:2018',0,4);
        $josn = ['code' => '1', 'status' => 'ok', 'data' => $arr,'aet'=>$haxi];
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
    function attr(){
        $id=input('get.id');
        $this->assign('id',$id);
        return $this->fetch();
    }
    //属性选择的展示
    function aa(){
        $attrid=input('post.attr_id');
        $id=input('post.id');
        $attr=Db::table('goods_attr')->where('goods_id',$attrid)->where('attr_cate_id',$id)->select();

        $arr= Db::table('attr_details')->field('attribute.name,attr_details.name as d_name,attr_details.id')->join('attribute','attr_details.attr_id = attribute.id')->where('attrcate_id',$id)->select();
        $ass=[];
        foreach ($arr as $key=>$value){
            $ass[$value['name']][]=[$value['d_name'],$value['id']];
        }
        $json=['code'=>'0','status'=>'ok','data'=>$ass,'date'=>$attr];
        echo json_encode($json);
    }

    function goodsattrad(){
        $attr_cate_id=input('post.attr_cate_id');
        $id=input('post.id');
        $attrid=input('post.attrid');
        $arr=substr($attrid,'1');
        Db::table('goods_attr')->where('goods_id',$id)->delete();
        $pieces = explode(",", $arr);
        foreach ($pieces as $key => $value){
           $arr=Db::table('attr_details')->where('id',$value)->find();
           $arr_id=$arr['attr_id'];
           $data=['goods_id'=>$id,'attr_details_id'=>$value,'attr_id'=>$arr_id,'attr_cate_id'=>$attr_cate_id];
           Db::table('goods_attr')->insert($data);
        }
    }


}


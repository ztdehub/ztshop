<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use Session;
use Request;
use gmars\rbac\Rbac;
class Common extends Controller
{
    public function __construct(){
        parent::__construct();
        $a=Session::get('name');
        $this->assign('name',$a);
        if (empty($a)){
            $this->redirect('login/login');
        }else{
            $action=Request::action();
            $class=Request::controller();
            $ass="admin/$class/".$action;
            $str=strtolower($ass);
            //in_array搜索数组中是否含有指定的值
            $arr_class=['Permission','Permissioncategory','Role','User'];
            $arr_action=['add','update','show','del'];
            if (in_array($class,$arr_class)){
                if (in_array($action,$arr_action)){
                    $rbac=new Rbac();
                    $a=$rbac->can($str);
                    if ($a==false){
                        $json=['code'=>'10001','status'=>'error','data'=>'权限不足'];
                        echo json_encode($json);
                        die;
                    }
                }
            }
        }
    }
}
<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use Session;
class Common extends Controller
{
    public function __construct(){
        parent::__construct();
        $a=Session::get('name');
        $this->assign('name',$a);
        if (empty($a)){
            $this->redirect('login/login');
        }
    }
}
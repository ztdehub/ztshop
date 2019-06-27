<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Index
{
    public function index()
    {
        return view('index');
    }
}

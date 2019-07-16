<?php

namespace app\admin\validate;

use think\Validate;

class Permissioncategory extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name'  =>  'require|max:10',
        'description' => 'require|max:100',
    ];
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [
        'name'  =>  '格式不正确',
        'description' =>  '长度不对',
    ];
}

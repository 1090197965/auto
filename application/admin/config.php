<?php

//和主配置进行覆盖, 保留主配置原有的模版替换
$view_replace_str = config('view_replace_str');

//配置文件
return [
	'view_replace_str'       => $view_replace_str + [
		'__PATH__'				=> '/static/admin/index_style',
	],

	//默认错误跳转对应的模板文件
	'dispatch_error_tmpl' => APP_PATH . 'admin/jump_error.html',
	//默认成功跳转对应的模板文件
	'dispatch_success_tmpl' => APP_PATH . 'admin/jump_success.html',
];
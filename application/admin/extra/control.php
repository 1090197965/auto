<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-12
	 * Time: 12:17
	 */

return [
	'url'	=> [
		'会员管理'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> '#',
			'child'	=> [
				'用户管理'	=> [
					'用户列表'	=> 'admin/user/index',
					'登录信息'	=> 'admin/userLogin/index',
				],
				'权限管理'	=> [
					'权限节点'	=> 'admin/authRule/index',
					'角色管理'	=> 'admin/authGroup/index',
					'角色分配'	=> 'admin/authGroupUser/index',
				]
			]
		],
		'订单管理'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> '#',
			'child'	=> [
				'订单列表'	=> 'admin/order/index',
			]
		],
		'资金管理'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> '#',
			'child'	=> [
				'资金记录'	=> 'admin/LogMoney/index',
				'提现申请'	=> 'admin/ApplyDrawMoney/index'
			]
		],
		'内容'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> '#',
			'child'	=> [
				'单页面'			=> 'admin/page/index',
				'前台页面'		=> 'admin/Home/index',
			]
		],
		'业务设置'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> '#',
			'child'	=> [
				'订单设置'	=> 'admin/WebConfig/order',
				'等级设置'	=> 'admin/level/index',
				'积分设置'	=> 'admin/WebConfig/score'
			]
		],
		'网站设置'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> '#',
			'child'	=> [
				'基础设置'	=> 'admin/WebConfig/index',
				'短信'		=> 'admin/WebConfig/phone',
				'验证码'		=> 'admin/WebConfig/verify',
				'安全'		=> 'admin/WebConfig/safe',
			]
		],
		'范例'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> 'admin/demo/index',
		],
		'测试'	=> [
			'ico'	=> '&#xe68e;',
			'url'	=> 'admin/demo/test',
		],
	]
];
<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-13
	 * Time: 23:51
	 */
namespace app\admin\controller;

use app\auto\config\Field;
use app\auto\config\Item;
use app\auto\my_abstract\AbstractAuto;

class User extends AbstractAuto{
	public function setConfig() {
		//QP:TODO: 设置视图功能, 用来显示关联的表
			//QP:TODO: 那这样应该有视图的字段, 应该区分开, 可以搜索, 但是不能添加和编辑
		//QP:TODO: 设置只显示没有数据库相关的单元格
		//QP:TODO: 添加页面尝试用include功能来自定义表单
		//QP:TODO: 自定义搜索
		//QP:TODO: 添加可以批量编辑的字段
		$this->_config->setTableName('demo');
		$this->_config->setFieldEditShow(['id', 'name', 'phone', 'type', 'add_time', 'status']);
		$this->_config->setFieldIndexShow(['id', 'name', 'phone', 'type', 'add_time', 'status']);

		$field = new Field();

		$field->add(Item::get('id', Item::ID, '编号', '唯一且不能修改', true));
		$field->add(Item::get('phone', Item::INT, '手机号', '', true, 220));
		$field->add(Item::get('type', Item::SELECT, '类型', '可选', true, null, [
			'选项1', '选项2', '选项3'
		]));
		$field->add(Item::get('status', Item::SW, '状态', '开启或者关闭', true, null, ['关闭', '开启']));
		$field->add(Item::get('add_time', Item::TIME, '创建时间', '最好不要修改', true));
		$field->add(Item::get('name', Item::STRING, '名称', '唯一, 最好不要修改', true, null, null, null, true));
		
		$this->_config->setField($field);
	}
}

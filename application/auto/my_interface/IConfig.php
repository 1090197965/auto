<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-14
	 * Time: 16:30
	 */
namespace app\auto\my_interface;

use app\auto\config\Field;

interface IConfig {
	/**
	 * @return 获取数据库表名
	 */
	public function getTableName();
	/**
	 * @param mixed 设置数据库表名
	 */
	public function setTableName($tableName);

	/**
	 * 如果是多表关联, 则可以设置
	 * @param $join
	 *
	 * @return mixed
	 */
	public function setJoin(array $join);
	public function getJoin();

	/**
	 * @return 获取默认的筛选条件
	 */
	public function getWhere();
	/**
	 * @param mixed 设置的筛选条件
	 */
	public function setWhere($where);

	/**
	 * @return 获得默认排序
	 */
	public function getOrder();
	/**
	 * @param mixed 设置默认排序
	 */
	public function setOrder($order);

	/**
	 * @return 暂时没用
	 */
	public function getPageLimit();
	/**
	 * @param mixed 暂时没用
	 */
	public function setPageLimit($limit);

	/**
	 * @return Field
	 */
	public function getField();
	/**
	 * @param $field 设置字段信息
	 */
	public function setField(Field $field);

	/**
	 * @return 获得首页显示的字段
	 */
	public function getFieldIndexShow();
	/**
	 * @param mixed 设置首页显示的字段
	 */
	public function setFieldIndexShow(array $fieldShow);

	/**
	 * @return 获得编辑页面显示的字段
	 */
	public function getFieldEditShow();
	/**
	 * @param mixed 设置编辑页面显示的字段
	 */
	public function setFieldEditShow(array $fieldEditShow);

	//以下是页面跳转url的设置接口------------------
	/**
	 * @param mixed 设置首页链接
	 */
	public function setUrlIndexName($url);
	/**
	 * @return 获取首页链接
	 */
	public function getUrlIndexName();

	/**
	 * @param null 设置首页表格数据接口
	 */
	public function setAjaxIndexTableData($url);
	/**
	 * @return 获取首页表格数据接口
	 */
	public function getAjaxIndexTableData();

	/**
	 * @param mixed 设置编辑页链接
	 */
	public function setUrlEditName($url);
	/**
	 * @return 获取编辑页链接
	 */
	public function getUrlEditName();

	/**
	 * @param null 设置编辑处理页面链接
	 */
	public function setHandleEditName($url);
	/**
	 * @return 获得编辑处理页面链接
	 */
	public function getHandleEditName();
	//-----------------------------------------

	//事件--------------------------------------
	/**
	 * 绑定事件
	 *
	 * @param          $event 使用常量来设置事件名
	 * @param callable $function
	 * @param          $isDisplayKeys 如果为true则打印可事件拥有的形参
	 */
	public function on($event, callable $function, $isDisplayKeys = false);

	/**
	 * 检查事件是否有绑定
	 * @param $event
	 *
	 * @return mixed
	 */
	public function issetOn($event);

	/**
	 * 编辑前的操作
	 * @return mixed
	 */
	public function onEdit();

	/**
	 * 添加前的检查操作
	 * @return mixed
	 */
	public function onCheckAdd();

	/**
	 * 保存前的检查操作
	 * @return mixed
	 */
	public function onCheckSave();

	/**
	 * 自定义搜索操作
	 * @return mixed
	 */
	public function onSearch();

	/**
	 * 保存后的操作
	 * @return mixed
	 */
	public function onEnd();

	/**
	 * 显示前的处理
	 * @return mixed
	 */
	public function onShow();
	//事件--------------------------------------

	//显示的按钮---------------------------------

	public function addIndexTool($name, $title, $onClick, $class);

	/**
	 * 获得工具按钮
	 * @return mixed
	 */
	public function getIndexTool();

	/**
	 * 设置列表工具
	 * @param array $tool
	 *
	 * @return mixed
	 */
	public function addIndexItemTool($name, $title, $onClick, $class);

	/**
	 * 获得列表工具
	 * @return mixed
	 */
	public function getIndexItemTool();
	//显示的按钮---------------------------------

	//检查配置是否有问题
	public function check();
}
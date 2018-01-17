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
	 * 设置不能使用编辑功能
	 * @return mixed
	 */
	public function setNotEdit();
	public function checkNotEdit();

	/**
	 * 设置不能删除功能
	 * @return mixed
	 */
	public function setNotDelete();
	public function checkNotDelete();

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

	/**
	 * 获取可以批量设置的列表
	 * @return mixed
	 */
	public function getFieldBatch();
	/**
	 * 设置可以批量设置的字段
	 * @param array $fieldBatch
	 *
	 * @return mixed
	 */
	public function setFieldBatch(array $fieldBatch);

	/**
	 * 检查是否可以使用批量编辑
	 * @return mixed
	 */
	public function checkFieldBatch();

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
	 * @param mixed 设置批量编辑页链接
	 */
	public function setUrlEditBatchName($url);
	/**
	 * @return 获取批量编辑页链接
	 */
	public function getUrlEditBatchName();

	/**
	 * @param null 设置编辑处理页面链接
	 */
	public function setHandleEditName($url);
	/**
	 * @return 获得编辑处理页面链接
	 */
	public function getHandleEditName();

	/**
	 * @param null 设置批量编辑处理页面链接
	 */
	public function setHandleEditBatchName($url);
	/**
	 * @return 获得批量编辑处理页面链接
	 */
	public function getHandleEditBatchName();

	/**
	 * 删除指定id数据的接口
	 * @param $idList
	 *
	 * @return mixed
	 */
	public function setHandleDeleteId($idList);
	/**
	 * 获得删除指定id数据的接口
	 * @return mixed
	 */
	public function getHandleDeleteId();
	//-----------------------------------------

	//事件--------------------------------------
	//QP:TODO: 添加事件 这里也需要添加, 需要EVENT开头
	const EVENT_EDIT = 'Edit';
	const EVENT_CHECK_SAVE = 'CheckSave';
	const EVENT_SEARCH = 'Search';
	const EVENT_END = 'End';
	const EVENT_SHOW = 'Show';
	const EVENT_GET_FORM_LAST = 'GetFormLast';
	const EVENT_BEFORE = 'Before';
	const EVENT_AFTER = 'After';

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
	 * 保存操作结束后的操作
	 * @return mixed
	 */
	public function onEnd();

	/**
	 * 显示前的处理
	 * @return mixed
	 */
	public function onShow();

	/**
	 * 保存之前的操作
	 * @return mixed
	 */
	public function onBefore();

	/**
	 * 保存之后的操作
	 * @return mixed
	 */
	public function onAfter();

	/**
	 * 这里是提取表单后, 获取表单数据的事件
	 * @return mixed
	 */
	public function onGetFormLast();
	//事件--------------------------------------

	//检查配置是否有问题
	public function check();
}
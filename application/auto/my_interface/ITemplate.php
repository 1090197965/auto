<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-14
	 * Time: 16:30
	 */
namespace app\auto\my_interface;

interface ITemplate {
	/**
	 * 用来注入配置以及数据库操作类
	 *
	 *@param IConfig  $config
	 *
	 * @return mixed
	 */
	public function setConfig(IConfig $config);
	/**
	 * 用来注入配置以及数据库操作类
	 *
	 * @param IDataBase $model
	 *
	 * @return mixed
	 */
	public function setDataBase(IDataBase $model);

	/**
	 * 首页模板
	 * @return mixed
	 */
	public function vIndex();

	/**
	 * 编辑页模板
	 * @return mixed
	 */
	public function vEdit();

	/**
	 * 批量操作模版
	 * @return mixed
	 */
	public function vEditBatch();

	/**
	 * 获得表头的json数据
	 * @return mixed
	 */
	public function getTableData();

	public function createHTML();

	/**
	 * 根据连接获得弹出iframe的js代码
	 * @param $url
	 *
	 * @return mixed
	 */
	public function getIframe($url);

	//显示的按钮---------------------------------
	const TOOL_ADD = 'add';
	const TOOL_EDIT = 'edit';
	const TOOL_RELOAD = 'reload';
	const TOOL_DELETE = 'delete';
	const TOOL_ITEM_EDIT = 'edit';
	const TOOL_ITEM_DETETE = 'delete';

	/**
	 * 设置工具按钮
	 * @param $name string 工具的标识 使用常量, TOOL开头的
	 * @param $title string 显示名称
	 * @param $onClick string 点击js事件, 如果为连接, 则自动弹出iframe
	 * @param $ico string 图标, 可以在这里找到http://www.layui.com/doc/element/icon.html
	 * @param $class string 自定义class
	 *
	 * @return mixed
	 */
	public function addIndexTool($name, $title, $onClick, $ico = '', $class = '');

	/**
	 * 获得工具按钮
	 * @return mixed
	 */
	public function getIndexTool();

	/**
	 * 删除显示的工具按钮
	 * @param $name string 使用常量, TOOL开头的
	 *
	 * @return mixed
	 */
	public function removeIndexTool($name);

	/**
	 * 设置列表工具
	 *
	 * @param $name string 使用常量, TOOL_ITEM 开头的
	 * @param $title string 按钮名称
	 * @param $onClick string 点击事件, 如果为连接, 则自动弹出iframe
	 * @param $ico string 图标
	 * @param $class string 类
	 *
	 * @return mixed
	 *
	 */
	public function addIndexItemTool($name, $title, $onClick, $ico = '', $class = '');

	/**
	 * 获得列表工具
	 * @return mixed
	 */
	public function getIndexItemTool();

	/**
	 * 删除显示的工具按钮
	 * @param $name string 使用常量, TOOL_ITEM 开头的
	 *
	 * @return mixed
	 */
	public function removeIndexItemTool($name);
	//显示的按钮---------------------------------
}
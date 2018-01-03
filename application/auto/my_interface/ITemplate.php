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

	//生成js代码------------------------------------------
	/**
	 * 根据url生成弹窗的代码, 需要注意, 生成的代码不会传递任何参数, 也就是当前页面打开此处窗口的功能而已
	 * @param $url
	 *
	 * @return mixed
	 */
	public function getJSOpenIframe($url, $title);

	/**
	 * 根据url生成检查选中, 并且弹窗的代码
	 * 需要注意, 这个会先检测是否选中, 如果有选中则打开iframe窗口
	 * 此窗口会带有一个get值, 值的内容为选中的id, 格式为 1,32,45 这样
	 * @param $url
	 *
	 * @return mixed
	 */
	public function getJSOpenCheckboxIframe($url, $title);

	/**
	 * 行工具使用, 只能用于行按钮
	 * 使用的时候有get传递一个id值, 需要注意
	 * @param $url
	 * @param $title
	 *
	 * @return mixed
	 */
	public function getJSOpenItemIdIframe($url, $title);
	//生成js代码--------------------------------------------

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
	 * @param $onClickJavascript string 点击js事件
	 * @param $ico string 图标, 可以在这里找到http://www.layui.com/doc/element/icon.html
	 * @param $class string 自定义class
	 *
	 * @return mixed
	 */
	public function addIndexTool($name, $title, $onClickJavascript, $ico = '', $class = '');

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
	 * @param $onClickJavascript string 点击事件
	 * @param $ico string 图标
	 * @param $class string 类
	 *
	 * @return mixed
	 *
	 */
	public function addIndexItemTool($name, $title, $onClickJavascript, $ico = '', $class = '');

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
<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-14
	 * Time: 16:09
	 */
namespace app\auto\config;

use app\auto\my_interface\IConfig;

class Config implements IConfig{
	const DEFAULT_PAGE_LIMIT = 20;

	private $handleDeleteId = null;
	private $ajaxIndexTableData = null;
	private $HandleEditName = null;
	private $HandleEditBatchName = null;
	private $urlIndexName = null;
	private $urlEditName = null;
	private $urlEditBatchName = null;
	private $tableName = null;
	private $pageLimit = DEFAULT_INCLUDE_PATH;
	private $where = null;
	private $order = null;
	private $join = null;
	/**
	 * @var Field
	 */
	private $field = null;
	private $fieldIndexShow = null;
	private $fieldEditShow = null;
	private $fieldBatch = null;

	public function getOrder() {
		return $this->order;
	}
	public function setOrder($order) {
		$this->order = $order;
	}
	public function getField() {
		return $this->field;
	}
	public function getHandleEditName() {
		return $this->HandleEditName;
	}
	public function setHandleEditName($HandleEditName) {
		$this->HandleEditName = $HandleEditName;
	}
	public function setField(Field $field) {
		//放入配置
		$this->field = $field;

		//同时设置显示的字段信息
		if(empty($this->fieldEditShow))
			$this->fieldEditShow = $field->getListName();
		if(empty($this->fieldIndexShow))
			$this->fieldIndexShow = $field->getListName();
	}
	public function getFieldIndexShow() {
		return $this->fieldIndexShow;
	}
	public function setFieldIndexShow(array $fieldShow) {
		$this->fieldIndexShow = $fieldShow;
	}
	public function getFieldEditShow() {
		return $this->fieldEditShow;
	}
	public function setFieldEditShow(array $fieldEditShow) {
		$this->fieldEditShow = $fieldEditShow;
	}
	public function getUrlIndexName() {
		return $this->urlIndexName;
	}
	public function setUrlIndexName($indexName) {
		$this->urlIndexName = $indexName;
	}
	public function getUrlEditName() {
		return $this->urlEditName;
	}
	public function setUrlEditName($editName) {
		$this->urlEditName = $editName;
	}
	public function getTableName() {
		return $this->tableName;
	}
	public function setTableName($tableName) {
		$this->tableName = $tableName;
	}
	public function getWhere() {
		return $this->where;
	}
	public function setWhere($where) {
		$this->where = $where;
	}
	public function getPageLimit() {
		return $this->pageLimit;
	}
	public function setPageLimit($limit) {
		$this->pageLimit = $limit;
	}
	public function getAjaxIndexTableData() {
		return $this->ajaxIndexTableData;
	}
	public function setAjaxIndexTableData($AjaxIndexTableData) {
		$this->ajaxIndexTableData = $AjaxIndexTableData;
	}
	public function getJoin() {
		return $this->join;
	}
	public function setJoin(array $join) {
		$this->join = $join;
	}
	public function getHandleDeleteId() {
		return $this->handleDeleteId;
	}
	public function setHandleDeleteId($handleDeleteId) {
		$this->handleDeleteId = $handleDeleteId;
	}
	public function getFieldBatch() {
		return $this->fieldBatch;
	}
	public function setFieldBatch(array $fieldBatch) {
		$this->fieldBatch = $fieldBatch;
	}

	/**
	 * @return null
	 */
	public function getUrlEditBatchName() {
		return $this->urlEditBatchName;
	}

	/**
	 * @param null $urlEditBatchName
	 */
	public function setUrlEditBatchName($urlEditBatchName) {
		$this->urlEditBatchName = $urlEditBatchName;
	}

	/**
	 * @return null
	 */
	public function getHandleEditBatchName() {
		return $this->HandleEditBatchName;
	}

	/**
	 * @param null $HandleEditBatchName
	 */
	public function setHandleEditBatchName($HandleEditBatchName) {
		$this->HandleEditBatchName = $HandleEditBatchName;
	}



	//---事件-----------------------------------------------
	//这里的操作都是on开头, 一个是防止重复名称, 另一个是方便代码提示
	//QP:TODO: 添加事件 如果需要添加事件的话
	//第一需要添加变量, $onXXX
	//第二部需要添加常量, 需要EVENT_XXX, EVENT_开头是必须的
	//第三部需要去IConfig接口声明 onXXX()
	//第四部需要在这个类中实现这个 onXXX()接口

	protected $onEdit;
	protected $onCheckSave;
	protected $onSearch;
	protected $onEnd;
	protected $onShow;		//完成
	protected $onGetFormLast;

	//QP:TODO: 添加事件 这里也需要添加, 需要EVENT开头
	const EVENT_EDIT = 'Edit';
	const EVENT_CHECK_SAVE = 'CheckSave';
	const EVENT_SEARCH = 'Search';
	const EVENT_END = 'End';
	const EVENT_SHOW = 'Show';
	const EVENT_GET_FORM_LAST = 'GetFormLast';

	public function on($event, callable $function, $isDisplayKeys = false) {
		$name = 'on' . $event;

		$this->checkIsThereEvent($name);

		if($isDisplayKeys){
			switch($event){
				case self::EVENT_EDIT:
					break;

				case self::EVENT_CHECK_SAVE:
					dump('三个形参, [$data] [$saveType] 以及 [IHandle类]');
					dump('data是获取到的表单信息');
					dump('其中saveType有两种状态, 可以用Handle::SAVE 或者 Handle::ADD 来区别新增与保存这两种操作');
					dump('第三个Ihandle类用来保存错误信息, 如果检查错误, 可以用Ihandle->setError("error info")来提示错误');
					dump('最后返回值是true或者false, 如果返回true, 则继续进行, 如果反false, 则提示ihandle的错误, 并且总之终止操作, 返回失败的ajax信息');die;//QP:TODO: 断点调试
					break;

				case self::EVENT_SEARCH:
					dump('两个形参, [$get] 和 [$query]');//QP:TODO: 断点调试
					dump('第一个形参是获取到的需要搜索的数据');//QP:TODO: 断点调试
					dump('第二个是搜索用的对象');//QP:TODO: 断点调试
					dump('直接使用$query->where(xxx)的方式来进行搜索, 具体方式可以参考tp5的闭包搜索');//QP:TODO: 断点调试
					dump('直接使用即可, 最后不需要return $query');die;//QP:TODO: 断点调试
					break;

				case self::EVENT_END:
					break;

				case self::EVENT_SHOW:
					dump('一个形参 [$data], 值为一条数据库的信息, 为数组');
					dump('直接return处理好的数据即可');die;//QP:TODO: 断点调试
					break;

				case self::EVENT_GET_FORM_LAST:
					dump('一个形参 [$data], 值为通过表单提交获取到的数据, 需要注意的是, 这里获得的$data是进过程序预处理的数据');die;//QP:TODO: 断点调试
					break;
			}
		}

		$this->$name = $function;
	}
	public function issetOn($event){
		$event = 'on'.$event;
		$this->checkIsThereEvent($event);
		return is_callable($this->$event);
	}
	public function onEdit(){
		if(is_callable($this->onEdit)){
			return call_user_func_array($this->onEdit, func_get_args());
		}
	}
	public function onCheckSave(){
		if(is_callable($this->onCheckSave)){
			return call_user_func_array($this->onCheckSave, func_get_args());
		}
	}
	public function onSearch(){
		if(is_callable($this->onSearch)){
			return call_user_func_array($this->onSearch, func_get_args());
		}
	}
	public function onEnd(){
		if(is_callable($this->onEnd)){
			return call_user_func_array($this->onEnd, func_get_args());
		}
	}
	public function onShow(){
		if(is_callable($this->onShow)){
			return call_user_func_array($this->onShow, func_get_args());
		}
	}
	public function onGetFormLast() {
		if(is_callable($this->onGetFormLast)){
			return call_user_func_array($this->onGetFormLast, func_get_args());
		}
	}

	/**
	 * 检查是否存在事件
	 *
	 * @param $name 事件名称例如onEdit
	 *
	 * @return bool
	 */
	private function checkIsThereEvent($name) {
		//调用得是on开头的, 否则报错
		if (strpos($name, 'on') !== 0) {
			exception($name . ' 此事件不存在, 请使用Item的常量设置事件, 例如 "Item::EDIT"');
		}

		//查找不到此事件
		if (!in_array($name, array_keys(get_class_vars(get_class())))) {
			exception($name . ' 此事件不存在, 请使用Item的常量设置事件, 例如 "Item::EDIT"');
		}
	}
	//---事件-----------------------------------------------

	//显示的按钮---------------------------------------------
	const TOOL_ADD = 'add';
	const TOOL_EDIT = 'edit';
	const TOOL_RELOAD = 'reload';
	const TOOL_DELETE = 'delete';
	const TOOL_ITEM_EDIT = 'edit';
	const TOOL_ITEM_DETETE = 'delete';

	protected $indexTool = [];
	protected $indexItemTool = [];

	public function addIndexTool($name, $title, $onClick, $ico = '', $class = '') {
		$this->indexTool[$name] = [
			'name'	=> $name,
			'title'	=> $title,
			'click'	=> $onClick,
			'class'	=> $class,
			'ico'	=> $ico
		];
	}
	public function getIndexTool() {
		return $this->indexTool;
	}
	public function removeIndexTool($name) {
		unset($this->indexTool[$name]);
	}
	public function removeIndexItemTool($name) {
		unset($this->indexItemTool[$name]);
	}

	public function addIndexItemTool($name, $title, $onClick, $ico = '', $class = '') {
		$this->indexItemTool[$name] = [
			'name'	=> $name,
			'title'	=> $title,
			'click'	=> $onClick,
			'class'	=> $class,
			'ico'	=> $ico
		];
	}

	public function getIndexItemTool() {
		return $this->indexItemTool;
	}

	//显示的按钮---------------------------------------------

	//检查错误, 会抛出异常
	public function check(){
		if(empty($this->urlIndexName)){
			exception('请用 setIndexName 进行设置 列表页方法名');
		}
		if(empty($this->urlEditName)){
			exception('请用 setEditName 进行设置 编辑页方法名');
		}
		if(empty($this->tableName)){
			exception('请用 setTableName 进行设置 表名');
		}
		if(empty($this->field)){
			exception('请用 setField 进行设置 字段信息');
		}else{
			//检查字段是否有错误
			$this->field->check();
		}
		if(empty($this->fieldIndexShow)){
			exception('请用 setFieldIndexShow 进行设置 列表页显示字段');
		}
		if(empty($this->fieldEditShow)){
			exception('请用 setFieldEditShow 进行设置 编辑页显示字段');
		}
	}
}
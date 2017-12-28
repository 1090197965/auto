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

	private $ajaxIndexTableData = null;
	private $HandleEditName = null;
	private $urlIndexName = null;
	private $urlEditName = null;
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

	//---事件-----------------------------------------------
	//这里的操作都是on开头, 一个是防止重复名称, 另一个是方便代码提示
	//QP:TODO: 添加事件 如果需要添加时间的话, 则需要添加一个变量, 同时增加一个常量即可, 记得需要on开头
	public $onEdit;
	public $onCheckAdd;
	public $onCheckSave;
	public $onSearch;
	public $onEnd;
	public $onShow;		//完成

	//QP:TODO: 添加事件 这里也需要添加, 需要EVENT开头
	const EVENT_EDIT = 'Edit';
	const EVENT_CHECK_ADD = 'CheckAdd';
	const EVENT_CHECK_SAVE = 'CheckSave';
	const EVENT_SEARCH = 'Search';
	const EVENT_END = 'End';
	const EVENT_SHOW = 'Show';

	public function on($event, callable $function, $isDisplayKeys = false) {
		$name = 'on' . $event;

		$this->checkIsThereEvent($name);

		if($isDisplayKeys){
			switch($event){
				case self::EVENT_EDIT:
					break;

				case self::EVENT_CHECK_ADD:
					break;

				case self::EVENT_CHECK_SAVE:
					break;

				case self::EVENT_SEARCH:
					break;

				case self::EVENT_END:
					break;

				case self::EVENT_SHOW:
					dump('一个形参 [$data], 值为一条数据库的信息, 为数组');die;//QP:TODO: 断点调试
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
	public function onCheckAdd(){
		if(is_callable($this->onCheckAdd)){
			return call_user_func_array($this->onCheckAdd, func_get_args());
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
	protected $indexTool = [];
	protected $indexItemTool = [];

	public function addIndexTool($name, $title, $onClick, $ico = '', $class = '') {
		$this->indexTool[] = [
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
	public function addIndexItemTool($name, $title, $onClick, $ico = '', $class = '') {
		$this->indexItemTool[] = [
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
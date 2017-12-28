<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-24
	 * Time: 15:43
	 */
namespace app\auto\handle;

use app\auto\config\Config;
use app\auto\config\Item;
use app\auto\my_interface\IConfig;
use app\auto\my_interface\IDataBase;
use app\auto\my_interface\IHandle;
use think\Validate;

class Handle implements IHandle {
	const SAVE = 'save';
	const ADD = 'add';

	/**
	 * @var IDataBase
	 */
	protected $_db = null;
	/**
	 * @var IConfig
	 */
	protected $_config = null;
	private $error = '操作失败';

	public function setError($error) {
		$this->error = $error;
	}
	public function getError() {
		return $this->error;
	}

	public function setDataBase(IDataBase $db) {
		if(empty($this->_db))
			$this->_db = $db;
	}

	public function setConfig(IConfig $config) {
		if(empty($this->_config))
			$this->_config = $config;
	}

	public function editOrSave(){
		$idName = $this->_config->getField()->getIdName();
		$getId = input('get.'.$idName, null);
		$postId = input('post.'.$idName, null);
		if(empty($getId) and empty($postId))
			return self::ADD;
		else{
			return self::SAVE;
		}
	}

	public function validate(array $data){
		$field = $this->_config->getField();
		$rules = [];
		//生成tp5的检查器规则
		foreach ($field->getList() as $item) {
			if($item instanceof Item){
				$t = [];
				if($item->checkIsRequired()){
					$t[] = 'require';
				}
				
				if($item->checkValidate()){
					$t[] = $item->validate;
				}
				
				if(!empty($t))
					$rules[$item->name.'|'.$item->title] = join('|', $t);
			}
		}
		$validate = new Validate($rules);
		if (!$validate->check($data)) {
			$this->error = $validate->getError();
			return false;

		}else{
			//自定义检查错误
			if($this->_config->issetOn(Config::EVENT_CHECK_SAVE)){
				$result = $this->_config->onCheckSave($data, $this->editOrSave(), $this);
				return $result;

			}else{
				return true;
			}
		}
	}

	public function getForm(array $data) {
		$newData = [];

		//这里原本是便利配置的字段, 但是考虑到一些原因, 还是直接遍历提交来的字段把
		$field = $this->_config->getField()->getList();
//		foreach ($this->_config->getField()->getList() as $item) {
		foreach ($data as $name => $item) {
			$item = $field[$name];

			if($item instanceof Item){
				if(isset($data[$item->name]) and $data[$item->name] !== '')
					$value = $data[$item->name];
				else{
					$value = $item->default;
				}

				switch($item->type){
					case Item::TIME:
						$value = strtotime($value)?:null;
						break;

					case Item::SW:
						$value = !empty($value) ? 1 : 0;
						break;
					
					default:
						break;
				}

				$newData[$item->name] = $value;
			}
		}

		return $newData;
	}

	public function editSave(array $data){
		$this->error = '保存失败';
		if($this->editOrSave() == Handle::SAVE){
			return $this->_db->saveForm($data);
		}else{
			return $this->_db->addForm($data);
		}
	}

	public function deleteId($id){
		if(empty($id)){
			$this->error = '未查询到数据';
			return false;
		}

		//QP:TODO: 这里要加入一个删除事件
		$result = $this->_db->removeIdData($id);

		if($result)
			return true;
		else{
			$this->error = $this->_db->getError();
			return false;
		}
	}
}
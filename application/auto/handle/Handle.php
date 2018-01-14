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
			return IHandle::ADD;
		else{
			return IHandle::SAVE;
		}
	}

	public function validate(array $data){
		$field = $this->_config->getField();
		$idName = $field->getIdName();
		$idValue = $data[$idName];

		//验证规则
		$rules = [];
		//需要检查重复性的字段
		$only = [];
		//生成tp5的检查器规则
		foreach ($field->getList() as $item) {
			if($item instanceof Item){
				$t = [];
				//QP:TODO: 这里应该默认检测, 例如int默认检测number等

				//检查是否是必须输入的字段
				if($item->checkIsRequired()){
					$t[] = 'require';
				}

				//自定义的验证
				if($item->checkValidate()){
					$t[] = $item->validate;
				}

				//保存需要检测是否重复的字段
				if($item->checkIsOnly())
					$only[] = $item;
				
				if(!empty($t))
					$rules[$item->name.'|'.$item->title] = join('|', $t);
			}
		}
		//先验证器验证
		$validate = new Validate($rules);
		if (!$validate->check($data)) {
			$this->error = $validate->getError();
			return false;

		}else{
			//重复性检查
			foreach ($only as $item) {
				if(!$this->_db->isOnly($item->name, $data[$item->name], $idValue)){
					$this->error = $item->title . ' 已存在, 不能重复, 请重试';
					return false;
				}
			}

			//自定义检查错误
			if($this->_config->issetOn(IConfig::EVENT_CHECK_SAVE)){
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

		//获取表单数据后事件
		if($this->_config->issetOn(IConfig::EVENT_GET_FORM_LAST)){
			$newData = $this->_config->onGetFormLast($newData);
		}

		return $newData;
	}

	public function editSave(array $data){
		$this->error = '保存失败';

		//保存前的事件
		$isGoOn = true;
		if($this->_config->issetOn(IConfig::EVENT_BEFORE)){
			$isGoOn = $this->_config->onBefore($data, $this->editOrSave(), $this);
		}

		if($isGoOn){
			if($this->editOrSave() == IHandle::SAVE){
				$result = $this->_db->saveForm($data);
			}else{
				$result = $this->_db->addForm($data);
			}

			//保存后的事件
			if($result){
				if($this->_config->issetOn(IConfig::EVENT_AFTER)){
					$result = $this->_config->onAfter($data, $this->editOrSave(), $this);
				}

				if($this->_config->issetOn(IConfig::EVENT_END)){
					$this->_config->onEnd();
				}
			}

			return $result;

		}else{
			return false;
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
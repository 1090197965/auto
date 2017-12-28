<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-24
	 * Time: 15:43
	 */
namespace app\auto\handle;

use app\auto\my_interface\IConfig;
use app\auto\my_interface\IDataBase;
use app\auto\my_interface\IHandle;

class Handle implements IHandle {
	/**
	 * @var IDataBase
	 */
	protected $_db = null;
	/**
	 * @var IConfig
	 */
	protected $_config = null;
	private $error = null;

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

	public function editSave() {

	}

	public function deleteId(){
		$id = input('get.id');
		if(empty($id)){
			$this->error = '未查询到数据';
			return false;
		}

		$result = $this->_db->removeIdData($id);

		if($result)
			return true;
		else{
			$this->error = $this->_db->getError();
			return false;
		}
	}
}
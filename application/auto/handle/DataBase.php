<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-15
	 * Time: 18:39
	 */
namespace app\auto\handle;

use app\auto\config\Config;
use app\auto\config\Field;
use app\auto\config\Item;
use app\auto\my_interface\IConfig;
use app\auto\my_interface\IDataBase;
use think\Db;
use think\db\Query;
use think\Paginator;

class DataBase implements IDataBase{
	const ALIAS = '_main_';

	/**
	 * @var IConfig
	 */
	private $_config;

	/**
	 * @var Query
	 */
	private $db;

	private $error;

	public function getError() {
		return $this->error;
	}

	public function setConfig(IConfig $config){
		$this->_config = $config;
		$this->db = Db::name($this->_config->getTableName());
	}

	public function getIdInfo($id) {
		if(empty($id)){
			return null;
		}else{
			return $this->db->where($this->_config->getField()->getIdName(), $id)->find();
		}
	}

	public function getIndexPageData($limit){
		$get = input('get.');

		$c = $this->_config;
		$data = $this->db
			//设置显示的字段
//			->field($c->getFieldIndexShow())
			//原本是只是显示只显示的字段, 但是修改成所有字段都提出来, 有时候有些字段需要用show事件处理, 但是却不需要显示, 所以这样修改了
			->field($c->getField()->getListName())
			//默认筛选
			->where($c->getWhere())
			//搜索栏筛选
			->where(function($q)use($get){
				foreach ($get as $key => $value){
					//QP:TODO: 这里, 如果恰好数据库有page或者limit这两个字段会导致数据出错, 需要注意
					//设置中含有的字段信息
					$fieldList = $this->_config->getField()->getListName();
					if($value !== '' and in_array($key, $fieldList)){
						//获得常量值
						Field::switchSearch($q, $this->_config->getField()->getList($key), $value);
					}
				}

				if($this->_config->issetOn(IConfig::EVENT_SEARCH)){
					$this->_config->onSearch($get, $q);
				}
			})
			->order($this->_config->getOrder())
			->paginate($limit);

		//显示事件处理
		if($data instanceof Paginator){
			$data->each(function($item){
				if($this->_config->issetOn(IConfig::EVENT_SHOW)){
					$newItem = $this->_config->onShow($item);
					if(!empty($newItem))
						return $newItem;
				}

				return $item;
			});
		}

		return $data;
	}

	public function removeIdData($id) {
		$result = $this->db->where($this->_config->getField()->getIdName(), 'in', $id)->delete();

		if($result){
			return $result;
		}else{
			$this->error = '删除数据失败';
			return null;
		}
	}

	public function saveForm(array $data){
		return $this->db->update($data);
	}

	public function addForm(array $data){
		return $this->db->insert($data);
	}

	public function update($id, $data){
		$this->error = '保存失败';
		return $this->db->where($this->_config->getField()->getIdName(), 'in', $id)->update($data);
	}

	public function isOnly($name, $value, $id = null){
		$data = $this->db->where($name, $value)->count();
		
		//如果没查询到数据直接true
		if($data == 0)
			return true;

		else{
			//如果没设置id, 则只要有数据都是直接false
			if(empty($id)){
				return false;

			}else{
				//如果大于一条, 则直接false
				if($data > 1){
					return false;

				}else{
					$dbId = $this->db->where($name, $value)->value('id');
					return $id == $dbId ? true : false;
				}
			}
		}
	}
}
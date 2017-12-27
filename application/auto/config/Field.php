<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-14
	 * Time: 22:33
	 */
namespace app\auto\config;

class Field{
	//保存设置的字段信息
	private $list = [];
	private $listName = [];
	/**
	 * @var Item
	 */
	private $id = null;

	public function add(Item $item){
		//插入列表
		$this->listName[] = $item->name;
		$this->list[$item->name] = $item;

		//设置主键
		if($item->type == Item::ID)
			$this->id = $item;
	}

	/**
	 * 根据name获得type的常量值
	 * @param $key
	 */
	public function getKey($key){
		return $this->list[$key]->type;
	}

	/**
	 * 检查总体配置是否有错误, 例如必须要有id字段等问题
	 */
	public function check(){
		foreach ($this->getList() as $item) {
			if($item instanceof Item){
				switch($item->type){
					case Item::SW:
						if(count($item->option) != 2){
							exception('SW(状态)类型只能设置两种值');
						}
						break;

					case Item::SELECT:
						if(empty($item->option))
							exception('SELECT类型则option不能为空');
						break;

					case Item::CHECKBOX:
						if(empty($item->option))
							exception('CHECKBOX类型则option不能为空');
						break;
				}

				if(empty($item->name)){
					exception('字段名称不能为空');
				}

				if(empty($item->type)){
					exception('字段类型不能为空');
				}

				if(empty($item->title)){
					exception('字段显示名不能为空');
				}
			}
		}

		//判断主键是否为空
		if(empty($this->id)){
			exception('必须要有ID字段, 请设置');
		}
	}

	/**
	 * 获取主键名
	 * @return idName
	 */
	public function getIdName(){
	    return $this->id->name;
	}

	/**
	 * 获取主键的配置
	 * @return Item
	 */
	public function getIdItem(){
		return $this->id;
	}

	/**
	 * @return array
	 */
	public function getList($key = null) {
		if(empty($key))
			return $this->list;
		else
			return $this->list[$key];
	}

	/**
	 * @return array
	 */
	public function getListName(){
		return $this->listName;
	}
}
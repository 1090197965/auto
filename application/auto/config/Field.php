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
						if(!$item->checkOption())
							exception('SELECT类型则option不能为空');
						break;

					case Item::CHECKBOX:
						if(!$item->checkOption())
							exception('CHECKBOX类型则option不能为空');
						break;
				}

				if(!$item->checkName()){
					exception('字段名称不能为空');
				}

				if(!$item->checkType()){
					exception('字段类型不能为空');
				}

				if(!$item->checkTitle()){
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
	 * @return string
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

	public static function switchSearch($q, $item, $value){
		switch($item->type){
			//这四种类型直接获取搜索
			case Item::ID:
			case Item::INT:
			case Item::SELECT:
			case Item::SW:
				$q->where($item->name, $value);
				break;

			//字符串类型都采用模糊搜索的方式
			case Item::STRING:
			case Item::DESCRIPTION:
			case Item::CONTENT:
				$q->where($item->name, 'like', "%$value%");
				break;

			//时间都是范围选择
			case Item::TIME:
				list($start, $end) = explode('~', $value);
				$start = strtotime($start);
				$end = strtotime($end);
				$q->where($item->name, 'between', [$start, $end]);
				break;

			//用来防止没有筛选的时候where为空的报错
			default:
				$q->where(null);
				break;
		}
	}

	public static function switchGetForm($item, $value){
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

		return $value;
	}
}
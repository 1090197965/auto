<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-15
	 * Time: 11:53
	 */
namespace app\auto\config;

/**
 * Class Item
 * @method checkName()
 * @method checkWidth()
 * @method checkOption()
 * @method checkRemark()
 * @method checkTitle()
 * @method checkHtml()
 * @method checkDefault()
 * @method checkIsOnly()
 * @method checkIsRequired()
 * @method checkIsSearch()
 * @method checkValidate()
 *
 * @package app\auto\config
 */
class Item {
	//QP:TODO: 添加配置 添加变量后, 还需去get方法中设置形参以及常量
	/**
	 * @var 类型
	 */
	public $type;

	/**
	 * 特殊显示的单元格
	 * @var array
	 */
	private static $specialItem = [
		self::SELECT, self::SW, self::TIME
	];

	/**
	 * 获得特殊显示的单元格类型
	 * @return array
	 */
	public static function getSpecialShow(){
		return self::$specialItem;
	}

	//这个是type用的类型常量
	const ID = 11;
	const INT = 1;
	const TIME = 2;
	const STRING = 3;
	const SELECT = 4;
	const CONTENT = 5;
	const CHECKBOX = 6;
	const DESCRIPTION = 7;
	const TREE = 8;
	const IMAGE = 9;
	const IMAGES = 10;
	const SW = 12;	//这个是开关, 只有两种状态, 如果是这个类型, 则option只能为两个长度的数组, 并且不能设置key

	/**
	 * @var 字段名
	 */
	public $name;
	/**
	 * @var 类型为SELECT和CHECKBOX等才可以进行设置
	 */
	public $option;
	/**
	 * @var 宽度
	 */
	public $width;
	/**
	 * @var 备注
	 */
	public $remark;
	/**
	 * @var 显示名称
	 */
	public $title;
	/**
	 * @var 自定义html
	 */
	public $html;
	/**
	 * @var 默认值
	 */
	public $default;
	/**
	 * @var 是否唯一
	 */
	public $isOnly;
	/**
	 * @var 是否必须填写
	 */
	public $isRequired;
	/**
	 * @var 是否可以搜索
	 */
	public $isSearch;
	/**
	 * 填写规则请参考 https://www.kancloud.cn/manual/thinkphp5/129356
	 * @var 验证规则
	 */
	public $validate;
	//需要注意, 如果添加了新的配置, 需要在get中设置, 并且需要在顶部设置check的提示方法

	/**
	 * 使用此方法获得一个字段信息
	 *
	 * @param      $name           字段名
	 * @param      $type           字段类型设置使用常量
	 * @param      $title          显示名称
	 * @param      $remark         提示备注
	 * @param      $isSearch       是否可以搜索
	 * @param      $width          表格宽度
	 * @param      $option         选择框等设置
	 * @param      $default        默认值
	 * @param      $isRequired     是否必须输入
	 * @param      $isOnly         是否唯一
	 * @param null $validate
	 * @param      $html           自定义HTML表单
	 *
	 * @return Item
	 */
	public static function get($name, $type = '', $title = '', $remark = null, $isSearch = null, $width = null, $option = null, $default = null, $isRequired = null, $isOnly = null, $validate = null, $html = null) {
		$item = new Item();

		if(empty($type)){
			switch($name){
				case 'id':
					$type = Item::ID;
					$title = '编号';
					$isSearch = true;
					$remark = '唯一标识符';
					break;

				case 'status':
					$type = Item::SW;
					$title = '状态';
					$isSearch = true;
					break;

				case 'add_time':
					$type = Item::TIME;
					$title = '添加时间';
					$remark = '数据创建的时间';
					$isSearch = true;
					$default = time();
					break;
			}
		}

		$item->type = $type;
		$item->name = $name;
		$item->width = $width;
		$item->option = $option;
		$item->remark = $remark;
		$item->title = $title;
		$item->html = $html;
		$item->default = $default;
		$item->isOnly = $isOnly;
		$item->isRequired = $isRequired;
		$item->isSearch = $isSearch;
		$item->validate = $validate;

		//启用类型可以设置默认值
		if($type == Item::SW and empty($option))
			$item->option = ['关闭', '开启'];

		return $item;
	}

	/**
	 * 检查字段是否存在并且设置
	 * @param $name
	 * @param $arguments
	 *
	 * @return bool
	 */
	public function __call($name, $arguments) {
		if(strpos($name, 'check') === 0){
			$checkField = lcfirst(ltrim($name, 'check'));
			return !empty($this->$checkField);
			
		}else{
			exception($name.'方法不存在');
		}
	}
}
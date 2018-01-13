<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-30
	 * Time: 0:12
	 */
namespace app\common\model;

use think\Model;

class Base extends Model {
	/**
	 * 用来当作下拉框的数据使用, 如果有status字段则自动设置status=1
	 * 如果不需要自动设置status=1, 则$where = null即可
	 * @param      $key
	 * @param      $value
	 * @param null $where
	 *
	 * @return mixed
	 */
	public static function getOption($key, $value, $where = null){
		if(empty($where))
			if(in_array('status', self::getTableFields())){
				$where = ['status' => 1];
			}

		return self::where($where)->column($value, $key);
	}
}
<?php
	/**
	 * Created by PhpStorm.
	 * User: L
	 * Date: 2017/8/11 0011
	 * Time: 15:08
	 */
namespace other;

class Base {
	protected static $instance = null;

	/*初始化*/
	public static function instance(){
		/*单例*/
		if(empty(self::$instance)){
			self::$instance = new static();
		}

		return self::$instance;
	}

	//	public static abstract function help();
}
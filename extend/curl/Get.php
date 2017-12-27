<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2017/6/22 0022
 * Time: 16:10
 */
namespace curl;
use think\Exception;
include 'ganon.php';

class Get {
	/*当前打开的链接*/
	public static $now = null;

	/*打开过的链接的队列*/
	public static $list = [];

	/**
	 * 单例模式+工厂, 爬指定链接
	 *
	 * @param $url
	 *
	 * @return Get
	 */
	public static function url($url, $cookieName = null){
		$list = self::$list;
		$now = null;

		/*是否爬过此链接*/
		if(isset($list[$url])){
			/*直接从队列中获取*/
			$now = $list[$url];

		}else{
			/*去爬此链接, 我修改了一下, 没有使用类库默认的方法, 因为file_get_content好像会有点问题*/
//			$now = @file_get_dom($url);
//			if(!$now){
				$now = str_get_dom(Curl::get($url, '', $cookieName));
//			}

			/*加入队列*/
			$list = self::$list;
			$list[$url] = $now;
			self::$list = $list;
		}

		/*设置成当前打开的链接*/
		self::$now = $now;

		return new self;
	}

	/**
	 * 获取指定dom, 返回多个dom对象
	 *
	 * @param $select
	 *
	 * @return \HTML_Node
	 */
	public static function query($select){
		$html = self::$now;
		return $html($select);
	}

	/**
	 * 获取单个dom对象
	 *
	 * @param $select
	 *
	 * @return \HTML_Node
	 */
	public static function find($select){
		$html = self::$now;
		$node = $html($select);

		if(empty($node)){
			exception('未获得dom对象, 请检查选择器语法是否有误');
		}

		return $node[0];
	}
}
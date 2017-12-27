<?php
	/**
	 * Created by PhpStorm.
	 * User: L
	 * Date: 2017/9/9 0009
	 * Time: 16:23
	 */

namespace other;
use think\Cache;

class TTime extends Base{
	/**
	 * 检查时间
	 * 例如, 设置一天后过期, 再次调用这个方法表示检查是否过期
	 * name名不可重复, 重新设置会导致上一个失效
	 *
	 * @param $name : 标识
	 * @param $time : 过期时间(s)
	 *
	 * @return bool 返回true表示已经过期, 返回false表示还未过期
	 */
	public static function setPast($name, $time){
		return Cache::set($name, true, $time);
	}

	/**
	 * 检查是否过期
	 *
	 * @param $name : 标识
	 *
	 * @return bool 返回true表示已经过期, 返回false表示还未过期
	 */
	public static function isPast($name){
		return !Cache::get($name, false);
	}

	/**
	 * 过去了多长时间
	 *
	 * @param $dateline
	 *
	 * @return bool|string
	 */
	public static function past($dateline){
		if(empty($dateline)) return false;
		$seconds = time() - $dateline;
		if ($seconds < 60){
			return "1分钟前";
		}elseif($seconds < 3600){
			return floor($seconds/60)."分钟前";
		}elseif($seconds < 24*3600){
			return floor($seconds/3600)."小时前";
		}elseif($seconds < 48*3600){
			return date("昨天 H:i", $dateline)."";
		}else{
			return date('Y-m-d', $dateline);
		}
	}
}
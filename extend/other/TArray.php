<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2017/8/11 0011
 * Time: 15:03
 */

namespace other;

class TArray extends Base{
	public static function json($value, $options = 0){
	    return json_encode($value, $options);
	}

	/**
	 * 获取数组的下一个值, 和系统的next不同的是, 此方法是从第一个值开始, 而系统是从第二个值开始
	 *
	 * @param $array
	 *
	 * @return mixed
	 */
	public static function next(&$array){
		$key = key($array);
		$value = $array[$key];

		next($array);
		return $value;
	}

	/**
	 * 递归重组成树
	 *
	 * @param        $array 	: 需要重组的数组
	 * @param string $sonName 	: 存放子节点的key名
	 * @param bool   $isdyadic 	: 是否转换成二维数组
	 * @param int    $pid 		: 需要取的分类级别, 0为取所有
	 * @param string $idName 	: id名称
	 * @param string $pidName 	: pid名称
	 *
	 * @return array
	 */
	public static function tree($array, $sonName = 'children', $isdyadic = false, $idName='id', $pidName = 'pid', $pid = 0){
		if($isdyadic)
			static $newArray = [];
		else
			$newArray = [];

		foreach ($array as $value) {
			if(isset($value[$pidName]) and $value[$pidName] == $pid){
				if($isdyadic){
					$newArray[] = $value;
					if(!isset($value['is_skip']) or !$value['is_skip'])
						self::tree($array, $sonName, $isdyadic, $idName, $pidName, $value[$idName]);

				}else{
					if(!isset($value['is_skip']) or !$value['is_skip'])
						$value[$sonName] = self::tree($array, $sonName, $isdyadic, $idName, $pidName, $value[$idName]);
					$newArray[] = $value;
				}
			}
		}

		return $newArray;
	}

	/**
	 * 可以将其他表中的数据取出来然后拼接到分类中形成树
	 * 此函数是将需要拼接的数据, 重新命名成可以给递归使用的数据格式
	 * 重命名结束后, 可以使用array_merge组合成一个数组后, 然后使用tree在组合成树
	 *
	 * @param $data : 数据
	 * @param $isNotFather : 重命名的是否不作为父节点, 因为id有可能重复
	 * @param $oldPid : 名称
	 * @param $newPid : 名称
	 * @param $oldId : 名称
	 * @param $newId : 名称
	 *
	 * @return int : 重命名后的数组
	 */
	public static function treeRename($data, $isNotFather = false, $oldPid='category_id', $newPid = 'pid', $oldId = 'id', $newId = 'id'){
		foreach ($data as $key => $items) {
			if(isset($items[$oldPid])){
				$data[$key][$newPid] = $items[$oldPid];
			}

			if($oldId != $newId){
				if(isset($items[$oldId])){
					unset($data[$key][$oldId]);
					$data[$key][$newId] = $items[$oldId];
				}
			}

			if($isNotFather){
				$data[$key]['is_skip'] = true;
			}
		}

		return $data;
	}

	/**
	 * 直接将两个数据进行凭借(例如文章分类+文章内容), 其中第二个数据的pid名称不是为pid, 需要重命名
	 * 备注, 如果两个数组的key名一致, 直接merge用tree即可
	 *
	 * @param        $data : 主数组, 一般为分类
	 * @param        $sonData : 子数据, 一般是信息, 例如文章和产品
	 * @param string $sonName : 保存子类的key名称
	 * @param string $oldPid : 需要修改的字数据的pid名称
	 *
	 * @return array
	 */
	public static function treeJoin($data, $sonData, $sonName = 'children', $oldPid='category_id'){
		$newSonData = self::treeRename($sonData, true, $oldPid);
		$newData = array_merge($data, $newSonData);

		return self::tree($newData, $sonName);
	}

	/**
	 * 根据某个值, 对数组进行分组, 成树状
	 *
	 * @param $array
	 * @param $key
	 *
	 * @return mixed
	 */
	public static function group($array, $key){
		$newArray = [];
		foreach ($array as $Key => $Value) {
			if(is_callable($key)){
				$date = $key($Value);
			}else{
				$date = $Value[$key];
			}

			if(isset($newArray[$date])){
				$newArray[$date][] = $Value;
			}else{
				$newArray[$date] = [$Value];
			}

		}

		return $newArray;
	}
}


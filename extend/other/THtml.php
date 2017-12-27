<?php
	/**
	 * Created by PhpStorm.
	 * User: L
	 * Date: 2017/8/25 0025
	 * Time: 15:15
	 */
	namespace other;

class THtml extends Base{
	/*获取标签内的*/
	public static function getTagText($html, $tag, $isReset = false){
		$title = null;
		preg_match_all('/<'.$tag.'.*>.*<\/'.$tag.'>/', $html, $title, PREG_PATTERN_ORDER);

		foreach ($title as $tKey => &$tValue) {
			foreach ($tValue as $Key => &$Value) {
				$Value = strip_tags($Value);
				unset($Value);
			}

			unset($tValue);
		}

		if($isReset){
			if(empty(array_filter($title))){
				return '';
			}else
				$title = $title[0][0];
		}

		return $title;
	}

	/*删除指定标签*/
	public static function removeTag($html, $tag){
		if(!is_array($tag)){
			$tag = [$tag];
		}

		/*删除标题*/
		foreach ($tag as $Key => $Value) {
			$html = preg_replace('/<'.$Value.'.*>.*<\/'.$Value.'>/', "", $html);
		}

		return $html;
	}
}
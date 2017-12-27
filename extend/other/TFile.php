<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2017/8/11 0011
 * Time: 15:04
 */

namespace other;

/**
 * 使用这个类, 填写的路径要么是全路径, 要么是以app为根目录的绝对路径, 例如'/public/static/css/xx.css'这样
 *
 * Class TFile
 * @package other
 */
class TFile extends Base{
	/**
	 * 删除目录以及目录以下的所有文件
	 * @param $path 填写要么是全路径要么是以根目录开始的路径
	 */
	public static function delete($path){
		$path = self::getTruePath($path);

		//目录
		if(is_dir($path)){
			if ($handle = @opendir("$path")) {
				while (false !== ($item = readdir($handle))) {
					if ($item != "." && $item != "..") {
						if (is_dir("$path".DS."$item")) {
							self::delete("$path".DS."$item");
						}
						else {
							unlink("$path".DS."$item");
						}
					}
				}

				closedir($handle);
				rmdir($path);
			}

		//文件
		}else{
			if(is_file($path)){
				unlink($path);
			}else{
				echo '目录不存在';
			}
		}

	}

	private static function getTruePath($path){
		if(is_file($path) or is_dir($path)){
			return $path;

		}else if(is_file(ROOT_PATH.$path) or is_dir(ROOT_PATH.$path)){
			return ROOT_PATH.$path;
		}
	}
}
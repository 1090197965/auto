<?php
	/**
	 * Created by PhpStorm.
	 * User: L
	 * Date: 2017/3/20 0020
	 * Time: 14:29
	 */
namespace upload;

use think\Cache;
use think\File;
use think\Request;

class Image {
	protected static $error = '未获取到图片';
	public static $model = UPLOAD_MODULE_NAME;
	public static $controller = UPLOAD_CONTROLLER_NAME;

	/**
	 * @param $imageName : 图片名
	 * @param $isCache   : 是否为暂时没使用到的图片
	 * @param $notThumb : 是否不要上水印
	 *
	 * @return array
	 */
	protected static function _uploadHandle($imageName, $isCache=false, $notThumb = false){
		if($isCache){
			$file = Request::instance()->file('file');
			$path = self::getCachePath();
		}else{
			$file = Request::instance()->file($imageName);
			$path = self::getSavePath();
		}

		$imageValue = [];
		if(empty($file)){
			self::$error = '未上传图片';
			return false;
		}

		if(is_array($file)){
			foreach ($file as $value) {
				$info = self::_moveHandle($value, $path, $notThumb);
				if($info == false){
//					self::$error = '保存文件失败';
					return false;
				}

				$imageValue[] = $info;
			}
		}else{
			$info = self::_moveHandle($file, $path, $notThumb);
			if($info == false)
				return false;

			$imageValue[] = $info;
		}

		return $imageValue;
	}

	/**
	 * 保存图片的操作, 上传操作+缩略图+水印
	 *
	 * @param $file : 文件对象
	 * @param $path : 保存路径
	 * @param $notThumb : 是否不上水印
	 *
	 * @return bool
	 */
	protected static function _moveHandle($file, $path, $notThumb){
		//打开图片
		$image = \think\Image::open($file->getPathname());

		/*检查格式*/
		if(strpos(web('图片格式', 'jpg,jpeg,png'), $image->type()) === false){
			self::$error = '图片格式不正确';
			return false;
		}

		/*检查大小*/
		if($file->getSize()/1000 > web('图片上传大小', 2000)){
			self::$error = '图片超出'. web('图片上传大小', 2000) . 'KB 最大上传限制';
			return false;
		}
		
		$file = $file->move($path);

		if(!$notThumb){
			$isWater = false;
			/*水印*/
			if(web('是否开启Logo水印', false)){
				$isWater = true;
				$image->water(
					web('水印LOGO地址', './water.jpg'),
					web('水印位置', 9),
					web('水印LOGO透明度', 100)
				);

			}else if(web('是否开启文字水印', false)){
				$isWater = true;
				$image->text(
					web('水印文字内容',''),
					web('水印字体路径','./msyh.ttf'),
					web('水印字体大小', 15),
					web('水印字体颜色', '#000000'),
					web('水印位置', 9)
				);
			}

			if($isWater){
				/*保存水印*/
				$image->save(
					$file->getPath().DS.$file->getFilename(),
					null,
					100,
					web('是否设置隔行扫描', false)
				);
			}
		}

		return $file;
	}

	/*设置缩略图*/
	public static function getThumb($file, $isObj = false, $width, $height){
		if(web('是否启用缩略图', false)) {
			if(!$isObj)
				$file = self::create($file);

			if(empty($file))
				return '';

			/*获取后缀*/
			$last = '.'.explode('.', $file->getFilename())[1];

			/*生成缩略图*/
			$thumbImage = \think\Image::open($file->getPathname());
			$savePath = $file->getPath().DS.$file->getFilename().'_thumb';
			$saveFileName = $width.'_'.$height.'_'.web('缩略图类型', \think\Image::THUMB_SCALING).$last;
			$lastPath = $savePath . DS . $saveFileName;

			/*生成缩略图文件夹*/
			if(!is_dir($savePath)){
				mkdir($savePath, 0777, true);
			}

			$thumbImage
				->thumb($width, $height, web('缩略图类型', \think\Image::THUMB_SCALING))
				->save($lastPath, null, 100);

			return self::getPath($lastPath);

		}else{
			return $file;
		}
	}

	/**
	 * 创建图片操作类
	 *
	 * @param $imageName : 图片名
	 *
	 * @return File
	 */
	public static function create($imageName, $isAll=false){
		$path = $isAll ? $imageName : ROOT_PATH.'public'.$imageName;
		if(is_file($path))
			$file = new File($path);
		else
			$file = null;

		return $file;
	}

	/**
	 * 获得的为数组格式
	 *
	 * @param $imageName
	 *
	 * @return array
	 */
	public static function getArray($imageName, $notThumb = false){
		if(isset($_FILES[$imageName])){
			$return = self::_toType($imageName, 'array', $notThumb);
		}else{
			$return = self::_moveImage($imageName, 'array');
		}
		return $return;
	}

	/**
	 * 获得的为数组格式, 逗号分割
	 *
	 * @param $imageName
	 *
	 * @return string
	 */
	public static function getString($imageName, $notThumb = false){
		if(isset($_FILES[$imageName])){
			$return = self::_toType($imageName, 'string', $notThumb);
		}else{
			$return = self::_moveImage($imageName, 'string');
		}
		return $return;
	}

	/**
	 * 获取图片, 作为检查使用
	 *
	 * @param        $imageName
	 * @param string $prefix
	 *
	 * @return mixed
	 */
	public static function getImage($imageName, $prefix=''){
		$prefix = empty($prefix) ? strtolower(self::$model).'-'.strtolower(self::$controller).'-' : $prefix;
		$cacheArr = Cache::tag('upload')->get($prefix.$imageName.session_id());

		if(!empty($cacheArr)){
			foreach ($cacheArr as $key => $item) {
				$oldPath = ROOT_PATH.'public'.$item['path'];
				$newFile = self::create($oldPath, true);
				$cacheArr[$key]['file'] = $newFile;
			}
		}

		return $cacheArr;
	}

	/**
	 * 检查是否有上传图片
	 *
	 * @param        $imageName
	 * @param string $prefix
	 *
	 * @return mixed
	 */
	public static function checkCache($imageName, $prefix=''){
		$array = self::getImage($imageName, $prefix);

		if(empty($array))
			return false;
		else
			return true;
	}

	/**
	 * 获取网站根目录开始的路径
	 *
	 * @param $image
	 *
	 * @return string
	 */
	public static function getPath($image){
		$path = is_string($image) ? $image :$image->getPathname();
		$path = ltrim($path, ROOT_PATH);
		$path = ltrim($path, 'public');

		return $path;
	}

	/**
	 * 获取完整链接
	 *
	 * @param $path
	 *
	 * @return string
	 */
	public static function getAllPath($path){
		return ROOT_PATH.'public'.$path;
	}

	/**
	 * 删除图片
	 *
	 * @param $imagePath : 图片地址
	 *
	 * @return bool
	 */
	public static function delete($imagePath, $isFile = false){
		if($isFile)
			$file = $imagePath;
		else{
			if(strpos($imagePath, ',') !== false){
				return self::_manyDelete($imagePath);
			}else{
				$file = self::create($imagePath);
			}
		}

		if(empty($file))
			return false;
		else{
			if($file->isFile()){
				 try{
					$path = $file->getPathname();
					unset($file);

					$thumbPath = thumb($imagePath);
					/*是否存在缩略图*/
					if(is_dir('.'.$thumbPath)){
						removeDir('.'.$thumbPath);
					}

					$result = unlink($path);

					return $result;
				 }catch(\Exception $e){
				 	return false;
				 }
			}
		}
	}

	protected static function _manyDelete($image){
		$array = explode(',', $image);

		foreach ($array as $Key => $Value) {
			self::delete($Value);
		}

		return true;
	}

	/**
	 * 上传图片至缓存目录
	 *
	 * @param $saveImageName
	 *
	 * @return bool
	 */
	public static function uploadCache($saveImageName, $prefix){
	    $image = self::_uploadHandle($saveImageName, true, false);
		if($image){
			$imageArray = Cache::tag('upload')->get($prefix.$saveImageName.session_id(), []);
			foreach ($image as $item) {
				$imageArray[] = [
					'path'		=> self::getPath($item),
					'savePath'	=> $item->getSaveName(),
					'name'		=> $item->getFilename()
				];
			}
			Cache::tag('upload')->set($prefix.$saveImageName.session_id(), $imageArray);
			return true;
		}else{
//			self::$error = '保存文件失败';
			return false;
		}
	}

	/**
	 * 移动文件至保存目录
	 *
	 * @param $imageName
	 * @param $type
	 *
	 * @return array|null|string
	 */
	protected static function _moveImage($imageName, $type, $prefix=''){
		$prefix = empty($prefix) ? strtolower(self::$model).'-'.strtolower(self::$controller).'-' : $prefix;
		$cacheArr = Cache::tag('upload')->get($prefix.$imageName.session_id());
		
		if(empty($cacheArr))
			$cacheArr = [];

		$newArr = [];
		foreach ($cacheArr as $item) {
			$oldPath = ROOT_PATH.'public'.$item['path'];
			$newPath = self::getSavePath().DS.$item['savePath'];
			$fatherPath = rtrim($newPath, $item['name']);

			if(!is_dir($fatherPath)){
				mkdir($fatherPath, 0777, true);
			}

			if(is_file($oldPath))
				$result = rename($oldPath, $newPath);
			else
				$result = false;

			if($result){
				$newFile = self::create($newPath, true);
				$newArr[] = $newFile;
			}
		}
		Cache::tag('upload')->rm($prefix.$imageName.session_id());

		switch($type){
			case 'string':
				$string = self::_toString($newArr);

				return $string;
				break;

			case 'array':
				$array = self::_toArray($newArr);

				return $array;
				break;
		}

		return null;
	}

	/**
	 * 转换输出格式
	 *
	 * @param $imageName
	 * @param $type
	 *
	 * @return array|null|string
	 */
	protected static function _toType($imageName, $type, $notThumb){
	    switch($type){
			case 'string':
				try{
					$images = self::_uploadHandle($imageName, false, $notThumb);
					$string = self::_toString($images);

				}catch(\Exception $e){
					$string = '';
				}

				return $string;
				break;

			case 'array':
				try{
					$images = self::_uploadHandle($imageName, false, $notThumb);
					$images = self::_toArray($images);

				}catch(\Exception $e){
					$images = [];
				}

				return $images;
				break;
		}

		return null;
	}

	/**
	 * 处理成数组形式
	 *
	 * @param $images
	 *
	 * @return array
	 */
	protected static function _toArray($images){
		if($images == false)
			return [];
		foreach ($images as $key => $image) {
			if(!empty($image)){
				$path = self::getPath($image);
				$images[$key] = $path;
			}
		}

		return $images;
	}

	/**
	 * 处理成字符串形式
	 *
	 * @param $images
	 *
	 * @return string
	 */
	protected static function _toString($images){
		if($images == false)
			return '';

		$string = '';

		foreach ($images as $key => $image) {
			if(!empty($image)){
				$path = self::getPath($image);
				$string .= $path . ',';
			}
		}
		$string = rtrim($string, ',');

		return $string;
	}

	/**
	 * 保存路径, 通过读取配置以及当前模块, 控制器组合而成
	 *
	 * @return string
	 */
	public static function getSavePath($path = ''){
		$savePath = ROOT_PATH.'public'.DS.config('upload_image.path').DS.strtolower(self::$model).DS.strtolower(self::$controller);

		if(empty($path))
	    	$path = $savePath;
		else{
			$path = $savePath.$path;
		}

		return $path;
	}

	/**
	 * 保存路径, 通过读取配置以及当前模块, 控制器组合而成
	 *
	 * @param string : 路径
	 *
	 * @return string
	 */
	public static function getCachePath($path = ''){
		$savePath = ROOT_PATH.'public'.DS.config('upload_image.cache');

		if(empty($path))
			$path = $savePath;
		else{
			$path = $savePath.$path;
		}

		return $path;
	}

	public static function getError(){
	    return self::$error;
	}
}
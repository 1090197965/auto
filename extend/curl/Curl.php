<?php
	/**
	 * Created by PhpStorm.
	 * User: L
	 * Date: 2017/6/15 0015
	 * Time: 10:15
	 */
namespace curl;

use think\Cache;

class Curl {
	public static function post($url, $postFields, $cookieName = null, $isShowHeader = false, $isRandIp = false){
		$postFields = http_build_query($postFields);

		$ch = curl_init ();

		//如果需要保留cookie, 则需要获取头信息
		if($cookieName or $isShowHeader){
			curl_setopt($ch, CURLOPT_HEADER, 1);
		}else{
			curl_setopt ($ch, CURLOPT_HEADER, 0 );
		}

		//设置cookie
		if($cookieName){
			self::_curlSetCookie($ch, $cookieName);
		}

		curl_setopt ( $ch, CURLOPT_POST, 1 );

		//设置访问ip
		if($isRandIp){
			$headers = self::randIp();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		$result = curl_exec ( $ch );
		curl_close ( $ch );

		if($result){
			if($cookieName or $isShowHeader){
				// 解析HTTP数据流
				list($header, $body) = explode("\r\n\r\n", $result);

				//获取cookie到缓存
				if($cookieName)
					self::_setCookie($header, $cookieName);

				if($isShowHeader){
					return $result;
				}else{
					return $body;
				}

			}else{
				return $result;
			}
		}else{
			return false;
		}
	}

	public static function get($url, $getFields = '', $cookieName = null, $isShowHeader = false,  $isRandIp = false){
		if(!empty($getFields)){
			if(is_array($getFields))
				$getFields = http_build_query($getFields);

			$url .= '?' . $getFields;
		}

		$ch = curl_init();
		//设置抓取的url
		curl_setopt($ch, CURLOPT_URL, $url);

		//设置头文件的信息作为数据流输出 如果需要保留cookie, 则需要获取头信息
		if($cookieName or $isShowHeader){
			curl_setopt($ch, CURLOPT_HEADER, 1);
		}else{
			curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		}

		//设置cookie
		if($cookieName){
			self::_curlSetCookie($ch, $cookieName);
		}

		//设置访问ip
		if($isRandIp){
			$headers = self::randIp();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}

		//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		if(strlen($url) > 5 && strtolower(substr($url,0,5)) == "https" ) {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		}

		//执行命令
		$result = curl_exec($ch);
		//关闭URL请求
		curl_close($ch);

		if($result){
			if($cookieName or $isShowHeader){
				// 解析HTTP数据流
				list($header, $body) = explode("\r\n\r\n", $result);

				//获取cookie到缓存
				if($cookieName)
					self::_setCookie($header, $cookieName);

				if($isShowHeader){
					return $result;
				}else{
					return $body;
				}

			}else{
				return $result;
			}
		}else{
			return false;
		}
	}

	public static function _curlSetCookie(&$ch, $cookieName){
		curl_setopt($ch, CURLOPT_COOKIE, self::getCookie($cookieName));
//		curl_setopt($ch, CURLOPT_COOKIEJAR,  ROOT_PATH.'public/login/'.$cookieName.'.txt'); //存储cookies
//		curl_setopt($ch, CURLOPT_COOKIEFILE, ROOT_PATH.'public/login/'.$cookieName.'.txt'); //使用上面获取的cookies
	}

	public static function getCookie($cookieName){
		$cookie = Cache::get($cookieName, []);
		$array = [];

		if(empty($cookie)){
			return null;

		}else{
			foreach ($cookie as $key => $item) {
				$array[] = "$key=$item";
			}

			return join(';', $array);
		}
	}

	/**
	 * 内部使用的, 用来讲头信息保存到cache中
	 *
	 * @param $header
	 * @param $cookieName
	 */
	private static function _setCookie($header, $cookieName){
		$oldCookie = Cache::get($cookieName);

		// 解析COOKIE
		preg_match_all("/set\-cookie:([^\r\n]*)/i", $header, $matches);

		if(!empty($matches)){
			$cookies=$matches[1];
			foreach ($cookies as $item) {
				list($key, $value) = explode('=', explode(';', $item)[0]);
				$oldCookie[$key] = $value;
			}

			Cache::set($cookieName, $oldCookie);
		}
	}

	/**
	 * 设置cookie
	 * @param $cookieName cookie识别号
	 * @param $key
	 * @param $value
	 */
	public static function setCookie($cookieName, $key, $value){
		$oldCookie = Cache::get($cookieName);

		if($value === null){
			unset($oldCookie[$key]);
		}else{
			$oldCookie[$key] = $value;
		}

		Cache::set($cookieName, $oldCookie);
	}

	/**
	 * 重设虚拟cookie
	 * @param $cookieName
	 */
	public static function resetCookie($cookieName){
		Cache::set($cookieName, null);
	}

	/*
	 * 伪造ip进行访问
	 * */
	public static function task($url){
		$headers = self::randIp();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT,  "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11");      //模拟浏览器类型
		curl_setopt($ch, CURLOPT_TIMEOUT, 300);                               // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_HEADER, 0);                                  // 显示返回的Header区域内容
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);                          // 获取的信息以文件流的形式返回
		$tmpInfo = curl_exec($ch);
		if (curl_errno($ch)) {
			//			print "Error: " . curl_error($ch);
			return false;

		} else {
			curl_close($ch);
			return $tmpInfo;
		}
	}

	protected static function randIP(){
		$ip_long = array(
			array('607649792', '608174079'), //36.56.0.0-36.63.255.255
			array('1038614528', '1039007743'), //61.232.0.0-61.237.255.255
			array('1783627776', '1784676351'), //106.80.0.0-106.95.255.255
			array('2035023872', '2035154943'), //121.76.0.0-121.77.255.255
			array('2078801920', '2079064063'), //123.232.0.0-123.235.255.255
			array('-1950089216', '-1948778497'), //139.196.0.0-139.215.255.255
			array('-1425539072', '-1425014785'), //171.8.0.0-171.15.255.255
			array('-1236271104', '-1235419137'), //182.80.0.0-182.92.255.255
			array('-770113536', '-768606209'), //210.25.0.0-210.47.255.255
			array('-569376768', '-564133889'), //222.16.0.0-222.95.255.255
		);
		$rand_key = mt_rand(0, 9);
		$ip= long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
		$headers['CLIENT-IP'] = $ip;
		$headers['X-FORWARDED-FOR'] = $ip;

		$headerArr = array();
		foreach( $headers as $n => $v ) {
			$headerArr[] = $n .':' . $v;
		}
		return $headerArr;
	}

	/**
	 * 获取重定向后的真实url地址
	 *
	 * @param $url
	 * @param $isRandIp : 是否伪造ip
	 *
	 * @return mixed
	 */
	public static function getUrl($url, $isRandIp = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if($isRandIp){
			$headers = self::randIp();
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		// 不需要页面内容
		curl_setopt($ch, CURLOPT_NOBODY, 1);
		// 不直接输出
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 返回最后的Location
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_exec($ch);
		$info = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
		curl_close($ch);
		return $info;
	}
}
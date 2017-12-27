<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-24
	 * Time: 23:12
	 */
namespace app\common\controller;

use think\Config;
use think\Controller;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class Base extends Controller{
	public function ajax($type, $info = '', $data = [], $header = []){
		if($type === null){
			$icon = 'info';
			$type = false;
		}else if($type)
			$icon = 'success';
		else
			$icon = 'error';

		$result = [
			'result'	=> $type,
			'message' 	=> $info,
			'time' 		=> $_SERVER['REQUEST_TIME'],
			'data' 		=> $data,
			'type' 		=> $icon
		];

		$type =  Config::get('default_ajax_return');
		$response = Response::create($result, $type)->header($header);
		throw new HttpResponseException($response);

		exit();
	}

	/**
	 * 自动返回信息, 根据ajax还是普通页面, 如果是ajax则返回json, 页面则使用success 和error 两个方法
	 *
	 * @param      $result : 状态, 有三种参数可供选择, true(成功), null(提示), false(错误)
	 * @param      $success : 成功信息
	 * @param      $error : 错误信息
	 */
	public function message($result, $success='', $error = '', $info = ''){
		if(Request::instance()->isAjax()){
			if($result === null){
				$message = $info;
			}else if($result){
				$message = $success;
			}else{
				$message = $error;
			}
			$this->ajax($result, $message);

		}else{
			if($result){
				$this->success($success);
			}else{
				$this->error($success);
			}
		}
	}

	/**
	 * 检查是否有错误, 有则跳转报错界面, 没有则返回true信息
	 * 用途主要是如果有报错就报错, 否则继续执行其他程序的简写方式
	 *
	 * @param $result : 状态
	 * @param $error : 返回信息
	 * @param $isAjax : 返回状态
	 */
	public function returnError($result, $error, $isAjax = false){
		if($result == false){
			if($isAjax){
				$this->ajax(false, $error);
			}else{
				$this->error($error);
			}
		}
	}
}
<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2018-01-01
	 * Time: 22:35
	 */
namespace app\admin\controller;

use app\common\controller\Auth;
use app\index\model\User;
use think\Controller;
use think\Db;
use think\Session;

class IndexController extends Base
{
	public function index(){
		$this->view->config = config('control.url');
		$this->view->defaultUrl = url('admin/index/home');
		$this->view->user = Session::get('user');
		return $this->fetch('index');
	}

	/**
	 * 后台首页
	 * @return mixed
	 */
	public function home(){
		dump('后台首页');die;//DIE:TODO
	}
}
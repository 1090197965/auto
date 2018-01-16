<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-14
	 * Time: 16:33
	 */
namespace app\auto\template;

use app\auto\config\Item;
use app\auto\my_interface\IConfig;
use app\auto\my_interface\IDataBase;
use app\auto\my_interface\ITemplate;
use app\common\controller\Base;
use think\Db;
use think\Paginator;

class Layui extends Base implements ITemplate{
	//这里是用来覆盖主配置使用
	private $templateConfig = [
		// 标签库标签开始标记
		'taglib_begin' => '<',
		// 标签库标签结束标记
		'taglib_end'   => '>',

		//自定义标签库
		'taglib_pre_load'     =>    'app\common\taglib\Base',
		//QP:TODO: 缓存, 部署阶段要删除
		'tpl_cache'    => false,
	];
	//用来覆盖配置中的模版替换, 这里都是默认的
	private $replace_str = [
		'__JS__'		=> '/static/common/js',
		'__PLUGIN__'	=> '/static/plugin',

		'__JSMAIN__'	=> '/static/common/js/me_main.js',	//通用js文件, 含有自定义的方法
		'__VUE__'		=> '/static/common/js/vue.min.js'	//vue文件
	];

	/**
	 * @var IConfig
	 */
	private $_config;
	/**
	 * @var IDataBase
	 */
	private $_dataBase;

	public function setConfig(IConfig $config){
		$this->_config = $config;

		//给接口地址赋值
		$this->view->urlIndex	= $this->_config->getUrlIndexName();		//列表页地址
		$this->view->ajaxTable	= $this->_config->getAjaxIndexTableData();  //首页数据接口

		$this->view->urlEdit	= $this->_config->getUrlEditName();			//编辑页地址
		$this->view->handleEdit	= $this->_config->getHandleEditName();		//编辑页面处理地址

		$this->view->urlEditBatch	= $this->_config->getUrlEditBatchName();			//批量编辑页地址
		$this->view->handleEditBatch	= $this->_config->getHandleEditBatchName();		//批量编辑页面处理地址

		//所有字段信息
		$this->view->field = $this->_config->getField()->getList();
		$this->view->editShow = $this->_config->getFieldEditShow();
		$this->view->indexShow = $this->_config->getFieldIndexShow();

		$this->view->Item = 'app\auto\config\Item';
	}

	public function setDataBase(IDataBase $model){
		$this->_dataBase = $model;
	}

	public function vIndex(){
		$this->view->col = json_encode($this->getTableColInfo());
		$this->view->tool = $this->getIndexTool();
		$this->view->itemTool = $this->getIndexItemTool();
		$this->view->widget = $this->getWidget();
		$this->view->stat = $this->getStat();

		return $this->fetch('auto@index/index', [], $this->replace_str, $this->templateConfig);
	}

	public function vEdit() {
		$id = input('get.id', 0);

		$this->view->id = $id;

		$this->view->info = $this->_dataBase->getIdInfo($id);

		return $this->fetch('auto@edit/edit', [], $this->replace_str, $this->templateConfig);
	}

	public function vEditBatch(){
		$id = input('get.id', 0);
		if(empty($id))
			$this->error('批量处理必须要选中才可进行操作');

		$this->view->id = $id;
		$this->view->batch = $this->_config->getFieldBatch();

		return $this->fetch('auto@edit/editBatch', [], $this->replace_str, $this->templateConfig);
	}

	public function getTableData(){
		$page = $this->_dataBase->getIndexPageData(input('get.limit'));

		if($page instanceof Paginator){
			echo  json_encode([
				'code'	=> 0,
				'msg'	=> '获取成功',
				'count'	=> $page->total(),
				'data'	=> $page->items()
			]);

		}else{
			echo  json_encode([
				'code'	=> 0,
				'msg'	=> '数据为空',
				'count'	=> 0,
				'data'	=> []
			]);
		}

		exit();
	}

	/**
	 * 获得layui的表头json信息
	 *
	 * @return array
	 */
	public function getTableColInfo(){
		$info = [[
			'type'=>'checkbox'
		]];
		$c = $this->_config;
		$list = $c->getField()->getList();

		foreach ($c->getFieldIndexShow() as $item) {
			$item = $list[$item];
			if($item instanceof Item){
				$t = [
					'field'		=> $item->name,
					'title'		=> $item->title
				];

				//设置特殊单元格显示, 需要在列表页面中设置
				if(in_array($item->type, Item::getSpecialShow())){
					$t['templet'] = '#box-'.$item->name;
				}

				if($item->checkWidth()){
					$t['width'] = $item->width;
				}

				//layui只能用单引号的json
				$info[] = $t;
			}
		}

		$info[] = [
			'toolbar'	=> '#barDemo',
			'title'		=> '工具'
		];

		return $info;
	}

	public function createHTML() {
		// TODO: Implement createHTML() method.
	}

	//生成js代码--------------------------------------------
	public function getJSOpenIframe($url, $title, $size = ''){
		return '
			$alert.iframe("'.$title.'", "' . $url . '", '.$this->createSizeJs($size).')
		';
	}
	public function getJSOpenCheckboxIframe($url, $title, $size = ''){
		return '
			openIdList(function(idList){
				$alert.iframe("'.$title.'", "' . $url . '?id="+idList, '.$this->createSizeJs($size).');
			})
		';
	}
	public function getJSOpenItemIdIframe($url, $title, $size = '') {
		return '
			$alert.iframe("'.$title.'", "' . $url . '?id="+data.id, '.$this->createSizeJs($size).')
		';
	}
	public function getJSOpenItemGetIframe($url, $get, $title, $size = '') {
		return '
			$alert.iframe("'.$title.'", "' . $url . '?'.$get.', '.$this->createSizeJs($size).')
		';
	}
	private function createSizeJs($size){
		if(empty($size))
			return 'undefined';

		if(count($size) != 2)
			exception('大小的格式只能是两个长度的数组, 例如[100px, 200px] 或者 [100%, 50%]');

		$c = reset($size);
		$k = next($size);

		return "['$c', '$k']";
	}

	//生成js代码--------------------------------------------

	//显示的按钮---------------------------------------------
	protected $indexTool = [];
	protected $hiddenTool = [];
	protected $indexItemTool = [];
	protected $hiddenItemTool = [];

	public function addIndexTool($name, $title, $onClick, $ico = '', $class = '') {
		if(!in_array($name, $this->hiddenTool)){
			$this->indexTool[$name] = [
				'name'	=> $name,
				'title'	=> $title,
				'click'	=> $onClick,
				'class'	=> $class,
				'ico'	=> $ico
			];
		}
	}
	public function getIndexTool() {
		return $this->indexTool;
	}
	public function removeIndexTool($name) {
		$this->hiddenTool[$name] = $name;
	}
	public function removeIndexItemTool($name) {
		$this->hiddenItemTool[$name] = $name;
	}
	public function addIndexItemTool($name, $title, $onClick, $ico = '', $class = '') {
		if(!in_array($name, $this->hiddenItemTool)){
			$this->indexItemTool[$name] = [
				'name'	=> $name,
				'title'	=> $title,
				'click'	=> $onClick,
				'class'	=> $class,
				'ico'	=> $ico
			];
		}
	}
	public function getIndexItemTool() {
		return $this->indexItemTool;
	}
	//显示的按钮---------------------------------------------

	//自定义html模块-----------------------------------------
	private $_widget = [];
	public function addWidget($title, $widget, $widgetArgs = null) {
		$this->_widget[$title] = ['widget'=>$widget, 'args'=>$widgetArgs];
	}

	public function getWidget() {
		return $this->_widget;
	}

	private $_stat = [];
	public function addStat($title, array $statArr) {
		$this->_stat[$title] = $statArr;
	}

	public function getStat() {
		return $this->_stat;
	}
	//自定义html模块-----------------------------------------
}
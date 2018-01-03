<?php
namespace app\auto\my_abstract;

use app\auto\config\Config;
use app\auto\config\Item;
use app\auto\handle\DataBase;
use app\auto\handle\Handle;
use app\auto\my_interface\IDataBase;
use app\auto\my_interface\IHandle;
use app\auto\my_interface\ITemplate;
use app\auto\template\Layui;
use app\auto\my_interface\IAuto;
use app\auto\my_interface\IConfig;
use app\common\controller\Base;
use think\Db;
use think\Validate;

abstract class AbstractAuto extends Base implements IAuto
{
	/**
	 * @var IConfig
	 */
	protected $_config = null;
	/**
	 * @var IDataBase
	 */
	protected $_dataBase = null;
	/**
	 * @var ITemplate
	 */
	protected $_template = null;
	/**
	 * @var IHandle
	 */
	protected $_handle = null;

	/**
	 * 初始化整套流程
	 * 1, 设置所有页面,接口地址
	 * 2, 自动注入配置,模版以及数据库操控类
	 */
	public function _initialize(){
		parent::_initialize();
		$this->_config = new Config();
		$this->_template = new Layui();
		$this->_dataBase = new DataBase();
		$this->_handle = new Handle();

		//设置配置类
		$c = &$this->_config;

		//设置基础的配置， 例如ajax接口地址， 编辑页面地址等等
		$c->setUrlIndexName(url('index'));
		$c->setUrlEditName(url('edit'));
		$c->setUrlEditBatchName(url('editBatch'));

		$c->setAjaxIndexTableData(url('ajaxGetIndexTableData'));

		$c->setHandleEditName(url('editHandle'));
		$c->setHandleEditBatchName(url('editBatchHandle'));
		$c->setHandleDeleteId(url('deleteIdHandle'));

		//执行子类的配置
		$this->setConfig();

		//检查配置是否有问题
		$c->check();

		//注入配置
		$this->_dataBase->setConfig($c);

		//注入配置
		$this->_handle->setConfig($c);
		$this->_handle->setDataBase($this->_dataBase);

		//注入配置
		$this->_template->setConfig($c);
		$this->_template->setDataBase($this->_dataBase);

		//设置显示的按钮, 这里需要懂layui框架
		$this->_template->addIndexTool(ITemplate::TOOL_RELOAD, '', '
			 location.reload();
		', '&#x1002;');

		//添加按钮
		$this->_template->addIndexTool(
			ITemplate::TOOL_ADD,
			'添加',
			$this->_template->getJSOpenIframe($c->getUrlEditName(), '添加'),
			'&#xe654;'
		);

		//删除按钮
		$this->_template->addIndexTool(ITemplate::TOOL_DELETE, '删除', '
			openIdList(function(idList){
				$alert.delete("确认删除编号为: "+idList+" 的数据吗? ", "'.$c->getHandleDeleteId().'?id="+idList, function(){
					tableReload();
				});
			})
		', '&#xe640;', 'layui-btn-danger');

		//批量编辑
		if(!empty($c->getFieldBatch()))
			$this->_template->addIndexTool(
				ITemplate::TOOL_EDIT,
				'批量编辑',
				$this->_template->getJSOpenCheckboxIframe($c->getUrlEditBatchName(), '批量编辑'),
				'&#xe642;'
			);

		//行编辑
		$this->_template->addIndexItemTool(ITemplate::TOOL_ITEM_EDIT, '编辑',
			$this->_template->getJSOpenItemIdIframe($c->getUrlEditName(), '编辑')
			, '&#xe642;');
		//行删除
		$this->_template->addIndexItemTool(ITemplate::TOOL_ITEM_DETETE, '删除', '
			$alert.delete("确认删除编号为: "+data.id+" 的数据吗? ", "'.$c->getHandleDeleteId().'?id="+data.id, function(){
				tableReload();
			});
		', '&#xe640;', 'layui-btn-danger');

	}

	/**
	 * 列表页
	 * @return mixed
	 */
	public function index(){
		return $this->_template->vIndex();
	}

	/**
	 * 列表页的表数据接口
	 * 提交类型 : get
	 * 提交参数 : [
	 * 		page 	=> 1,  //页码
	 * 		limit 	=> 10, //分页数量
	 * 		phone	=> 110 //需要搜索的字段, 以及字段的值
	 * ]
	 * 返回的参数 : [
	 * 		code	=> 0,	//状态
	 *		msg		=> '获取成功', //信息
	 *		count	=> $page->total(), //查询的总数量
	 *		data	=> $page->items()  //数据
	 * ]
	 */
	public function ajaxGetIndexTableData(){
		$this->_template->getTableData();
	}

	/**
	 * 编辑页
	 * @return mixed
	 */
	public function edit(){
		return $this->_template->vEdit();
	}

	/**
	 * 批量处理页面
	 */
	public function editBatch(){
		return $this->_template->vEditBatch();
	}

	/**
	 * 处理编辑操作
	 * 接受类型: post
	 */
	public function editHandle(){
		$data = input('post.');
		if($this->_handle->validate($data)){
			$data = $this->_handle->getForm($data);
			$result = $this->_handle->editSave($data);
			$this->message($result, '保存成功!', $this->_handle->getError());

		}else
			$this->ajax(false, $this->_handle->getError());
	}

	/**
	 * 批量处理操作
	 */
	public function editBatchHandle(){
		$idName = $this->_config->getField()->getIdName();
		$data = input('post.');
		//取出需要修改的主键
		$id = $data[$idName];
		//消除主键
		unset($data[$idName]);

		//筛选出填写了数据的表单
		$data = array_filter($data, function($value){
			return $value === '' ? false : true;
		});

		if(empty($data))
			$this->ajax(false, '未获取到修改内容');

		$field = $this->_config->getField()->getList();

		//拼写验证规则
		$rules = [];
		foreach ($data as $name => $item) {
			$item = $field[$name];
			if($item instanceof Item){
				if($item->checkValidate()){
					$rules[$item->name . '|'. $item->title] = $item->validate;
				}
			}
		}
		//验证表单
		$validate = new Validate($rules);
		if (!$validate->check($data)) {
			$this->ajax(false, $validate->getError());

		}else{
			$data = $this->_handle->getForm($data);
			//保存
			$result = $this->_dataBase->update($id, $data);

			$this->message($result, '批量修改成功!', $this->_dataBase->getError());
		}
	}

	/**
	 * 删除数据库数据的接口
	 * 提交类型 : get
	 * 提交参数 : [
	 * 		id 	=> '1,2,3',  //id的值, 可以只是一个单值
	 * ]
	 * 返回的参数 : 标准ajax返回
	 */
	public function deleteIdHandle(){
		$id = input('get.id');
		$this->message($this->_handle->deleteId($id), '删除成功!', $this->_handle->getError());
	}

	public function getConfig(){
		return $this->_config;
	}

	/**
	 * 这个是设置配置文件的方法, 具体可以参考IConfig中声明的接口
	 * @return mixed
	 */
	abstract public function setConfig();
}

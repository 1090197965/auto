<?php
namespace app\auto\my_abstract;

use app\auto\config\Config;
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
		$c = &$this->_config;

		//设置配置类
		if(empty($this->_config)){
			$c = new Config();

			//设置基础的配置， 例如ajax接口地址， 编辑页面地址等等
			$c->setUrlIndexName(url('index'));
			$c->setUrlEditName(url('edit'));
			$c->setAjaxIndexTableData(url('ajaxGetIndexTableData'));
			$c->setHandleEditName(url('editHandle'));
			$c->setHandleDeleteId(url('deleteIdHandle'));

			//执行子类的配置
			$this->setConfig();

			//设置显示的按钮, 这里需要懂layui框架
			//添加按钮
			$c->addIndexTool('add', '添加', '
				$alert.iframe("添加", "' . $c->getUrlEditName() . '")
			', '&#xe654;');
			//删除按钮
			$c->addIndexTool('delete', '删除', '
				openIdList(function(idList){
					$alert.delete("确认删除编号为: "+idList+" 的数据吗? ", "'.$c->getHandleDeleteId().'?id="+idList);
				})
			', '&#xe640;');
			//批量编辑
			$c->addIndexTool('edit', '批量编辑', '
				openIdList(function(idList){
					$alert.iframe("批量编辑", "' . $c->getUrlEditName() . '?id="+idList);
				})
			', '&#xe642;');

//			$c->addIndexItemTool('detail', '查看', '
//				layer.msg("选中的手机号是"+data.phone);
//			', 'layui-btn-primary');

			//编辑
			$c->addIndexItemTool('edit', '编辑', '
				layui.layer.open({
					type: 2,
					title: "很多时候，我们想最大化看，比如像这个页面。",
					shadeClose: true,
					shade: 0.5,
					maxmin: true, //开启最大化最小化按钮
					area: ["993px", "500px"],
					content: "' . $c->getUrlEditName() . '?id="+data.id
				});
			', '&#xe642;');
			//删除
			$c->addIndexItemTool('del', '删除', '
				layer.confirm("真的删除行么", function(index){
					obj.del();
					layer.close(index);
				});
			', '&#xe640;', 'layui-btn-danger');

			//检查配置是否有问题
			$c->check();
		}

		//注入数据库操作类
		if(empty($this->_dataBase)){
			$this->_dataBase = new DataBase();
			$this->_dataBase->setConfig($c);
		}

		//逻辑层
		if(empty($this->_handle)){
			$this->_handle = new Handle();
			$this->_handle->setConfig($c);
			$this->_handle->setDataBase($this->_dataBase);
		}

		//注入模版类
		if(empty($this->_template)){
			$this->_template = new Layui();
			$this->_template->setConfig($c);
			$this->_template->setDataBase($this->_dataBase);
		}
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
	 * 处理编辑操作
	 */
	public function editHandle(){
		$this->_handle->editSave();
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
		$this->returnError($id, '为查询到数据', true);

		$this->ajax(true, "删除编号为 $id 的数据");
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

<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-14
	 * Time: 16:30
	 */
namespace app\auto\my_interface;

interface ITemplate {
	/**
	 * 用来注入配置以及数据库操作类
	 *
	 *@param IConfig  $config
	 *
	 * @return mixed
	 */
	public function setConfig(IConfig $config);
	/**
	 * 用来注入配置以及数据库操作类
	 *
	 * @param IDataBase $model
	 *
	 * @return mixed
	 */
	public function setDataBase(IDataBase $model);

	/**
	 * 首页模板
	 * @return mixed
	 */
	public function vIndex();

	/**
	 * 编辑页模板
	 * @return mixed
	 */
	public function vEdit();

	public function getTableData();

	public function createHTML();
}
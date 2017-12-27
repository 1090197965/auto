<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-15
	 * Time: 18:40
	 */
namespace app\auto\my_interface;

interface IDataBase {
	/**
	 * 用来注入配置以及数据库操作类
	 *
	 *@param IConfig  $config
	 *
	 * @return mixed
	 */
	public function setConfig(IConfig $config);

	/**
	 * 获得列表页的分页数据
	 * @param $limit 分页条数
	 * @return mixed
	 */
	public function getIndexPageData($limit);

	/**
	 * 获得指定的id信息
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getIdInfo($id);
}
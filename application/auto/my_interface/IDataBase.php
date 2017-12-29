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

	/**
	 * 根据id删除数据
	 * @param $id string 可以是 1,2,3这样多数, 也可以是1这样单个
	 *
	 * @return mixed
	 */
	public function removeIdData($id);

	/**
	 * 保存表单
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function saveForm(array $data);
	public function addForm(array $data);

	/**
	 * 获取错误信息
	 * @return mixed
	 */
	public function getError();

	/**
	 * 根据id修改指定的信息
	 * @param $id string 如果是1,2这种格式, 则批量修改
	 * @param $data
	 *
	 * @return mixed
	 */
	public function update($id, $data);

	/**
	 * 指定字段是否有重复值
	 * @param $name
	 * @param $value
	 * @param $id int 不填写则查询到数据就返回false, 如果填写了, 则查询到仅一条, 且id相等, 则返回true
	 *
	 * @return mixed true则表示目前查无重复值
	 */
	public function isOnly($name, $value, $id);
}
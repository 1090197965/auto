<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-24
	 * Time: 15:44
	 */

namespace app\auto\my_interface;
interface IHandle {
	const SAVE = 'save';
	const ADD = 'add';

	/**
	 * 注入数据库类
	 * @param IDataBase $db
	 *
	 * @return mixed
	 */
	public function setDataBase(IDataBase $db);

	/**
	 * 租入配置
	 * @param IConfig $config
	 *
	 * @return mixed
	 */
	public function setConfig(IConfig $config);

	/**
	 * 根据id删除数据库信息, 可以多条一起删除, 可以查看源代码
	 * @param $id
	 *
	 * @return mixed
	 */
	public function deleteId($id);

	/**
	 * 获取错误信息
	 * @return mixed
	 */
	public function getError();

	/**
	 * 设置错误信息
	 * @param $error
	 *
	 * @return mixed
	 */
	public function setError($error);

	/**
	 * 验证器
	 * 验证器的规则是自动生成的
	 * @param array $data 需要验证的数据
	 *
	 * @return mixed
	 */
	public function validate(array $data);

	/**
	 * 获取表单数据
	 * @param array $data 表单数据
	 *
	 * @return mixed
	 */
	public function getForm(array $data);

	/**
	 * 编辑保存
	 * @param array $data
	 *
	 * @return mixed
	 */
	public function editSave(array $data);

	/**
	 * 获取当前是编辑还是新增
	 * @return mixed
	 */
	public function editOrSave();
}
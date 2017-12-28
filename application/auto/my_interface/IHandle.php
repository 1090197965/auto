<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-24
	 * Time: 15:44
	 */

namespace app\auto\my_interface;
interface IHandle {
	//QP:TODO: 加上备注
	public function setDataBase(IDataBase $db);
	public function setConfig(IConfig $config);
	public function deleteId($id);
	public function getError();
	public function setError($error);
	public function validate(array $data);
	public function getForm(array $data);
	public function editSave(array $data);
	public function editOrSave();
}
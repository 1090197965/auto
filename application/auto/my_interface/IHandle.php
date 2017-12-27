<?php
	/**
	 * Created by PhpStorm.
	 * User: Administrator
	 * Date: 2017-12-24
	 * Time: 15:44
	 */

namespace app\auto\my_interface;
interface IHandle {
	public function editSave();
	public function setDataBase(IDataBase $db);
	public function setConfig(IConfig $config);
}
<?php

abstract class eVias_Service_Abstract
{
	protected function _initDb() {
		// will only init the database in not MVC mode (cron, ..)
		if (! eVias_ArrayObject_Db::getDefaultAdapter()) {
			$connData = array(
				'host'		=> 'localhost',
				'dbname'	=> 'evias',
				'user'		=> 'root',
				'password'  => 'xaJAe7uu'
			);

			eVias_ArrayObject_Db::setDefaultAdapter(
				new Zend_Db_Adapter_Pdo_Mysql($connData));
		}
	}
}

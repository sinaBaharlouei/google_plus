<?php

class Application_Model_UserMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_User';

	public function increaseView() {

		$db = Zend_Registry::get('db');
		$sql = "update google_user set `view` = `view` + 1 where id = " . get_user_id() . ";";
		$db->query($sql);
	}
}


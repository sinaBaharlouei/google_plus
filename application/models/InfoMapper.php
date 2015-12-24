<?php

class Application_Model_InfoMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Info';


	public function update($key, $value, $category) {

		$db = Zend_Registry::get('db');
		$sql = "update user_info set `value` = '" . $value .  "' where user_id = " . get_user_id() .
			   " and category = '" . $category . "' and `title` = '" . $key . "';";
		$db->query($sql);
	}
}

<?php

class Application_Model_NotificationMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Notification';


	public function Seen() {

		$db = Zend_Registry::get('db');
		$sql = "update user_notification set `is_seen` = 1 where user_id = " . get_user_id() . ";";
		$db->query($sql);
	}
}


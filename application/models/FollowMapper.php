<?php

class Application_Model_FollowMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Follow';


	public function getFollowersNumber($user_id) {

		$select = $this->getDbTable()->select();
		$select->from("user_follow", array("number" =>new Zend_Db_Expr("COUNT(follower_id)")));
		$select->where("followed_id = ?", $user_id);

		$result_set = $this->getDbTable()->fetchAll($select);


		return $result_set[0]['number'];
	}

	public function InsertFollower($follower, $followed) {

		$row = $this->findByTwoColumns('follower_id', $follower, 'followed_id', $followed);
		if(empty($row)) {
			$db = Zend_Registry::get('db');
			$sql = "insert into user_follow values(" . $follower .  "," . $followed . ");";
			$db->query($sql);
		}
	}

	public function getFriend($follower, $followed) {

		$row = $this->findByTwoColumns('follower_id', $follower, 'followed_id', $followed);
		if(empty($row)) {
			$db = Zend_Registry::get('db');
			$sql = "insert into user_follow values(" . $follower .  "," . $followed . ");";
			$db->query($sql);
		}
	}
}

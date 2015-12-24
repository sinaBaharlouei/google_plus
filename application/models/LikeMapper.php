<?php

class Application_Model_LikeMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Like';

	public function like($user_id , $post_id) {

		$like_elm = $this->findByTwoColumns('user_id', $user_id, 'post_id', $post_id);
		$db = Zend_Registry::get('db');

		if(empty($like_elm)) {
			$sql = "insert into post_like VALUES(" . $user_id . "," . $post_id .  ",0);";
		}
		else {
			$sql = "delete from post_like where post_id = " . $post_id . " and user_id=" . $user_id . " and type=0;";
		}

		$db->query($sql);

		$select = $this->getDbTable()->select();
		$select->from('post_like', array('like' => new Zend_Db_Expr('COUNT(user_id)')));
		$select->where('post_id = ?', $post_id);

		$res = $this->getDbTable()->fetchAll($select);

		return $res[0]['like'];
	}

	public function count($post_id) {


		$select = $this->getDbTable()->select();
		$select->from('post_like', array('like' => new Zend_Db_Expr('COUNT(user_id)')));
		$select->where('post_id = ?', $post_id);

		$res = $this->getDbTable()->fetchAll($select);

		return $res[0]['like'];
	}



}


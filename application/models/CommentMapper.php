<?php

class Application_Model_CommentMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Comment';


	public function getComments($post_id, $isLimit = true) {

		$select = $this->getDbTable()->select()->setIntegrityCheck(false);
		$select->from('user_comment', array('content', 'created_at', 'user_id'));
		$select->join('google_user', 'google_user.id = user_comment.user_id' , array('username'));

		$select->where('user_comment.post_id = ?', $post_id);
		$select->order('user_comment.created_at');

		if($isLimit)
			$select->limit(2);

		$result_set = $this->getDbTable()->fetchAll($select);

		$entries = array();

		foreach ($result_set as $row) {
			$array = $row->toArray();
			remove_null_values($array);
			$entries[] = $array;
		}

		return $entries;
	}

	public function count($id) {

		$select = $this->getDbTable()->select();
		$select->from('user_comment', array('comments' => new Zend_Db_Expr('COUNT(id)')));
		$select->where('post_id = ?', $id);

		$res = $this->getDbTable()->fetchAll($select);

		return $res[0]['comments'];
	}
}

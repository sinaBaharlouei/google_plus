<?php

class Application_Model_PostMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Post';


	public function getPostInfo($post_id) {


		$select = $this->getDbTable()->select()->setIntegrityCheck(false);

		$select->from('user_post', array('text' => 'content', 'date' => 'created_at', 'updated_at'));
		$select->join('google_user', 'google_user.id = user_post.user_id', array('author' => 'username', 'user_id' => 'id'));

		$select->where('user_post.id = ?', $post_id);
		$result_set = $this->getDbTable()->fetchAll($select);

		$entries = array();

		foreach ($result_set as $row) {
			$array = $row->toArray();
			remove_null_values($array);
			$entries[] = $array;
		}

		return $entries[0];
	}

	public function getPosts(array $user_ids, $tag = null, $name = null, $order = 'created_at') {


		$ids = array();

		if(!empty($tag)) {

			$tag_mapper = new Application_Model_TagMapper();
			$result = $tag_mapper->findAllByTwoColumns('content', $tag, 'type', TYPE_TAG);

			foreach($result as $row)
				$ids[] = $row['post_id'];

		} else
		{
			$select = $this->getDbTable()->select();
			$select->setIntegrityCheck(false);

			$select->from("user_post", array('id'));

			if(!empty($name)) {
				$select->join('google_user', 'user_post.user_id = google_user.id', array('username'));
				$select->where('google_user.username = ?', $name);
			}
			else
				$select->where('user_id IN(?)', $user_ids);

			if($order == 'created_at' || $order== '')
				$select->order("user_post.created_at" . ' DESC');

			elseif($order == "hot") {
				$select->order("user_post.comment_number" . ' DESC');
			}
			$result = $this->getDbTable()->fetchAll($select);

			foreach($result as $row)
				$ids[] = $row['id'];

		}
		return $ids;

	}

	public function like($user_id, $post_id) {
	}
}


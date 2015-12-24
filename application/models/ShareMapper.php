<?php

class Application_Model_ShareMapper extends Application_Model_ParentMapper
{
	protected $_dbTableName = 'Application_Model_DbTable_Share';


	public function share($user_id , $post_id) {


		$post_mapper = new Application_Model_PostMapper();
		$user_mapper = new Application_Model_UserMapper();

		$shared_post = $post_mapper->find($post_id);

		$user = $user_mapper->find($shared_post['user_id']);



		$share_elm = $this->findByTwoColumns('user_id', $user_id, 'post_id', $post_id);
		$db = Zend_Registry::get('db');

		if(empty($share_elm)) {

			$sql = "insert into post_share VALUES(" . $user_id . "," . $post_id .  ");";
			$db->query($sql);


			$post_model = new Application_Model_Post();
			$username = $user['username'];

			$post_model->_fields['user_id'] = get_user_id();
			$post_model->_fields['content'] = "The Post originally shared by $username: \n" . $shared_post['content'];
			$post_model->_fields['comment_number'] = 0;
			$post_model->_fields['is_reported'] = 0;
			$post_model->_fields['updated_at'] = time();

			$new_id = $post_mapper->save($post_model);


			$path = APPLICATION_PATH . "/../public/post_pic/" . "$post_id.png";
			$path2 = APPLICATION_PATH . "/../public/post_pic/$new_id.png";

			copy($path, $path2);

			return true;
		}

		return false;
	}


	public function count($post_id) {


		$select = $this->getDbTable()->select();
		$select->from('post_share', array('share' => new Zend_Db_Expr('COUNT(user_id)')));
		$select->where('post_id = ?', $post_id);

		$res = $this->getDbTable()->fetchAll($select);

		return $res[0]['share'];
	}
}


<?php

class PostController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function homeAction() {

		$request = $this->getRequest();

		$this->view->isAdmin = is_admin();

		$name = $request->getParam("name");
		$order = $request->getParam('order');
		$tag = $request->getParam('tag');

		$xml_loc = "http://plus.local/post/getposts";

		if(!empty($tag))
			$xml_loc .= "/tag/$tag";

		elseif(!empty($name))
			$xml_loc .= "/name/$name";

		elseif(!empty($order))
			$xml_loc .= "/order/$order";

		$this->view->xml = $xml_loc;
		$user_mapper = new Application_Model_UserMapper();

		$user = $user_mapper->find(get_user_id());
		$this->view->username = $user['username'];
	}

	public function getpostsAction() {

		$request = $this->getRequest();

		$order = $request->getParam('order');
		$tag = $request->getParam('tag');
		$name = $request->getParam('name');

		$user_id = get_user_id();

		$user_ids = array();

		$user_ids[] = $user_id;

		$follow_mapper = new Application_Model_FollowMapper();
		$friends = $follow_mapper->findAllByColumn('follower_id', $user_id);

		if(!empty($friends)) {
			foreach($friends as $friend) {
				$user_ids[] = $friend['followed_id'];
			}
		}
		$post_mapper = new Application_Model_PostMapper();
		$posts_ids = $post_mapper->getPosts($user_ids, $tag, $name, $order);

		$xml_text = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_text .= '<posts>';

		foreach($posts_ids as $post_id) {

			$xml_text .= '<post>' . "http://plus.local/post/postxml/id/" . $post_id . "</post>";
		}

		$xml_text .= '</posts>';

		header("Content-type: text/xml");
		$xml = new SimpleXMLElement($xml_text);
		echo $xml->asXML();
		exit();
		
	}


	public function postxmlAction() {

		$request = $this->getRequest();

		$id = $request->getParam("id");

		if(!is_numeric($id))
			throw new Exception("empty post id");


		$post_mapper = new Application_Model_PostMapper();
		$post_info = $post_mapper->getPostInfo($id);
		$post_image_path = get_post_path($id);

		$user_id = $post_info['user_id'];

		$profile_pic = get_profile_path($user_id);

		$tag_mapper = new Application_Model_TagMapper();
		$tags = $tag_mapper->findAllByTwoColumns("post_id", $id, "type", TYPE_TAG);

		$comment_mapper = new Application_Model_CommentMapper();

		$comments = $comment_mapper->getComments($id);
		$comments_count = $comment_mapper->count($id);

		$like_mapper = new Application_Model_LikeMapper();
		$like_numbers = $like_mapper->count($id);

		$share_mapper = new Application_Model_ShareMapper();
		$shares= $share_mapper->count($id);

		$xml_text = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_text .= '<post hot="no">';
		$xml_text .= '<author>' . $post_info['author'] . '</author>';
		$xml_text .= '<proofilepic>' . $profile_pic . '</proofilepic>';
		$xml_text .= '<date>' . gmdate("Y/m/d  H:i", $post_info['date']) . '</date>';
		$xml_text .= '<updated_at>' . gmdate("Y/m/d  H:i", $post_info['updated_at']) . '</updated_at>';
		$xml_text .= '<image>' . $post_image_path . '</image>';
		$xml_text .= '<text>' . $post_info['text'] . '</text>';
		$xml_text .= '<share>' . $shares . '</share>';
		$xml_text .= '<id>' . $id . '</id>';
		$xml_text .= "<like likeNumber=" . '"' . $like_numbers . '"' . ">" ;

		$xml_text .= "<likers>";
		$xml_text .= "</likers>";

		$xml_text .= "</like>";

		$xml_text .= "<tag>";
		if(!empty($tags)) {
			foreach($tags as $tag) {
				$xml_text .= "#" . $tag['content'];
			}
		}

		$xml_text .= "</tag>";

		// comments
		$comments_link = "http://plus.local/post/allcomment/id/" . $id;

		$xml_text .= '<comments commentNumber="' . $comments_count . '" allCommentsLink="' . $comments_link . '">';

		foreach($comments as $comment) {

			$xml_text .= "<comment>";
			$xml_text.= "<text>" . $comment['content'] . "</text>";
			$xml_text.= "<image>" . get_profile_path($comment['user_id']) . "</image>";
			$xml_text.= "<date>" . gmdate("Y/m/d  H:i", $comment['created_at']) . "</date>";
			$xml_text.= "<name>" . $comment['username'] . "</name>";
			$xml_text .= "</comment>";
		}

		$xml_text .= "</comments>";

		$xml_text .= '</post>';


		header("Content-type: text/xml");
		$xml = new SimpleXMLElement($xml_text);
		echo $xml->asXML();
		exit();
	}

	public function newAction() {

		$request = $this->getRequest();

		if($request->isPost()) {


			$content = $request->getParam("content");
			if(empty($content)) {
				$this->_redirect("/post/home");
			}
			$pieces = explode("#", $content);

			$post_content = $pieces[0];

			$tags = array();

			for($i=1 ; $i < sizeof($pieces); $i++)
				$tags[] = $pieces[$i];

			$user_id = get_user_id();


			$post_model = new Application_Model_Post();
			$post_model->_fields['updated_at'] = time();
			$post_model->_fields['content'] = $post_content;
			$post_model->_fields['user_id'] = $user_id;
			$post_model->_fields['is_reported'] = 0;

			$post_mapper = new Application_Model_PostMapper();
			$post_id = $post_mapper->save($post_model);


			$tag_mapper = new Application_Model_TagMapper();

			foreach($tags as $tag) {
				$comment_model = new Application_Model_Tag();
				$comment_model->_fields['content'] = $tag;
				$comment_model->_fields['post_id'] = $post_id;
				$comment_model->_fields['type'] = TYPE_TAG;

				$tag_mapper->save($comment_model);
			}

			if ( isset($_FILES['post_pic'])) {

				if ( is_uploaded_file($_FILES['post_pic']['tmp_name'])) {

					if (! move_uploaded_file($_FILES['post_pic']['tmp_name'], APPLICATION_PATH . "/../public/post_pic/" . $post_id . '.png'))
					{
						throw new Exception("error in moving file");
					}

				}
			}

			$this->_redirect("/post/home");
		}
	}


	public function likeAction() {

		$request = $this->getRequest();

		$post_id = $request->getParam('post_id');
		$user_id = get_user_id();

		$like_mapper = new Application_Model_LikeMapper();

		$count = $like_mapper->count($post_id);
		$likes = $like_mapper->like($user_id, $post_id);

		if($count < $likes) {
			$notification_mapper = new Application_Model_NotificationMapper();
			$notification_model = new Application_Model_Notification();

			$post_mapper = new Application_Model_PostMapper();
			$post = $post_mapper->find($post_id);

			$notification_model->_fields['user_id'] = $post['user_id'];
			$notification_model->_fields['is_seen'] = 0;
			$notification_model->_fields['content'] = get_username() . " Likes your post with id = $post_id";
			$notification_mapper->save($notification_model);
		}
		$xml_text = '<?xml version="1.0" encoding="UTF-8"?>';

		$xml_text .= '<likes>' . $likes . '</likes>';

		header("Content-type: text/xml");
		$xml = new SimpleXMLElement($xml_text);
		echo $xml->asXML();
		exit();
	}

	public function commentAction() {

		$request = $this->getRequest();

		$post_id = $request->getParam('post_id');
		$user_id = get_user_id();
		$content = $request->getParam('content');


		$comment_mapper = new Application_Model_CommentMapper();
		$comment_model = new Application_Model_Comment();

		$comment_model->_fields['post_id'] = $post_id;
		$comment_model->_fields['user_id'] = $user_id;
		$comment_model->_fields['content'] = $content;

		$comment_mapper->save($comment_model);

		$db = Zend_Registry::get('db');
		$sql = "update user_post set comment_number = comment_number+1 where id = " . $post_id . ";";
		$db->query($sql);

		$notification_mapper = new Application_Model_NotificationMapper();
		$notification_model = new Application_Model_Notification();

		$post_mapper = new Application_Model_PostMapper();
		$post = $post_mapper->find($post_id);

		$notification_model->_fields['user_id'] = $post['user_id'];
		$notification_model->_fields['is_seen'] = 0;
		$notification_model->_fields['content'] = get_username() . " Comment on your post with id = $post_id";
		$notification_mapper->save($notification_model);

		$this->_redirect('/post/home');
	}

	public function allcommentAction() {

		$request = $this->getRequest();
		$post_id = $request->getParam('id');

		$comment_mapper = new Application_Model_CommentMapper();
		$comments = $comment_mapper->getComments($post_id, false);

		$xml_text = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml_text .= '<comments>';

		foreach($comments as $comment) {

			$xml_text .= "<comment>";
			$xml_text.= "<text>" . $comment['content'] . "</text>";
			$xml_text.= "<image>" . get_profile_path($comment['user_id']) . "</image>";
			$xml_text.= "<date>" . gmdate("Y/m/d  H:i", $comment['created_at']) . "</date>";
			$xml_text.= "<name>" . $comment['username'] . "</name>";
			$xml_text .= "</comment>";
		}

		$xml_text .= '</comments>';

		header("Content-type: text/xml");
		$xml = new SimpleXMLElement($xml_text);
		echo $xml->asXML();
		exit();
	}

	public function reportAction() {

		$request = $this->getRequest();
		$post_id = $request->getParam('id');

		$db = Zend_Registry::get('db');
		$sql = "update user_post set is_reported = 1 where id = " . $post_id . ";";
		$db->query($sql);
		$this->_redirect("/post/home");
	}


	public function deleteAction() {

		$request = $this->getRequest();
		$post_id = $request->getParam('id');

		$db = Zend_Registry::get('db');
		$sql = "delete from user_post where id = " . $post_id . " and is_reported = 1;";
		$db->query($sql);
		$this->_redirect("/user/admin");
	}

	public function editAction() {


		$request = $this->getRequest();

		$post_id = $request->getParam("id");

		if(empty($post_id))
			throw new Exception("empty post_id");

		$post_mapper = new Application_Model_PostMapper();
		$post = $post_mapper->find($post_id);


		if(empty($post))
			throw new Exception("empty post");

		$this->view->post = $post;

		$user_id = get_user_id();


		if($post['user_id'] != $user_id)
			throw new Exception("Not Authorized");

		$tag_mapper = new Application_Model_TagMapper();
		$tags1 = $tag_mapper->findAllByColumn('post_id', $post_id);

		$tag_array = array();
		foreach($tags1 as $tag) {
			$tag_content = "#" . $tag['content'];
			$tag_array[] = $tag_content;
		}

		$this->view->tags = $tag_array;

		if($request->isPost()) {

			$content = $request->getParam("content");

			$pieces = explode("#", $content);

			$post_content = $pieces[0];

			$tags = array();

			for($i=1 ; $i < sizeof($pieces); $i++)
				$tags[] = $pieces[$i];

			$post_model = new Application_Model_Post();
			$post_model->_fields['id'] = $post_id;
			$post_model->_fields['updated_at'] = time();
			$post_model->_fields['created_at'] = $post['created_at'];
			$post_model->_fields['content'] = $post_content;
			$post_model->_fields['user_id'] = $user_id;
			$post_model->_fields['is_reported'] = 0;

			$post_mapper = new Application_Model_PostMapper();
			$post_id = $post_mapper->save($post_model);


			$tag_mapper = new Application_Model_TagMapper();

			foreach($tags1 as $tag1) {
				$tag_mapper->delete($tag1['id']);
			}

			foreach($tags as $tag) {
				$comment_model = new Application_Model_Tag();
				$comment_model->_fields['content'] = $tag;
				$comment_model->_fields['post_id'] = $post_id;
				$comment_model->_fields['type'] = TYPE_TAG;

				$tag_mapper->save($comment_model);
			}

			if ( isset($_FILES['post_pic'])) {

				if ( is_uploaded_file($_FILES['post_pic']['tmp_name'])) {

					if (! move_uploaded_file($_FILES['post_pic']['tmp_name'], APPLICATION_PATH . "/../public/post_pic/" . $post_id . '.png'))
					{
						throw new Exception("error in moving file");
					}

				}
			}

			$this->_redirect("/post/home");
		}

	}

	public function shareAction() {


		$request = $this->getRequest();

		$post_id = $request->getParam('post_id');
		$user_id = get_user_id();

		$share_mapper = new Application_Model_ShareMapper();


		$is_shared = $share_mapper->share($user_id, $post_id);

		if($is_shared) {

			$notification_mapper = new Application_Model_NotificationMapper();
			$notification_model = new Application_Model_Notification();

			$post_mapper = new Application_Model_PostMapper();
			$post = $post_mapper->find($post_id);

			$notification_model->_fields['user_id'] = $post['user_id'];
			$notification_model->_fields['is_seen'] = 0;
			$notification_model->_fields['content'] = get_username() . " Likes your post with id = $post_id";
			$notification_mapper->save($notification_model);
		}

		$this->_redirect('/post/home');
	}

	public function notificationAction() {

		$this->view->isAdmin = is_admin();

		$user_id = get_user_id();

		$notification_mapper = new Application_Model_NotificationMapper();
		$nots = $notification_mapper->findAllByColumn('user_id', $user_id);

		$this->view->nots = $nots;

		$notification_mapper->Seen();


	}

}


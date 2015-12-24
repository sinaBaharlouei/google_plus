<?php

class UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{

	}

	public function registerAction() {

		$request = $this->getRequest();

		if($request->isPost()) {

			$request = $this->getRequest();

			$username = $request->getParam('username');
			$password = $request->getParam('password');
			$about = $request->getParam("about");
			$place = $request->getParam("place");

			if(empty($username))
				throw new Exception("Empty user name");

			if(empty($password))
				throw new Exception("empty password");

			if(strlen($password) < 4)
				throw new Exception("Minimum password length is 4");

			$user_mapper = new Application_Model_UserMapper();
			$user_model = new Application_Model_User();

			$user_model->_fields['username'] = $username;
			$user_model->_fields['password'] = $password;
			$user_model->_fields['user_type'] = ROLE_USER;
			$user_model->_fields['place'] = $place;
			$user_model->_fields['about'] = $about;

			$user_mapper->save($user_model);
			$this->_helper->redirector('login', 'user');
		}
	}

	public function uniqueAction() {


		$request = $this->getRequest();

		$username = $request->getParam('username');

		$userMapper = new Application_Model_UserMapper();

		$result = $userMapper->findByColumn('username', $username);
		$is_valid = empty($result);

		$json_array = array(
			'status' => true,
			'data' => $is_valid
		);

		echo $this->view->json($json_array);
		exit();
	}

	public function searchAction() {

		$request = $this->getRequest();
		$name = $request->getParam("username");

		if(empty($name))
			$this->_redirect("post/home");

		$user_mapper = new Application_Model_UserMapper();
		$followed = $user_mapper->findByColumn("username", $name);

		if(empty($followed))
			$this->_redirect("post/home");

		$followed_id = $followed["id"];
		$follower_id = get_user_id();
		$follow_mapper = new Application_Model_FollowMapper();
		$follow_mapper->InsertFollower($follower_id, $followed_id);
		$this->_redirect("post/home");
	}

	public function loginAction() {

		$request = $this->getRequest();

		if(!empty($_COOKIE['remember']) && get_user_id()!= -1) {
			$this->_redirect('/post/home');
			exit();
		}
		if($request->isPost()) {

			$username = $request->getParam('username');
			$password = $request->getParam('password');
			$remember = $request->getParam("remember");

			if(empty($username)) throw new Exception("empty username");
			if(empty($password)) throw new Exception("empty password");

			$auth_adapter = $this->_getAuthAdapter($username,$password);
			$auth = Zend_Auth::getInstance();
			$result = $auth->authenticate($auth_adapter);
			if($result->isValid()) {
				$data = $auth_adapter->getResultRowObject();
				$auth->getStorage()->write($data);


				if(!empty($remember)) {
					// it's on
					setcookie("remember", true);
				}
					$this->_redirect('/post/home');
			}
			else $this->_redirect('/user/login');

		}
	}


	public function changeinfoAction() {

		$request = $this->getRequest();

		$category = $request->getParam("category");
		$user_id = get_user_id();

		$info_mapper = new Application_Model_InfoMapper();
		$items = $info_mapper->findAllByTwoColumns('user_id', $user_id, 'category', $category);

		if(empty($items))
			throw new Exception("empty_item");

		$this->view->items = $items;

	}

	public function editAction() {

		$request = $this->getRequest();

		$user_id = get_user_id();

		$user_mapper = new Application_Model_UserMapper();
		$user = $user_mapper->find($user_id);
		$this->view->user = $user;

		if($request->isPost()) {


			$username = $request->getParam("username");
			$password = $request->getParam("password");
			$about = $request->getParam("about");
			$place = $request->getParam('place');

			if(strlen($username) < 4) {
				$this->_redirect("/user/edit");
			}

			$user_model = new Application_Model_User();
			$user_model->_fields['id'] = $user_id;
			$user_model->_fields['username'] = $username;
			$user_model->_fields['about'] = $about;
			$user_model->_fields['place'] = $place;
			$user_model->_fields['password'] = $password;

			$user_mapper->save($user_model);

			if ( isset($_FILES['profile_pic'])) {

				if ( is_uploaded_file($_FILES['profile_pic']['tmp_name'])) {

					if (! move_uploaded_file($_FILES['profile_pic']['tmp_name'], APPLICATION_PATH . "/../public/profile_pic/" . $user_id . '.png')) {
						$this->_redirect("/user/edit");
					}
				}


			}

			if ( isset($_FILES['cover_pic'])) {

				if (! is_uploaded_file($_FILES['cover_pic']['tmp_name'])) {
					$this->_redirect("/profile/profile");
				}
				if (! move_uploaded_file($_FILES['cover_pic']['tmp_name'], APPLICATION_PATH . "/../public/cover_pic/" . $user_id . '.png'))
				{
					$this->_redirect("/user/edit");
				}

			}


			$this->_redirect("/profile/profile");

		}
	}

	public function logoutAction() {

		$auth=Zend_Auth::getInstance();
		$auth->clearIdentity();
		setcookie("remember", "", -1);
		$this->_redirect('/user/login');
	}

	protected function _getAuthAdapter($username,$password) {
		//accessing to database:
		$dbAdapter = Zend_Registry::get('db');
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		//set up specific information
		$authAdapter->setTableName('google_user')->setIdentityColumn('username')->setCredentialColumn('password');
		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);
		return $authAdapter;
	}

	public function adminAction() {

		$this->view->isAdmin = is_admin();
		$post_mapper = new Application_Model_PostMapper();
		$posts = $post_mapper->findAllByColumn('is_reported', 1);

		$this->view->posts = $posts;

	}
}


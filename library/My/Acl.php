<?php

class My_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if (PHP_SAPI == 'cli') return;

		$acl = new Zend_Acl();

		// add the roles
		$acl->addRole(new Zend_Acl_Role('guest'));
		$acl->addRole(new Zend_Acl_Role('user'), 'guest');
		$acl->addRole(new Zend_Acl_Role('admin'), 'user');

		// add the resources
		$acl->addResource(new Zend_Acl_Resource('index'));
		$acl->addResource(new Zend_Acl_Resource('error'));
		$acl->addResource(new Zend_Acl_Resource('user'));
		$acl->addResource(new Zend_Acl_Resource('profile'));
		$acl->addResource(new Zend_Acl_Resource('post'));
		$acl->addResource(new Zend_Acl_Resource('*'));

		// set up the access rules
		$acl->allow(null, array('index', 'error'));

		// a guest can only sign up content and login
		$acl->allow('guest', 'user', array('login', 'register', 'unique', 'search'));


		// user
		$acl->allow('user', 'user', array('edit', 'logout'));
		$acl->allow('user', 'profile', array('edit', 'profile', 'getxml', 'viewxml', 'more'));
		$acl->allow('user', 'post', array('new', 'postxml', 'getposts', 'like', 'share', 'comment', 'home', 'edit', 'allcomment', 'notification', 'report'));


		$acl->allow('admin', null);

		// Fetch the current user
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$id = get_user_id();
			$role = "user";
			if($id == 2)
				$role = "admin";
		} else {
			$role = 'guest';
		}
		// Authorization
		$controller = $request->controller;
		$action = $request->action;
		try {

			if (!$acl->isAllowed($role, $controller, $action)) {
				if ($role == 'guest') {
					$redirector = new Zend_Controller_Action_Helper_Redirector();
					$redirector->gotoSimple('login', 'user');

				} else {
					// User with role $role is not authorized for $controller/$action"
					$request->setControllerName('error');
					$request->setActionName('notauthorized');
				}
			}

		} catch (Exception $e) {
			$request->setControllerName('error');
			$request->setActionName('notfound');
		}
	}
}
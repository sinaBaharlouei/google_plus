<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$this->_helper->redirector('login', 'user');
    }

	public function homeAction() {

		$user_id = get_user_id();
		echo $user_id;
	}

	public function moreAction() {

	}





}


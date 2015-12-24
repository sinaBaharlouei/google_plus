<?php

require_once("ParentModel.php");

class Application_Model_User extends ParentModel
{

	public $_fields = array(
		'id' => null,
		'created_at' => null,
		'username' => null,
		'password' => null,
		'view' => null,
		'about' => null,
		'user_type' => null
	);

}

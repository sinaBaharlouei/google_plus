<?php

require_once("ParentModel.php");

class Application_Model_Like extends ParentModel
{

	public $_fields = array(
		'user_id' => null,
		'post_id' => null,
		'type' => null
	);

}

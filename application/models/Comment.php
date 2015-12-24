<?php

require_once("ParentModel.php");

class Application_Model_Comment extends ParentModel
{

	public $_fields = array(
		'id' => null,
		'user_id' => null,
		'post_id' => null,
		'content' => null,
		'created_at' => null,
	);

}

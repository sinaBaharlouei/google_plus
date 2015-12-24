<?php

require_once("ParentModel.php");

class Application_Model_Post extends ParentModel
{

	public $_fields = array(
		'id' => null,
		'created_at' => null,
		'content' => null,
		'comment_number' => null,
		'updated_at' => null,
		'user_id' => null,
		'is_reported' => null
	);

}

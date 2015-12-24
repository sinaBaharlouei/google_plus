<?php

require_once("ParentModel.php");

class Application_Model_Tag extends ParentModel
{

	public $_fields = array(
		'id' => null,
		'created_at' => null,
		'content' => null,
		'type' => null,
		'post_id' => null,
	);

}

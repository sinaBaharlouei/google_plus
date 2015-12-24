<?php

require_once("ParentModel.php");

class Application_Model_Info extends ParentModel
{

	public $_fields = array(
		'id' => null,
		'user_id' => null,
		'category' => null,
		'title' => null,
		'value' => null,
		'created_at' => null,
	);

}

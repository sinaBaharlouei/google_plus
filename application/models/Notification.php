<?php

require_once("ParentModel.php");

class Application_Model_Notification extends ParentModel
{

	public $_fields = array(
		'id' => null,
		'created_at' => null,
		'content' => null,
		'user_id' => null,
		'is_seen' => null
	);


}

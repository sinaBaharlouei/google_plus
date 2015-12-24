<?php

require_once("ParentModel.php");

class Application_Model_Follow extends ParentModel
{

	public $_fields = array(
		'follower_id' => null,
		'followed_at' => null,
	);

}

<?php

function show_error_message ($error) {

}

function get_user_id()
{
	$auth = Zend_Auth::getInstance();
	if ($auth->hasIdentity()) {
		return $auth->getIdentity()->id;
	}
	return -1;
}

function get_username()
{
	$auth = Zend_Auth::getInstance();
	if ($auth->hasIdentity()) {
		return $auth->getIdentity()->username;
	}
	return null;
}

function CreateXML($test_array) {

	$xml = new SimpleXMLElement('<root/>');
	array_walk_recursive($test_array, array ($xml, 'addChild'));
	print $xml->asXML();
}

function vdump()
{

	$args = func_get_args();

	$backtrace = debug_backtrace();
	$code = file($backtrace[0]['file']);

	echo "<pre style='background: #eee; border: 1px solid #aaa; clear: both; overflow: auto; padding: 10px; text-align: left; margin-bottom: 5px'>";

	echo "<b>".htmlspecialchars(trim($code[$backtrace[0]['line']-1]))."</b>\n";

	echo "\n";

	ob_start();

	foreach ($args as $arg)
		var_dump($arg);

	$str = ob_get_contents();

	ob_end_clean();

	$str = preg_replace('/=>(\s+)/', ' => ', $str);
	$str = preg_replace('/ => NULL/', ' &rarr; <b style="color: #000">NULL</b>', $str);
	$str = preg_replace('/}\n(\s+)\[/', "}\n\n".'$1[', $str);
	$str = preg_replace('/ (float|int)\((\-?[\d\.]+)\)/', " <span style='color: #888'>$1</span> <b style='color: brown'>$2</b>", $str);

	$str = preg_replace('/array\((\d+)\) {\s+}\n/', "<span style='color: #888'>array&bull;$1</span> <b style='color: brown'>[]</b>", $str);
	$str = preg_replace('/ string\((\d+)\) \"(.*)\"/', " <span style='color: #888'>str&bull;$1</span> <b style='color: brown'>'$2'</b>", $str);
	$str = preg_replace('/\[\"(.+)\"\] => /', "<span style='color: purple'>'$1'</span> &rarr; ", $str);
	$str = preg_replace('/object\((\S+)\)#(\d+) \((\d+)\) {/', "<span style='color: #888'>obj&bull;$2</span> <b style='color: #0C9136'>$1[$3]</b> {", $str);
	$str = str_replace("bool(false)", "<span style='color:#888'>bool&bull;</span><span style='color: red'>false</span>", $str);
	$str = str_replace("bool(true)", "<span style='color:#888'>bool&bull;</span><span style='color: green'>true</span>", $str);

	echo $str;

	echo "</pre>";

	echo "<div class='block tiny_text' style='margin-left: 10px'>";

	echo "Sizes: ";
	foreach ($args as $k => $arg) {

		if ($k > 0) echo ",";
		echo count($arg);

	}

	echo "</div>";
}

// function defination to convert array to xml
function array_to_xml($student_info, &$xml_student_info) {
	foreach($student_info as $key => $value) {
		if(is_array($value)) {
			if(!is_numeric($key)){
				$subnode = $xml_student_info->addChild("$key");
				array_to_xml($value, $subnode);
			}
			else{
				$subnode = $xml_student_info->addChild("item$key");
				array_to_xml($value, $subnode);
			}
		}
		else {
			$xml_student_info->addChild("$key",htmlspecialchars("$value"));
		}
	}
}

function get_profile_path($id) {
	$path = APPLICATION_PATH . "/../public/profile_pic/$id.png";

	if(file_exists($path))
		return "/profile_pic/$id.png";
	else return  "/profile_pic/default.jpg";
}


function get_cover_path($id) {
	$path = APPLICATION_PATH . "/../public/cover_pic/$id.png";

	if(file_exists($path))
		return "/cover_pic/$id.png";
	else return  "/cover_pic/default_cover.jpg";
}

function get_post_path($id) {
	$path = APPLICATION_PATH . "/../public/post_pic/$id.png";

	if(file_exists($path))
		return "/post_pic/$id.png";
	else return null;
}

function remove_null_values(Array & $arr, $remove_zero = false)
{
	$to_remove = array();

	foreach ($arr as $key => &$value) {
		if (is_array($value)) {
			remove_null_values($value);
			continue;
		}

		if ($remove_zero) {
			if (empty($value)) $to_remove[] = $key;
		} else {
			if (is_null($value)) $to_remove[] = $key;
		}
	}

	foreach ($to_remove as $key) {
		unset($arr[$key]);
	}
}


function is_admin()
{
	return (get_user_id() == 2);
}
<?php

class ProfileController extends Zend_Controller_Action
{

	public function init()
	{
	}


	public function profileAction() {

		$this->view->isAdmin = is_admin();
	}

	public function getxmlAction() {

		$note = '<?xml version="1.0" encoding="UTF-8"?>';
		$note.="<profile>";

		$user_id = get_user_id();

		$user_mapper = new Application_Model_UserMapper();
		$info_mapper = new Application_Model_InfoMapper();
		$follow_mapper = new Application_Model_FollowMapper();

		$user_mapper->increaseView();

		$user = $user_mapper->find($user_id);

		$user_info = $info_mapper->findAllByColumn('user_id', $user_id);
		$followed_numbers = $follow_mapper->getFollowersNumber($user_id);

		$profile = array();
		$profile['profile'] = array();

		$profile_array = array(
			"basicInfo",
			"story",
			"education",
			"contacts",
			"links",
			"work"
		);

		foreach($profile_array as $elem)
			$profile[$elem] = array();

		if(!empty($user_info))
		foreach($user_info as $info) {
			if($info['category'] == CATEGORY_BASIC)
				$profile['basicInfo'][] = array("title" => $info['title'], "value" => $info['value']);

			elseif($info['category'] == CATEGORY_CONTACTS)
				$profile['contacts'][] = array("title" => $info['title'], "value" => $info['value']);

			elseif($info['category'] == CATEGORY_EDUCATION)
				$profile['education'][] = array("title" => $info['title'], "value" => $info['value']);

			elseif($info['category'] == CATEGORY_LINKS)
				$profile['links'][] = array("title" => $info['title'], "value" => $info['value']);

			elseif($info['category'] == CATEGORY_STORY)
				$profile['story'][] = array("title" => $info['title'], "value" => $info['value']);

			elseif($info['category'] == CATEGORY_WORK)
				$profile['work'][] = array("title" => $info['title'], "value" => $info['value']);
		}

		$note .= "<name>" . $user['username'] . "</name>";
		$note .= "<about>" . $user['about'] . "</about>";
		$note .= "<view>" . $user['view'] . "</view>";

		$profile_path = get_profile_path($user_id);
		$note .= "<profilePic>" . $profile_path . "</profilePic>";

		$cover_path = get_cover_path($user_id);
		$note .= "<cover>" . $cover_path . "</cover>";

		$note .= "<people>";

		$note .= "<followers>" . $followed_numbers . "</followers>";

		$note .= "<followersPics>" . "</followersPics>";
		$note .= "</people>";

		$note .= "<places> <place>" . $user['place'] . "</place> </places>";
		foreach($profile_array as $category) {

			$note.= "<" . $category . ">";

			foreach($profile[$category] as $bInfo)
				$note .= "<" . $bInfo["title"] . ">" . $bInfo['value'] . "</" . $bInfo["title"] . ">";

			$note.= "</" . $category . ">";
		}

		$note .= "</profile>";


		header("Content-type: text/xml");
		$xml = new SimpleXMLElement($note);
		echo $xml->asXML();
		exit();
	}

	public function editAction() {

		$request = $this->getRequest();
		$category = $request->getParam("category");

		$info_mapper = new Application_Model_InfoMapper();
		$items = $info_mapper->findAllByTwoColumns("category", $category, 'user_id', get_user_id());

		$this->view->items = $items;
		$this->view->category = $category;

		if($request->isPost()) {

			$add_key = $request->getParam("key_field");
			$add_value = $request->getParam("value_field");

			if(!empty($add_key) && !empty($add_value)) {

				$info_model = new Application_Model_Info();
				$info_model->_fields['user_id'] = get_user_id();
				$info_model->_fields['title'] = $add_key;
				$info_model->_fields['value'] = $add_value;
				$info_model->_fields['category'] = $category;

				$info_mapper->save($info_model);

			}
			$edit_value = $request->getParam("edit_value");
			$edit_key = $request->getParam("edit_key");
			if(!empty($edit_value) ) {
				if($edit_value != "Value")
					$info_mapper->update($edit_key, $edit_value, $category);
			}

			$this->_redirect("/profile/profile");
		}
	}


	public function viewxmlAction() {

		$request = $this->getRequest();

		$user_id = get_user_id();

		$follower_mapper = new Application_Model_FollowMapper();

		$friends = $follower_mapper->findAllByColumn('follower_id', $user_id);

		$user_mapper = new Application_Model_UserMapper();

		$persons = array();

		foreach($friends as $friend) {


			$friends_of_friend = $follower_mapper->findAllByColumn('followed_id', $friend['followed_id']);


			foreach($friends_of_friend as $ff) {
				$person_id = $ff['follower_id'];

				$image = get_profile_path($person_id);

				$person = $user_mapper->find($person_id);

				$name = $person['username'];
				$about = $person['about'];

				$matual = $user_mapper->find($friend['followed_id']);
				$matual_name = $matual['username'];

				if($name != get_username()) {

					$f = $follower_mapper->findAllByTwoColumns('followed_id', $ff['follower_id'], 'follower_id', get_user_id());
					if(empty($f)) {

						$persons[] = array(
							'image' => $image,
							'name' => $name,
							'about' => $about,
							'matual_friend' => $matual_name
						);

					}
				}

			}
		}


		$note = '<?xml version="1.0" encoding="UTF-8"?>';
		$note.="<people>";

		foreach($persons as $item) {

			$note.="<person>";

			$note.="<image>";
			$note.= $item['image'];
			$note.="</image>";

			$note.="<name>";
			$note.= $item['name'];
			$note.="</name>";

			$note.="<about>";
			$note.= $item['about'];
			$note.="</about>";

			$note.="<matualFriend>";
			$note.= $item['matual_friend'];
			$note.="</matualFriend>";


			$note.="</person>";

		}


		$note .= "</people>";

		header("Content-type: text/xml");
		$xml = new SimpleXMLElement($note);
		echo $xml->asXML();
		exit();
	}


	public function moreAction() {

	}


}


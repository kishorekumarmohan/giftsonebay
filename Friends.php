<?php
class Friends {

	const FB_URL =  'https://graph.facebook.com/';

	function getFriends($id, $fields, $access_token) {
		$myFriendsUrl = self::FB_URL . $id."/friends?" . $access_token . $fields;
		$myFriendsResponse = json_decode(file_get_contents($myFriendsUrl), true);
		if($myFriendsResponse == null) {
			throw new Exception("Access Token Invalid");
		}
		return $myFriendsResponse["data"];
	}

	function getUser($id, $fields, $access_token) {
		if($fields) {
			$graph_url = "https://graph.facebook.com/".$id."?" . $access_token . $fields;
		} else {
			$graph_url = "https://graph.facebook.com/".$id."?" . $access_token;
		}
		$user = json_decode(file_get_contents($graph_url), true);
		if($user == null) {
			throw new Exception("Access Token Invalid");
		}
		return $user;
	}

	function getFriendsByBirdayInXDays($myFriendsResponse, $no_of_days, $no_of_friends_to_fetch) {
		$friends = new ArrayObject();
		$count =0;
		$todays_date_plus_15 = mktime(0, 0, 0, date("m")  , date("d")+$no_of_days, date("Y"));

		foreach (array_keys($myFriendsResponse) as $value) {
			$id = $myFriendsResponse[$value][Constants::id];
			$birthday = $myFriendsResponse[$value][Constants::birthday];
			if($birthday == null){
				continue;
			}
			$birthdayArr = date_parse($birthday);
			$birthday_new = mktime(0, 0, 0, $birthdayArr["month"]  , $birthdayArr["day"], date('Y'));

			if($birthday_new >= strtotime(date("m/d/Y")) && $birthday_new <= $todays_date_plus_15){
				$img = "https://graph.facebook.com/". $id ."/picture?type=small";
				$display_bday = $this->getBithdayToDisplay($birthdayArr);
				$user = new User($id, $myFriendsResponse[$value][Constants::first_name], $display_bday);
				$friends->append($user);
				$count++;
			}
			if($count >=$no_of_friends_to_fetch) {
				break;
			}
		}
		return $friends;
	}

	function getBithdayToDisplay($birthday){
		if(!is_array($birthday)) {
			$birthday = date_parse($birthday);
		}
		$display_bday = mktime(0, 0, 0, $birthday["month"], $birthday["day"], $birthday["year"]);
		return  date("M j", $display_bday);
	}

	function getUserLikes($id, $access_token, $fields){
		$myLikesUrl = self::FB_URL . $id ."/likes?" . $access_token . $fields;
		$response = json_decode(file_get_contents($myLikesUrl), true);
		if($response == null) {
			throw new Exception("Access Token Invalid");
		}
		return $response["data"];
	}

	function parseLikesResponse($likesResponse) {
		$likes = new ArrayObject();
		if($likesResponse == null) {
			return $likes;
		}

		foreach (array_keys($likesResponse) as $i) {
			$like = null;
			$category = $likesResponse[$i][Constants::category];
			//echo  $category;
			$name = $likesResponse[$i][Constants::name];
			if ($category == LikeCategory::Book) {
				$like = new Like(LikeCategory::Book, LikeCategory::Book, $name, 267, "");
			} else if ($category == LikeCategory::Movie) {
				$like = new Like(LikeCategory::Movie, LikeCategory::Movie, $name, 11232, "");
			} else if ($category == LikeCategory::Music || $category == LikeCategory::Album || $category == LikeCategory::Musician || $category == LikeCategory::Song) {
				$like = new Like(LikeCategory::Music, LikeCategory::Music, $name, 11233, "");
			} else if ($category == LikeCategory::Tv_show) {
				$like = new Like(LikeCategory::Tv_show, LikeCategory::Tv_show, $name, 617, "");
			}

			/*else if ($category == LikeCategory::Sport) {
			 if ($name == LikeCategory::Cricket) {
			 $like = new Like(LikeCategory::Sport, LikeCategory::Cricket, $name, 2906, "bat");
			 } else if ($name == LikeCategory::Tennis) {
			 $like = new Like(LikeCategory::Sport, LikeCategory::Tennis, $name, 159134, "rackets");
			 } else if ($name == LikeCategory::Football) {
			 $like = new Like(LikeCategory::Sport, LikeCategory::Football, $name, 21214, "");
			 } else if ($name == LikeCategory::Soccer) {
			 $like = new Like(LikeCategory::Sport, LikeCategory::Soccer, $name, 20862, "");
			 }else if ($name == LikeCategory::Volleyball) {
			 $like = new Like(LikeCategory::Sport, LikeCategory::Volleyball, $name,  159129, "");
			 }
			 }*/
			if($like != null) {
				$likes->append($like);
			}
		}
		return $likes;
	}


	function getLikesByCategory($likes, $cat) {
		$values;
		$i =0;
		$iterator = $likes->getIterator();
		while($iterator->valid()) {
			$like = $iterator->current();
			if($cat == $like->getCategory() || $cat == $like->getParentCategory()){
				$values[$i++] = $like->getName();
			}
			$iterator->next();
		}
		return $values;
	}

	function getLikesTextToDisplay($values) {
		if($values) {
			foreach ($values as $s) {
				$value =  $s .", ". $value;
			}
			$value = substr($value, 0, -2);
			return $value;
		}
	}
}
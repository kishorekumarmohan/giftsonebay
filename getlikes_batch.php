<?php
require 'userlikesdao.php';
require 'usertokendao.php';
require 'userrelationdao.php';
require 'Friends.php';
require 'Constants.php';

class getlikes_batch {

	private static $user_token_dao;
	private static $user_relation_dao;
	private static $user_likes_dao;

	function get_likes($fb_user_id, $access_token) {
		$friends = new Friends();
		$fields = "&fields=name%2Ccategory";
		try {
			$response = $friends->getUserLikes($fb_user_id, $access_token, $fields);
		} catch (Exception $e) {
		}

		foreach (array_keys($response) as $value) {
			$fb_page_category = $response[$value][Constants::category];
			$fb_page_name = $response[$value][Constants::name];
			self::$user_likes_dao->insert($fb_user_id, $fb_page_category, $fb_page_name);
		}
	}

	function init(){
		self::$user_token_dao = new usertokendao();
		self::$user_likes_dao = new userlikesdao();
		self::$user_relation_dao = new userrelationdao();

		// get likes for the logged in user
		$result_arr = self::$user_token_dao->fetch(2);
		$access_token;
		foreach (array_keys($result_arr) as $fb_user_id) {
			$access_token = $result_arr[$fb_user_id];
			$this->get_likes($fb_user_id, $access_token);
			// get likes for the logged in user's friends
			$friends = self::$user_relation_dao->fetch($fb_user_id);
			foreach ($friends as $fb_friend_user_id) {
				$this->get_likes($fb_friend_user_id, $access_token);
				$process_complete = true;
			}
			if($process_complete) {
				self::$user_token_dao ->expireToken($fb_user_id);
			}
		}
	}

	function __destruct() {
		self::$user_token_dao -> close();
		self::$user_likes_dao -> close();
		self::$user_relation_dao->close();
	}
}
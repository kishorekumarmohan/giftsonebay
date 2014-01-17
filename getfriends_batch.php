<?php
require 'Friends.php';
require 'usertokendao.php';
require 'userrelationdao.php';
require 'Constants.php';
class getfriends_batch {

	private static $user_token_dao;
	private static $user_relation_dao;

	function get_friends($fb_user_id, $access_token) {
		$friends = new Friends();
		$fields = "&fields=id";
		try {
			$response = $friends->getFriends($fb_user_id, $fields, $access_token);
		} catch (Exception $e) {
			// deleting because the $access_token is already expired
			self::$user_token_dao->expireToken($fb_user_id);
		}

		foreach (array_keys($response) as $value) {
			$fb_friend_user_id = $response[$value][Constants::id];
			self::$user_relation_dao->insert($fb_user_id, $fb_friend_user_id);
		}
	}

	function init(){
		self::$user_token_dao = new usertokendao();
		self::$user_relation_dao = new userrelationdao();

		$result_arr = self::$user_token_dao->fetch(10);
		foreach (array_keys($result_arr) as $fb_user_id) {
			$this->get_friends($fb_user_id, $result_arr[$fb_user_id]);
		}
	}

	function __destruct() {
		self::$user_token_dao -> close();
		self::$user_relation_dao -> close();
	}
}
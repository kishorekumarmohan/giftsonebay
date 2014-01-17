<?php
class userrelationdao {
	private $db_connection;

	function __construct() {
		$this->db_connection = db::getConnection();
	}

	//$fb_user_id is unique in DB so if we get UniqueConstraintException we are ok
	function insert($fb_user_id, $fb_friend_user_id) {
		try {
			$query = "INSERT INTO user_relation (fb_user_id, fb_friend_user_id, created_date) VALUES (?, ?, NOW())";
			$statement = $this->db_connection->prepare($query);
			$statement->bind_param('ss', $fb_user_id, $fb_friend_user_id);
			$statement->execute();
			if($statement->affected_rows == 0) {
				return false;
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}


	function fetch($fb_user_id) {
		$query = "SELECT fb_friend_user_id FROM user_relation WHERE fb_user_id = ". $fb_user_id;
		$result = $this->db_connection->query($query);
		$result_arr = array();
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			$result_arr[$i++] = $row["fb_friend_user_id"];
		}
		$result->free();
		return $result_arr;
	}

	function close() {
		db::close();
	}
}
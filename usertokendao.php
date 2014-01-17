<?php
require 'db.php';
class usertokendao {
	private $db_connection;

	function __construct() {
		$this->db_connection = db::getConnection();
	}

	function upsert($fb_user_id, $access_token) {
		try {
			if(! $this->update($fb_user_id, $access_token)) {
				$this->insert($fb_user_id, $access_token);
			}
		} catch (Exception $e) {
			//error_log("Exception in usertokendao.upsert". $e->getMessage(), 0);
		}
	}

	function insert($fb_user_id, $access_token) {
		try {
			$query = "INSERT INTO user_token (fb_user_id, fb_access_token, created_date) VALUES (?, ?, NOW())";
			$statement = $this->db_connection->prepare($query);
			$statement->bind_param('ss', $fb_user_id, $access_token);
			$statement->execute();
			if($statement->affected_rows == 0) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}

	function update($fb_user_id, $access_token) {
		try {
			$query = "UPDATE user_token set fb_access_token = ?, expired = 0 WHERE fb_user_id = ?";
			$statement = $this->db_connection->prepare($query);
			$statement->bind_param('ss', $access_token, $fb_user_id);
			$statement->execute();
			if($statement->affected_rows == 0) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}

	function expireToken($fb_user_id) {
		try {
			$query = "UPDATE user_token set expired = 1 WHERE fb_user_id = ?";
			$statement = $this->db_connection->prepare($query);
			$statement->bind_param('s', $fb_user_id);
			$statement->execute();
			if($statement->affected_rows == 0) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}

	function delete($fb_user_id) {
		$query = "DELETE FROM user_token WHERE fb_user_id = ?";
		$statement = $this->db_connection->prepare($query);
		$statement->bind_param('s', $fb_user_id);
		$statement->execute();
		$statement->store_result();
		if($statement->num_rows != 1) {
			return false;
		}
	}

	function fetch($no_of_rows) {
		$query = "SELECT fb_user_id, fb_access_token FROM user_token where expired != 1 LIMIT " . $no_of_rows;
		$result = $this->db_connection->query($query);
		$result_arr = array();

		while ($row = $result->fetch_assoc()) {
			$result_arr[$row["fb_user_id"]] = $row["fb_access_token"];
		}
		$result->free();
		return $result_arr;
	}

	function close() {
		db::close();
	}
}
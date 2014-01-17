<?php

class userlikesdao {
	private $db_connection;

	function __construct() {
		$this->db_connection = db::getConnection();
	}

	function upsert($fb_user_id, $fb_page_category, $fb_page_name) {
		if(! $this->update($fb_user_id, $fb_page_category, $fb_page_name)) {
			$this->insert($fb_user_id, $fb_page_category, $fb_page_name);
		}
	}

	function insert($fb_user_id, $fb_page_category, $fb_page_name) {
		try {
			$query = "INSERT INTO user_likes (fb_user_id, category, name, created_date) VALUES (?, ?, ?, NOW())";
			$statement = $this->db_connection->prepare($query);
			$statement->bind_param('sss', $fb_user_id, $fb_page_category, $fb_page_name);
			$statement->execute();
			if($statement->affected_rows == 0) {
				return false;
			}
			return true;
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
	}

	function close() {
		db::close();
	}
}
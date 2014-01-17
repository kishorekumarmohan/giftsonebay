<?php
require 'db.php';
class userpopularlikesdao {
	private $db_connection;

	function __construct() {
		$this->db_connection = db::getConnection();
	}

	function fetch_by_category($fb_user_id, $count) {
		$query = "SELECT category, name FROM user_popular_likes WHERE fb_user_id = ". $fb_user_id .
		" and count >=". $count ." and category in ('Movie', 'Book', 'Music', 'Tv show') order by count desc";
		$result = $this->db_connection->query($query);
		$result_arr = array();
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			$result_arr[$row["name"]] = $row["category"];
		}
		$result->free();
		return $result_arr;
	}

	function fetch_all($fb_user_id, $count) {
		$query = "SELECT category, name FROM user_popular_likes WHERE fb_user_id = ". $fb_user_id .
		" and count >=". $count ." order by count desc";
		$result = $this->db_connection->query($query);
		$result_arr = array();
		$i = 0;
		while ($row = $result->fetch_assoc()) {
			$result_arr[$row["name"]] = $row["category"];
		}
		$result->free();
		return $result_arr;
	}

	function close() {
		db::close();
	}
}
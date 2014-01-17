<?php
class db {
	private static $conn;

	static function getConnection() {
		$host = $_SERVER['HTTP_HOST'];
		try {
			if(!isset($conn)) {
				if($host == "localhost") {
					self::$conn = new mysqli('localhost', 'root', '', 'aafe8qb7_gift');
				} else {
					self::$conn = new mysqli('localhost', 'aafe8qb7', 'kilvvaadseab', 'aafe8qb7_gift');
				}
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
		}
		return self::$conn;
	}

	static function close() {
		try {
			mysqli_close(self::$conn);
		}catch (Exception $e) {
			error_log($e->getMessage());
		}
	}
}
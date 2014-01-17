<?php
error_reporting(E_ALL & ~E_ALL);
session_start();
require 'getfriends_batch.php';

try {
	$friends_batch = new getfriends_batch();
	$friends_batch -> init();
	echo "Success";

} catch (Exception $e) {
	echo "failure";
}
?>
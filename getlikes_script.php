<?php
error_reporting(E_ALL & ~E_ALL);
session_start();
require 'getlikes_batch.php';

try {
	$likes_batch = new getlikes_batch();
	$likes_batch -> init();
	echo "Success";

} catch (Exception $e) {
	echo "failure";
}
?>
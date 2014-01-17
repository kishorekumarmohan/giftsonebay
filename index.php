<?php
session_start();
$host = $_SERVER['HTTP_HOST'];
if($host == "localhost") {
	header( 'Location: http://localhost/giftsonebay/login.php' );
} else {
	//header( 'Location: http://www.giftyourfriends.com/login.php' );
	header( 'Location: http://aafe8qb7.facebook.joyent.us/giftsonebay/login.php' );	
}
?>
<html>
<title>Gifts on eBay - see personalized gift recommendations for you!!!</title>
<body>
<?php include_once("analyticstracking.php") ?>
</body>
</html>

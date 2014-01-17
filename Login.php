<?php
session_start();
require 'Constants.php';
require 'Friends.php';
require 'usertokendao.php';
require 'config.php';
error_reporting(E_ALL & ~E_NOTICE);

$debug = $_GET[Constants::debug];
if($debug) {
	echo var_dump($_SERVER);
}

$host = $_SERVER['HTTP_HOST'];
if($host == "localhost") {
	$app_id = config::FB_DEV_APP_ID; // test
	$app_secret = config::FB_DEV_APP_SECRET; // test
	$my_url = config::FB_DEV_APP_URL;
} else {
	$app_id = config::FB_PROD_APP_ID;
	$app_secret = config::FB_PROD_APP_SECRET;
	$my_url = config::FB_PROD_APP_URL;
}

$access_token = $_SESSION[Constants::access_token];
if($access_token) {
	if(process($access_token)) {
		return;
	}
}
////

$code = $_REQUEST["code"];
//$referer = $_SERVER['HTTP_REFERER'];
if(empty($code)) {
	$dialog_url = "http://www.facebook.com/dialog/oauth?client_id="
	. $app_id . "&redirect_uri=" . urlencode($my_url) . "&scope=user_activities%2Cuser_birthday%2Cuser_interests%2Cuser_likes%2Cuser_location%2Cfriends_activities%2Cfriends_birthday%2Cfriends_interests%2Cfriends_likes%2Cfriends_location";
	echo("<script> top.location.href='" . $dialog_url . "'</script>");
}

$token_url = "https://graph.facebook.com/oauth/access_token?client_id="
. $app_id . "&redirect_uri=" . urlencode($my_url) . "&client_secret="
. $app_secret . "&code=" . $code;

$access_token = file_get_contents($token_url);
if($access_token) {
	$_SESSION[Constants::access_token] = $access_token;
	process($access_token);
}
?>
<html>
<body>
<?php include_once("analyticstracking.php") ?>
</body>
</html>

<?php
function process($access_token) {
	try {
		$friends = new Friends();
		$logged_in_user = $friends->getUser("me", "&fields=id", $access_token);
		$host = $_SERVER['HTTP_HOST'];
		if($host == "localhost") {
			header('Location: http://localhost/giftsonebay/home.php');
		} else {
			//header('Location: http://www.giftyourfriends.com/home.php');
			header('Location: http://aafe8qb7.facebook.joyent.us/giftsonebay/home.php');
		}
		// insert record into user_token db
		$user_token_dao = new usertokendao();
		$user_token_dao->insert($logged_in_user[Constants::id], $access_token);
		$user_token_dao->close();
	} catch (Exception $e) {
		session_destroy();
		return false;
	}
	return true;
}
?>

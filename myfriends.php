<?php
session_start();
require 'Friends.php';
require 'Constants.php';
header("Cache-Control: maxage=86400");
error_reporting(E_ALL);

$access_token = $_SESSION[Constants::access_token];
$fields = "&fields=first_name%2Cbirthday";
try{
	$friends = new Friends();
	$myFriendsResponse = $friends->getFriends("me", $fields, $access_token);
	$logged_in_user = $friends ->getUser("me", "&fields=name", $access_token);
} catch(Exception $e) {
	$e->getMessage();
	if($host == "localhost") {
		header( 'Location: http://localhost/giftsonebay/login.php' ) ;
	} else {
		header('Location: http://aafe8qb7.facebook.joyent.us/giftsonebay/login.php');
		//header( 'Location: http://www.giftyourfriends.com/login.php' ) ;
	}
}
?>
<!doctype html>
<html>
<link href="css/style.css" rel="stylesheet" type="text/css">
<head>
<title>My Friends on Facebook</title>
</head>
<body>
<?php include_once("analyticstracking.php") ?>
<?php include("menu.php"); ?>
	<div align="center">
		<table>
			<tr>
				<td valign="top" colspan="9">
					<div class="frndsPageSubHdrBorder">
						<span class="frndsPageSubHdr">Click on friends below to find out
							gift recommendations for them</span>
					</div>
				</td>
			</tr>
			<tr>
			<?php
			$count = 0;
			foreach (array_keys($myFriendsResponse) as $value) {
				$id = $myFriendsResponse[$value][Constants::id];
				$first_name = $myFriendsResponse[$value][Constants::first_name];
				$count++;
				?>
				<td>
					<div class="frndsPageName">
						<a href="home.php?id=<?php echo $id;?>"><img border="0"
							src="https://graph.facebook.com/<?php echo $id;?>/picture" />
							<div>
							<?php echo $first_name;?>
							</div> </a>
					</div>
				</td>
				<?php
				if($count >=9) {
					$count =0;
					?>
			</tr>
			<tr>
			<?php }
			}
			?>
		
		</table>
	</div>
</body>
</html>

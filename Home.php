<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require 'Friends.php';
require 'Item.php';
require 'User.php';
require 'EbayApiHelper.php';
require 'Constants.php';
header("Cache-Control: maxage=86400");

// get $access_token from session, if not present redirect to login page
$access_token = $_SESSION[Constants::access_token];
$host = $_SERVER['HTTP_HOST'];
if($access_token == null) {
	if($host == "localhost") {
		header( 'Location: http://localhost/giftsonebay/login.php') ;
	} else {
		//header( 'Location: http://www.giftyourfriends.com/login.php') ;
		header('Location: http://aafe8qb7.facebook.joyent.us/giftsonebay/login.php');
	}
}

$referrer = $_SERVER['HTTP_REFERER'];
$pos = strpos($referrer, "www.facebook.com");
//echo $referrer;
if($pos >0) {
	if($host == "localhost") {
		header( 'Location: http://apps.facebook.com/kishore' ) ;
	} else {
		header( 'Location: http://apps.facebook.com/giftsonebay' ) ;
	}
	return ;
}
$pos = 1;
$is_facebook;
$picture = "large";
$item_td_width = 140;
$item_td_height = 140;
$no_of_days = 30;
$profile_fb_fields_width =200;
$no_of_friends_to_fetch = 10;
$width = 900;
if($pos >0) {
	$is_facebook = true;
	$width = 730;
	$picture = "small";
	$item_td_width = 100;
	$item_td_height =80;
	$profile_fb_fields_width =100;
	$no_of_days = 30;
	$no_of_friends_to_fetch = 8;
}

$debug = $_GET[Constants::debug];
if($debug) {
	echo var_dump($_SERVER);
}

// check if access_token is valid if not redirect to login page
$friends = new Friends();
try{
	$is_friend_profile_page = false;
	$uid = $_GET[Constants::id];
	// if uid in url is null, show logged in user page
	if($uid == null || $uid == "me") {
		$uid = "me";
		$fields = "&fields=first_name%2Cbirthday";
		$myFriendsResponse = $friends-> getFriends("me", $fields, $access_token);
		$friendsList = $friends->getFriendsByBirdayInXDays($myFriendsResponse, $no_of_days, $no_of_friends_to_fetch);
	} else {
		$is_friend_profile_page = true;
	}

	$logged_in_user = $friends->getUser($uid, null, $access_token);
	$fields = "&fields=name%2Ccategory";
	$likesResponse = $friends-> getUserLikes($uid, $access_token, $fields);
	$likes = $friends -> parseLikesResponse($likesResponse);
	$user_has_likes = true;

	// if logged in user seeing his own profile and if he dont has any LIKES then we pull likes from one of his friends
	// and show it as popular from friends
	if(sizeof($likes) == 0 && $is_friend_profile_page== false) {
		$user_has_likes = false;
		$iterator = $friendsList->getIterator();
		while($iterator->valid()) {
			$user = $iterator->current();
			$id = $user->getId();
			$likesResponse = $friends-> getUserLikes($uid, $access_token, $fields);
			$friendlikes = $friends -> parseLikesResponse($likesResponse);
			if(sizeof($friendlikes) > 0) {
				$likes = $friendlikes;
				break;
			}
			$iterator->next();
		}
	}
} catch(Exception $e) {
	session_destroy();
	error_log($e->getMessage());
	if($host == "localhost") {
		header( 'Location: http://localhost/giftsonebay/login.php' ) ;
	} else {
		//header( 'Location: http://www.giftyourfriends.com/login.php' ) ;
		header('Location: http://aafe8qb7.facebook.joyent.us/giftsonebay/login.php');
	}
	return;
}
$likescount = sizeof($likes);
if($likescount == 0) {
	$user_has_likes = false;
	$gender = $logged_in_user[Constants::gender];
	if($gender == "male") {
		$likes->append(new Like(LikeCategory::Book, LikeCategory::Book, "Harry Potter", "267", ""));
	} else {
		$likes->append(new Like(LikeCategory::Book, LikeCategory::Book, "Harry Potter", "267", ""));
	}
}

$ebayApiHelper = new EbayApiHelper();
$pgcnt = $_GET[Constants::pgcnt];
if($likescount > $pgcnt) {
	$pgcnt = $pgcnt + 4;
}
if($likescount > $pgcnt) {
	$show_more_link = true;
}
$eBayUrls = $ebayApiHelper->buildEbayApiUrls($likes, $pgcnt);
?>

<!doctype html>
<html>
<link href="css/style.css" rel="stylesheet" type="text/css">
<head>
<title>Gift Recommendations / ideas on eBay</title>
</head>
<body>
<?php include_once("analyticstracking.php") ?>
<?php if ($logged_in_user == null) {
	return;
}?>
<?php include("menu.php"); ?>
	<!--  User logged in flow -->
	<table border="0" width="<?php echo $width;?>">
		<tr>
			<td class="tcol" valign="top"
				width="<?php echo $profile_fb_fields_width;?>px"><p /> <img
				src="https://graph.facebook.com/
				<?php echo $logged_in_user[Constants::id];?>/picture?type=<?php echo $picture?>" />

				<div style="float: left;">
				<?php
				$value = $logged_in_user[Constants::name];
				if($value) {
					echo getFbHdr("Name:") . "<b>" .getFbValue($value) . "</b>";
				}

				$value = $logged_in_user[Constants::location][Constants::name];
				if($value) {
					echo getFbHdr("City:") . getFbValue($value);
				}

				$value = $logged_in_user[Constants::relationship_status];
				if($value) {
					echo getFbHdr("Relationship Status:") . getFbValue($value);
				}

				$value = $logged_in_user[Constants::birthday];
				if($value) {
					echo getFbHdr("Birthday:") . getFbValue($friends->getBithdayToDisplay($value));
				}

				if($user_has_likes) {
					$value = $friends -> getLikesByCategory($likes, LikeCategory::Book);
					if($value) {
						echo getFbHdr("Favourite Books:") . getFbValue($friends ->getLikesTextToDisplay($value));
					}
					$value = null;

					$value = $friends -> getLikesByCategory($likes, LikeCategory::Movie);
					if($value) {
						echo getFbHdr("Favourite Movie:") . getFbValue($friends ->getLikesTextToDisplay($value));
					}
					$value = null;

					$value = $friends -> getLikesByCategory($likes, LikeCategory::Music);
					if($value) {
						echo getFbHdr("Favourite Music:") . getFbValue($friends ->getLikesTextToDisplay($value));
					}
					$value = null;

					$value = $friends -> getLikesByCategory($likes, LikeCategory::Tv_show);
					if($value) {
						echo getFbHdr("Favourite TV Shows:") . getFbValue($friends ->getLikesTextToDisplay($value));
					}
					$value = null;

					$value = $friends -> getLikesByCategory($likes, LikeCategory::Sport);
					if($value) {
						echo getFbHdr("Favourite Sports:") . getFbValue($friends ->getLikesTextToDisplay($value));
					}
				} else {
					if($is_friend_profile_page) {
						echo getFbHdr($logged_in_user[Constants::name] ." don't seem to have any favorite books, music, or movies on Facebook.");
					} else {
						echo getFbHdr("You don't seem to have any favorite books, music, or movies on Facebook. Add your favorites to get personalized recommendations on this page.");
					}
					echo ("<br/>");
					echo getFbValue("These recommendations are based on popular among your friend(s) favorite books, music, or movies");
				}
				?>
				</div>
			</td>
			<td valign="top"><?php
			if(sizeof($friendsList) >0) {
				?>
				<table border="0">
					<tr class="trow">
						<td class="tcol" valign="top" colspan="10">
							<div class="fbFrndsHdr">Gift ideas for your friends upcoming
								birthday</div>
							<div class="fbFrndsSubHdr">(Dont know what to gift? Clik on their
								names to find their likes and intrests)</div>
						</td>
					</tr>
					<tr>
					<?php

					$iterator = $friendsList->getIterator();
					while($iterator->valid()) {
						$user = $iterator->current();
						?>
						<td class="tcol">
							<div class="fbFrndsName">
								<a href="?id=<?php echo $user->getId();?>"><img border="0"
									src="https://graph.facebook.com/<?php echo $user->getId();?>/picture" />
								</a> <br /> <a href="?id=<?php echo $user->getId();?>"><?php echo $user->getName();?>
								</a>
							</div>
							<div class="fbFrndsDt">
							<?php echo $user->getBirthDay();?>
							</div>
						</td>
						<?php
						$iterator->next();
					}
					?>
					</tr>
				</table> <?php }?> <?php
				$no_data = true;
				foreach (array_keys($eBayUrls) as $i) {
					$eBayResponse= $ebayApiHelper->loadDataFromEbay($eBayUrls[$i]);
					$items = $ebayApiHelper->parseResponse($eBayResponse);
					if($items == null) {
						continue;
					}
					$iterator = $items->getIterator();
					?>
				<table border="0">
					<tr>
						<td class="tcol" colspan="5"><?php 
						if($user_has_likes) {
							?>
							<div class="fbMdlHdrUser">
								Gift ideas from eBay based on
								<?php
								$name = ($is_friend_profile_page == true) ? $logged_in_user[Constants::first_name] : "your";
								echo $name;?>
								likes on Facebook
							</div> <?php } else {
								?>
							<div class="fbMdlHdr">Gift ideas from eBay - based on popular
								among your Facebook friend(s)</div>
							<div class="fbMdlSubHdr">(Not personalized, since you dont have
								any favorite books, music, or movies on Facebook)</div> <?php
							}
							?>
						</td>
					</tr>
					<tr>
					<?php
					while($iterator->valid()) {
						$item = $iterator->current();
						$no_data = false;
						?>
						<td class="tcol" valign="top"
							width="<?php echo $item_td_width?>px">
							<table border="0">
								<tr>
									<td height="<?php echo $item_td_height?>px"><?php
									$img = $item->getImageUrl();
									if ($is_facebook) {
										$img = "http://thumbs4.ebaystatic.com/pict/". $item->getId()."8080.jpg";
									}

									if($img == null) {
										$img = "img/no_img.PNG";
									}?> <a href="<?php echo $item->getViUrl();?>" target="_blank">
											<img border=0 src="<?php echo $img;?>"
											title="<?php echo $item->getTitle();?>" /> </a>
									</td>
								</tr>
							</table>
							<div class="title">
								<a href="<?php echo $item->getViUrl();?>"><?php echo substr($item->getTitle(), 0, 25)."...";?>
								</a>
							</div>
							<div class="price">
							<?php echo "$".$item->getPrice();?>
							</div>
						</td>
						<?php
						$iterator->next();
					}
					?>
					</tr>
				</table> <?php  } ?> <?php
				if($no_data) {
					echo "<br/><br/><br/>";
					echo "Sorry, unfortunately we could not find related items on eBay based on your Facebook interests and likes.";
				}

				if($show_more_link) {
					?> <br />
				<div align="center">
					<a
						href="?home.php&id=<?php echo $logged_in_user[Constants::id]?>&pgcnt=<?php echo $pgcnt;?>">{{Show
						more recommendations}}</a>
				</div> <?php }?>
			</td>
		</tr>
	</table>

</body>
</html>
					<?php
					function getFbHdr($param) {
						return "<div class=\"fbFieldhdr\">" .$param ."</div>";
					}
					function getFbValue($param) {
						return "<div class=\"fbFieldValue\">" .$param ."</div><br/>";
					}
					?>
<?php
require 'Friends.php';
require 'userpopularlikesdao.php';
require 'Constants.php';
require 'LikeCategory.php';
session_start();

$friends = new Friends();
$logged_in_user = $friends->getUser("me", "&fields=id", $_SESSION[Constants::access_token]);
$user_popular_likes = new userpopularlikesdao();
$popular =	$user_popular_likes->fetch_all($logged_in_user[Constants::id], 5);
$rover_url = "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336805212&customid=itm-mdl&toolid=1&mpre=";
$ebay_srp_base_url = "http://shop.ebay.com/i.html?";
?>

<!doctype html>
<html>
<link href="css/style.css" rel="stylesheet" type="text/css">
<head>
<title>Gift Recommendations / ideas on eBay</title>
</head>
<body>
<?php include_once("analyticstracking.php") ?>
<?php include("menu.php"); ?>
	<br />
	<table class="tbl">
		<tr class="trow">
			<td width="600" class="tcol" colspan="4">
				<div class="fbFrndsHdr">Popular among your friends</div>
				<div class="fbFrndsSubHdr">(sorted by no of likes)</div>
			</td>
		</tr>
		<?php
		$i=0;
		foreach (array_keys($popular) as $name) {
			$i++;
			if($i == 20) {
				break;
			}
			$category = $popular[$name];
			$ebay_srp_url = $ebay_srp_base_url."_nkw=" . $name . "&_sacat=";
			$cat_id = NULL;
			if($category == LikeCategory::Movie) {
				$cat_id = LikeCategory::movie_cat_id;
			} else if($category == LikeCategory::Book) {
				$cat_id = LikeCategory::book_cat_id;
			} else if($category == LikeCategory::Music || $category == LikeCategory::Musician) {
				$cat_id = LikeCategory::music_cat_id;
			} else if($category == LikeCategory::Tv_show) {
				$cat_id = LikeCategory::tv_show_cat_id;
			}
			$ebay_srp_url = $rover_url . urlencode($ebay_srp_url . $cat_id);
			?>
		<tr>
			<td class="tcol"><a href="<?php echo $ebay_srp_url;?>"
				target="_blank"><?php echo $name;?> </a></td>
			<td class="tcol"><?php echo $category;?></td>
			<td class="tcol"><a href="<?php echo $ebay_srp_url;?>"
				target="_blank"> Search on eBay</a>
			</td>
			<td class="tcol"><a
				href="http://www.facebook.com/search.php?q=<?php echo urlencode($name);?>"
				target="_blank">Like</a>
			</td>
		</tr>
		<?php
		$ebay_srp_url = NULL;
		}?>
		<tr>
			<td colspan="4"><?php
			if($i == 0) {
				echo "Sorry we do not have the data to show you. Either you are logging in for the first time or something went wrong";
			}
			?>
			</td>
		</tr>
	</table>
</body>
</html>

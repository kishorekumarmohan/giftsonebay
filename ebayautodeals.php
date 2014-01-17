<?php
error_reporting(E_ALL & ~E_ALL);

$rover_url = "http://rover.ebay.com/rover/1/711-53200-19255-0/1?campid=5336805212&customid=itm-mdl&toolid=1&mpre=";
$auto_only = $_GET["auto_only"];
$ebay_auto_deals_url = "http://apps.motors.ebay.com/partsdeal";
$auto_xml_arr = 	object2array(simpleXML_load_file($ebay_auto_deals_url,"SimpleXMLElement",LIBXML_NOCDATA));

$core_xml_arr = null;
if($auto_only != 1) {
	$ebay_core_deals_url = "http://deals.ebay.com/feeds/xml";
	$core_xml_arr = object2array(simpleXML_load_file($ebay_core_deals_url,"SimpleXMLElement",LIBXML_NOCDATA));
}
?>

<!doctype html>
<html>
<link href="css/autodeal.css" rel="stylesheet" type="text/css">
<head>
<title>Great deals on electronics, video games, jewellery, cars & trucks
	parts accessories on eBay</title>
</head>

<body>
<?php include_once("analyticstracking.php") ?>

<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fapps.facebook.com%2Febayautodeals%2F&amp;layout=standard&amp;show_faces=true&amp;width=450&amp;action=like&amp;font=lucida+grande&amp;colorscheme=light&amp;height=80" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:20px;" allowTransparency="true"></iframe>
<br/>
<?php
if($core_xml_arr) {
	?>
	<table width="740px" class="tbl">
		<tr class="tblRow">
			<td class="autoDealsPageHdr" colspan="4">&nbsp;Today's great deals on
				eBay</td>
		</tr>
		<tr>
		<?php
		$item_arr= $core_xml_arr["Item"];
		foreach (array_keys($item_arr) as $i) {
			$url = $rover_url.urlencode("http://cgi.ebay.com/".$item_arr[$i]['ItemId']);
			?>
			<td valign="top" width="150px" class="tblCol">
				<div>
					<a href="<?php echo $url;?>" target="_blank"><img border="0"
						width="150px" height="150px"
						src="<?php echo $item_arr[$i]['Picture175Url']?>" /> </a>
				</div>
				<div class="title">
					<a href="<?php echo $url;?>" target="_blank"><?php echo $item_arr[$i]['Title'];?>
					</a>
				</div>
				<div style="padding-bottom: 5px;">
					<span class="label">Price:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</span> <span class="price"><?php echo "$".$item_arr[$i]['ConvertedCurrentPrice'];?>
					</span>

				</div>
				<div style="padding-bottom: 5px;">
					<span class="label">MSRP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</span> <span class="msrp"><?php echo "$".$item_arr[$i]['MSRP'];?>
					</span>
				</div>
				<div>
					<span class="label">You save:&nbsp;&nbsp; </span> <span
						class="savings"><?php echo $item_arr[$i]['SavingsRate'];?> </span>
				</div>
			</td>
			<?php 	}
			//}
			?>
		</tr>
	</table>
	<?php
}
?>
	<br />

	<table width="740px" class="tbl">
		<tr class="tblRow">
			<td class="autoDealsPageHdr" colspan="4">&nbsp;Today's great deals on
				eBay Motors</td>
		</tr>
		<tr>
		<?php
		$item_arr;
		foreach (array_keys($auto_xml_arr) as $value) {
			$item_arr= $auto_xml_arr[$value];
			foreach (array_keys($item_arr) as $i) {
				$url = $rover_url.urlencode($item_arr[$i]['DealURL']);
				?>
			<td valign="top" width="150px" class="tblCol">
				<div>
					<a href="<?php echo $url;?>" target="_blank"><img border="0"
						width="150px" height="150px"
						src="<?php echo $item_arr[$i]['PictureURL']?>" /> </a>
				</div>
				<div class="title">
					<a href="<?php echo $url;?>" target="_blank"><?php echo $item_arr[$i]['Title'];?>
					</a>
				</div>
				<div style="padding-bottom: 5px;">
					<span class="label">Price:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</span> <span class="price"><?php echo "$".$item_arr[$i]['ConvertedCurrentPrice'];?>
					</span>

				</div>
				<div style="padding-bottom: 5px;">
					<span class="label">MSRP:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</span> <span class="msrp"><?php echo "$".$item_arr[$i]['MSRP'];?>
					</span>
				</div>
				<div>
					<span class="label">You save:&nbsp;&nbsp; </span> <span
						class="savings"><?php echo $item_arr[$i]['SavingsRate'];?> </span>
				</div>
			</td>
			<?php 	}
		}
		?>
		</tr>
	</table>
</body>
</html>
		<?php
		function object2array($object) {
			return @json_decode(@json_encode($object),4);
		}
		?>
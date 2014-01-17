<?php

require 'Like.php';
require 'LikeCategory.php';
require 'config.php';
class EbayApiHelper {
	// TODO change [YOUR_APP_ID] with your app id
	const EBAY_URL = "http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&RESPONSE-DATA-FORMAT=JSON&affiliate.networkId=9&affiliate.trackingId=5336805212&affiliate.customId=itm-mdl&paginationInput.entriesPerPage=5";

	function buildEbayApiUrls($likes, $pgcnt) {
		$EBAY_URL = self::EBAY_URL . "&SECURITY-APPNAME=". config::EBAY_APP_ID;
		$eBayUrls = array();
		$iterator = $likes->getIterator();
		$i =0;
		while($iterator->valid()) {
			$like = $iterator->current();
			$tempUrl = $EBAY_URL;

			if($like->getEBayCatId() != null) {
				$tempUrl = $tempUrl. "&categoryId=" . $like->getEBayCatId();
			}
			if($like->getName() != null) {
				$tempUrl = $tempUrl . "&keywords=" . urlencode($like->getName());
			}
			$eBayUrls[$i++] = $tempUrl;
			if($i >= $pgcnt){
				break;
			}
			$iterator->next();
		}
		return $eBayUrls;
	}

	function loadDataFromEbay($url) {
		if ($url != null) {
			$eBayResponse = json_decode(file_get_contents($url), true);
			return $eBayResponse["findItemsAdvancedResponse"][0]["searchResult"][0]["item"];
		}
	}

	function parseResponse($eBayResponse) {
		if($eBayResponse == null) {
			return;
		}
		$items = new ArrayObject();
		$count = sizeof($eBayResponse);
		if($count <= 0) {
			return;
		}
		foreach (array_keys($eBayResponse) as $obj) {
			$item = new Item($eBayResponse[$obj]["itemId"][0], $eBayResponse[$obj]["viewItemURL"][0], $eBayResponse[$obj]["title"][0], $eBayResponse[$obj]["galleryURL"][0], $eBayResponse[$obj]["sellingStatus"][0]["convertedCurrentPrice"][0]["__value__"]);
			$items->append($item);
		}
		return $items;
	}
}
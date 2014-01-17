<?php
/**
 * User: kmohan
 * Date: 3/1/11
 * Time: 7:13 PM
 */

class Item {

	private $id;
	private $viUrl;
	private $title;
	private $imageUrl;
	private $price;

	function __construct($id, $viUrl, $title, $imageUrl, $price) {
		$this->id = $id;
		$this->viUrl = $viUrl;
		$this->title = $title;
		$this->imageUrl = $imageUrl;
		$this->price= $price;
	}

	public function getId() { return $this->id; }
	public function getViUrl() { return $this->viUrl; }
	public function getTitle() { return $this->title; }
	public function getImageUrl() { return $this->imageUrl; }
	public function getPrice() { return $this->price; }
	public function setId($x) { $this->id = $x; }
	public function setViUrl($x) { $this->viUrl = $x; }
	public function setTitle($x) { $this->title = $x; }
	public function setImageUrl($x) { $this->imageUrl = $x; }
	public function setPrice($x) { $this->price = $x; }
}

<?php
class Like{
	private $parentCategory;
	private $category;
	private $name;
	private $eBayCatId;
	private $kw;

	function __construct($parentCategory, $category, $name, $eBayCatId, $kw) {
		$this->parentCategory = $parentCategory;
		$this->category = $category;
		$this->name = $name;
		$this->eBayCatId = $eBayCatId;
		$this->kw = $kw;
	}

	public function getParentCategory() { return $this->parentCategory; }
	public function getCategory() { return $this->category; }
	public function getName() { return $this->name; }
	public function getEBayCatId() { return $this->eBayCatId; }
	public function getKw() { return $this->kw; }
	public function setParentCategory($x) { $this->parentCategory = $x; }
	public function setCategory($x) { $this->category = $x; }
	public function setName($x) { $this->name = $x; }
	public function setEBayCatId($x) { $this->eBayCatId = $x; }
	public function setKw($x) { $this->kw = $x; }
}
<?php
/**
 * User: kmohan
 * Date: 3/1/11
 * Time: 7:04 PM
 * To change this template use File | Settings | File Templates.
 */

class User {
	private $id;
	private $name;
	private $birthDay;

	function __construct($id, $name, $birthDay) {
		$this->id = $id;
		$this->name = $name;
		$this->birthDay = $birthDay;
	}

	public function getId() { return $this->id; }
	public function getName() { return $this->name; }
	public function getBirthDay() { return $this->birthDay; }
	public function setId($x) { $this->id = $x; }
	public function setName($x) { $this->name = $x; }
	public function setBirthDay($x) { $this->birthDay = $x; }
}

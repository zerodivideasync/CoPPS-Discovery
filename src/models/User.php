<?php

require_once DATABASE;
require_once COMPONENTS . 'propertiesConfig.php';

/**
 * Class containing attributes and getter/setter methods of the User entity
 * 
 * @var integer	$id User Id
 * @var string $username User username
 * @var string password	 User password
 * @var integer power	Represents the user privilege lever (specified in components/propertiesConfig.php)
 */
class User {

	private $id;
	private $username;
	private $password;
	private $power;

	function __construct($id = null, $username = null, $password = null, $power = POWERGUEST) {
		$this->id = $id;
		$this->username = $username;
		$this->password = (hash('sha512', $password, false));
		$this->power = $power;
	}

	public function getId() {
		return $this->id;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getPassword() {
		return $this->password;
	}

	public function getPower() {
		return $this->power;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setUsername($username) {
		$this->username = $username;
	}

	public function setPassword($password) {
		$this->password = $password;
	}

	public function setPower($power) {
		$this->power = $power;
	}

	
	/**
	 * Sets the sha512 hash of the given password as the password attribute.
	 * @param string $password is the password to be hashed
	 */
	public function setPasswordHash($password) {
		$this->password = (hash('sha512', $password, false));
	}

	public function __destruct() {
		
	}

}

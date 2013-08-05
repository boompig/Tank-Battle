<?php

/**
 * Represents a user, sharing same params as DB table 'user'
 */
class User  {

	const OFFLINE = 1;
	const AVAILABLE = 2;
	const WAITING = 3;
	const INVITED = 4;
	const BATTLING = 5;
	
	
	public $id;
	public $login;
	public $first;
	public $last;
	public $password;   // hashed version
	public $salt;
	public $email;	
	public $user_status_id = User::OFFLINE;
	public $invite_id;
	public $battle_id;
	
	public function encryptPassword($clearPassword) {
		$this->salt = mt_rand();
		$this->password = sha1($this->salt . $clearPassword);
	}
	
	/**
	 * Create a random password with salt. Store salt and *hashed* password in object.
	 * Return just the password, in the clear.
	 * This is used if user forgot their password, to generate a new one.
	 * @return {String}
	 */
	public function initPassword() {
		$this->salt = mt_rand();
		$clearPassword = mt_rand();
		$this->password = sha1($this->salt . $clearPassword);
		return $clearPassword;	
	}
	
	/**
	 * Return true iff the given password corresponds to the stored password
	 * @param {String} $clearPassword
	 * @return {Boolean}
	 */
	public function comparePassword($clearPassword) {
		return $this->password === sha1($this->salt . $clearPassword);
	}
	
	public function fullName() {
		return $this->first . " " . $this->last;
	}
	
}
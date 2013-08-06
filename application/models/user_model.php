<?php
class User_model extends CI_Model {

	/**
	 * Return a User object if a user with the given username exists, null otherwise
	 * @param {String} $username
	 * @return {User}
	 */
	function get($username) {
		$this -> db -> where('login', $username);
		$query = $this -> db -> get('user');
		if ($query && $query -> num_rows() > 0)
			return $query -> row(0, 'User');
		// instantiate this row with class User
		else
			return null;
	}

	function getFromId($id) {
		$this -> db -> where('id', $id);
		$query = $this -> db -> get('user');
		if ($query && $query -> num_rows() > 0)
			return $query -> row(0, 'User');
		else
			return null;
	}

	function getFromEmail($email) {
		$this -> db -> where('email', $email);
		$query = $this -> db -> get('user');
		if ($query && $query -> num_rows() > 0)
			return $query -> row(0, 'User');
		else
			return null;
	}

	function insert($user) {
		return $this -> db -> insert('user', $user);
	}

	function updatePassword($user) {
		$this -> db -> where('id', $user -> id);
		return $this -> db -> update('user', array('password' => $user -> password, 'salt' => $user -> salt));
	}

	function updateStatus($id, $status) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('user', array('user_status_id' => $status));
	}

	function updateInvitation($id, $invitationId) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('user', array('invite_id' => $invitationId));
	}

	function updateBattle($id, $battleId) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('user', array('battle_id' => $battleId));
	}

	/**
	 * Return either a CI result object for usernames of available users, or null if none exist.
	 */
	function getAvailableUsers() {
		$this -> db -> where('user_status_id', User::AVAILABLE);
		$query = $this -> db -> get('user');
		if ($query && $query -> num_rows() > 0)
			return $query -> result('User');
		else
			return null;
	}

	function getExclusive($username) {
		$sql = "select * from user where login=? for update";
		$query = $this -> db -> query($sql, array($username));
		if ($query && $query -> num_rows() > 0)
			return $query -> row(0, 'User');
		else
			return null;
	}
}
?>

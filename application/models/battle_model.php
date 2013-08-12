<?php
/**
 * Straight forward interface with the DB Table battle.
 */
class Battle_model extends CI_Model {

	/**
	 * Get an exclusive lock on the battle table, for the given battle ID.
	 * @param {int} $id The battle ID
	 * @return null if cannot acquire lock, Battle object if battle found
	 */
	function getExclusive($id) {
		$sql = "select * from battle where id=? for update";
		$query = $this -> db -> query($sql, array($id));
		if ($query && $query -> num_rows() > 0) {
			return $query -> row(0, 'Battle');
		} else {
			return null;
		}
	}

	/**
	 * Return the battle object associated with the given battle ID
	 * @param $id Battle ID
	 * @return Battle object on success, null on failure
	 */
	function get($id) {
		$this -> db -> where('id', $id);
		$query = $this -> db -> get('battle');
		if ($query && $query -> num_rows() > 0)
			return $query -> row(0, 'Battle');
		else
			return null;
	}

	/**
	 * Create a new battle by inserting some preliminary records.
	 */
	function insert($battle) {
		return $this -> db -> insert('battle', $battle);
	}
	
	/**
	 * Wrappper function so don't always have to check if current user is u1 or u2
	 * @param $userID 
	 * @param $battleID Battle ID
	 * @param $x1 Tank x-coordinate
	 */
	function updateUser ($userID, $battleID, $x1, $y1, $x2, $y2, $angle, $shot, $hit) {
		
		$battleObj = $this -> get($battleID);
		
		if ($battleObj -> user1_id === $userID) {
			$this -> updateU1($battleID, $x1, $y1, $x2, $y2, $angle, $shot, $hit);
		} else {
			$this -> updateU2($battleID, $x1, $y1, $x2, $y2, $angle, $shot, $hit);
		}
	}

	/**
	 * @param $id Battle ID
	 * @param $x1 Tank x-coordinate
	 * @param $y1 Tank y-coordinate
	 */
	function updateU1($id, $x1, $y1, $x2, $y2, $angle, $shot, $hit) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('u1_x1' => $x1, 'u1_y1' => $y1, 'u1_x2' => $x2, 'u1_y2' => $y2, 'u1_angle' => $angle, 'u1_shot' => $shot, 'u1_hit' => $hit));
	}

	function clearShotU1($id) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('u1_shot' => false));
	}

	function updateU2($id, $x1, $y1, $x2, $y2, $angle, $shot, $hit) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('u2_x1' => $x1, 'u2_y1' => $y1, 'u2_x2' => $x2, 'u2_y2' => $y2, 'u2_angle' => $angle, 'u2_shot' => $shot, 'u2_hit' => $hit));
	}

	function clearShotU2($id) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('u2_shot' => false));
	}

	function updateMsgU1($id, $msg) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('u1_msg' => $msg));
	}

	function updateMsgU2($id, $msg) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('u2_msg' => $msg));
	}

	function updateStatus($id, $status) {
		$this -> db -> where('id', $id);
		return $this -> db -> update('battle', array('battle_status_id' => $status));
	}

}
?>
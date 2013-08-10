<?php

class Combat extends CI_Controller {

	function __construct() {
		// Call the Controller constructor
		parent::__construct();
		session_start();
	}

	public function _remap($method, $params = array()) {
		// enforce access control to protected functions

		if (!isset($_SESSION['user']))
			redirect('account/loginForm', 'refresh');
		//Then we redirect to the index page again

		return call_user_func_array(array($this, $method), $params);
	}

	function index() {
		$user = $_SESSION['user'];

		$this -> load -> model('user_model');
		$this -> load -> model('invite_model');
		$this -> load -> model('battle_model');

		$user = $this -> user_model -> get($user -> login);

		$invite = $this -> invite_model -> get($user -> invite_id);

		if ($user -> user_status_id == User::WAITING) {
			$invite = $this -> invite_model -> get($user -> invite_id);
			$otherUser = $this -> user_model -> getFromId($invite -> user2_id);
		} else if ($user -> user_status_id == User::BATTLING) {
			$battle = $this -> battle_model -> get($user -> battle_id);
			if ($battle -> user1_id == $user -> id)
				$otherUser = $this -> user_model -> getFromId($battle -> user2_id);
			else
				$otherUser = $this -> user_model -> getFromId($battle -> user1_id);
		}

		$data['user'] = $user;
		$data['otherUser'] = $otherUser;

		switch($user->user_status_id) {
			case User::BATTLING :
				$data['status'] = 'battling';
				break;
			case User::WAITING :
				$data['status'] = 'waiting';
				break;
		}

		$this -> load -> view('battle/battleField', $data);
	}

	/**
	 * Called by battleField.php to post the results of the battle.
	 */
	function postBattleStatus () {
		$this -> load -> library('form_validation');
		$this -> form_validation -> set_rules("status", "Game winner", 'required|matches[active|win|loss|draw]');
		
		
	}

	/**
	 * Called by battleField.php to post the tank positions, turret angle, and other mid-game info.
	 * Report only own status:
	 * 		x		-	tank x (int)
	 * 		y		-	tank y (int)
	 * 		angle	-	turret angle (in degrees) (int)
	 * 
	 */
	function postBattle () {
		$this -> load -> library('form_validation');
		$this -> form_validation -> set_rules("x", "Tank x-coordinate", 'required|integer');
		$this -> form_validation -> set_rules("y", "Tank y-coordinate", 'required|integer');
		$this -> form_validation -> set_rules("angle", "Tank y-coordinate", 'required|integer');
		
		if ($this -> form_validation -> run()) {
			
			// makes sure this user is battling
			$this -> load -> model('user_model');
			$user = $_SESSION['user'];
			$user = $this -> user_model -> getExclusive($user -> login);
			if ($user -> user_status_id != User::BATTLING) {
				$errMsg = "Not in BATTLING state";
				goto error; //ewwwww using gotos in code is gross
			}

			$this -> load -> model('battle_model');
			$x1 = $this -> input -> post('x1');
			$y1 = $this -> input -> post('y1');
			$angle = $this -> input -> post('angle');

			// TODO setting a bunch of variables for now
			
			$this -> battle_model -> updateUser($user -> id, $user -> battle_id, $x1, $y1, 0, 0, $angle, false, false);

			echo json_encode(array('status' => 'success'));
			
			return;
		}
		
		$errMsg = "Missing arg";
		
		error:
			echo json_encode(array('status' => 'failure', 'message' => $errMsg));
	}

	/**
	 * Return information about the game from the server.
	 */
	function getBattle () {
		$this -> load -> model('user_model');
		$this -> load -> model('battle_model');

		$user = $_SESSION['user'];

		$user = $this -> user_model -> get($user -> login);
		if ($user -> user_status_id != User::BATTLING) {
			$errormsg = "Not in BATTLING state";
			goto error;
		}
		// start transactional mode
		$this -> db -> trans_begin();

		$battle = $this -> battle_model -> getExclusive($user -> battle_id);
		$data = array("status" => "success");

		if ($battle -> user1_id == $user -> id) {
			$data['x'] = $battle -> u2_x1;
			$data['y'] = $battle -> u2_x1;
			$data['angle'] = $battle -> u2_angle;
		} else {
			$data['x'] = $battle -> u1_x1;
			$data['y'] = $battle -> u1_x1;
			$data['angle'] = $battle -> u1_angle;
		}

		if ($this -> db -> trans_status() === FALSE) {
			$errormsg = "Transaction error";
			goto transactionerror;
		}

		// if all went well commit changes
		$this -> db -> trans_commit();

		echo json_encode($data);
		return;

		transactionerror:
			$this -> db -> trans_rollback();

		error:
			echo json_encode(array('status' => 'failure', 'message' => $errormsg));
	}
}

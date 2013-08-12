<?php

class Arcade extends CI_Controller {

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
		$data['user'] = $_SESSION['user'];
		if (isset($_SESSION['errmsg'])) {
			$data['errmsg'] = $_SESSION['errmsg'];
			unset($_SESSION['errmsg']);
		}
		$this -> load -> view('arcade/mainPage', $data);
	}

	/**
	 * A poll method to check for newly available users.
	 * Loads the availableUsers view with the data.
	 */
	function getAvailableUsers() {
		$this -> load -> model('user_model');
		$users = $this -> user_model -> getAvailableUsers();
		$data['users'] = $users;
		$data['currentUser'] = $_SESSION['user'];
		$this -> load -> view('arcade/availableUsers', $data);
	}

	/**
	 * A poll method to check for new invitations.
	 * Returns some JSON
	 */
	function getInvitation() {
		$user = $_SESSION['user'];

		$this -> load -> model('user_model');
		$user = $this -> user_model -> get($user -> login);

		// if the current user has been invited to battle
		if ($user -> user_status_id == User::INVITED) {
			$this -> load -> model('invite_model');
			$invite = $this -> invite_model -> get($user -> invite_id);
			$hostUser = $this -> user_model -> getFromId($invite -> user1_id);

			$msg = array('invited' => true, 'login' => $hostUser -> login);
			echo json_encode($msg);
		} else {
			$msg = array('invited' => false);
			echo json_encode($msg);
		}
	}

	/**
	 * Called from view arcade/mainPage
	 * Used to accept an invitation.
	 */
	function acceptInvitation() {
		$user = $_SESSION['user'];
		$data = array();

		$this -> load -> model('user_model');
		$this -> load -> model('invite_model');
		$this -> load -> model('battle_model');

		$user = $this -> user_model -> getExclusive($user -> login);

		$invite = $this -> invite_model -> get($user -> invite_id);
		$hostUser = $this -> user_model -> getFromId($invite -> user1_id);

		// start transactional mode
		$this -> db -> trans_begin();

		// change status of invitation to ACCEPTED
		$this -> invite_model -> updateStatus($invite -> id, Invite::ACCEPTED);
		
		if ($this -> db -> trans_status() === FALSE)
			goto transactionerror;
		
		$data["invite accepted insert good"] = true;

		// create a battle entry
		$battle = new Battle();
		$battle -> user1_id = $user -> id;
		$battle -> user2_id = $hostUser -> id;
		
		// create default spawn points and turret angles here
		// user 1 starts at (60, 60) with turret @ 90 degrees
		$battle -> u1_x1 = 60;
		$battle -> u1_y1 = 60;
		$battle -> u1_angle = 0;
		
		// user 2 starts at (540, 340) with turret @ 270 degrees
		$battle -> u2_x1 = 540;
		$battle -> u2_y1 = 340;
		$battle -> u2_angle = 180;
		
		$data["battle"] = $battle;
		$this -> battle_model -> insert($battle);
		
		if ($this -> db -> trans_status() === FALSE) {
			// $data["msg"] = htmlentities($this -> db -> error_message());
			goto transactionerror;
		}
		
		$data["battleInsert"] = true;
		$battleId = mysql_insert_id();

		// update status of both users
		$this -> user_model -> updateStatus($user -> id, User::BATTLING);
		$this -> user_model -> updateStatus($hostUser -> id, User::BATTLING);

		$this -> user_model -> updateBattle($user -> id, $battleId);
		$this -> user_model -> updateBattle($hostUser -> id, $battleId);

		if ($this -> db -> trans_status() === FALSE)
			goto transactionerror;

		// if all went well commit changes
		$this -> db -> trans_commit();

		echo json_encode(array('status' => 'success'));

		return;

		// something went wrong
		transactionerror:
			$this -> db -> trans_rollback();
			$data['status'] = 'failure';
			$data["textStatus"] = "transaction status is failing";
			echo json_encode($data);
	}

	/**
	 * Called from view arcade/mainPage
	 * Used to decline an invitation.
	 */
	function declineInvitation() {
		$user = $_SESSION['user'];

		$this -> load -> model('user_model');
		$this -> load -> model('invite_model');

		$user = $this -> user_model -> get($user -> login);
		$invite = $this -> invite_model -> get($user -> invite_id);

		// start transactional mode
		$this -> db -> trans_begin();

		// change status of invitation to REJECTED
		$this -> invite_model -> updateStatus($invite -> id, Invite::REJECTED);

		// update status
		$this -> user_model -> updateStatus($user -> id, User::AVAILABLE);

		if ($this -> db -> trans_status() === FALSE)
			goto transactionerror;

		// if all went well commit changes
		$this -> db -> trans_commit();

		echo json_encode(array('status' => 'success'));

		return;

		// something went wrong
		transactionerror:
		$this -> db -> trans_rollback();

		echo json_encode(array('status' => 'failure'));
	}

	/**
	 * Called by battleField view to see if invitation was accepted, rejected, or is pending.
	 */
	function checkInvitation() {
		$user = $_SESSION['user'];

		$this -> load -> model('user_model');
		$this -> load -> model('invite_model');

		$user = $this -> user_model -> get($user -> login);

		$invite = $this -> invite_model -> get($user -> invite_id);

		switch($invite -> invite_status_id) {
			case Invite::ACCEPTED:
				echo json_encode(array('status' => 'accepted'));
				break;
			case Invite::PENDING:
				echo json_encode(array('status' => 'pending'));
				break;
			case Invite::REJECTED:
				$this -> user_model -> updateStatus($user -> id, User::AVAILABLE);
				echo json_encode(array('status' => 'rejected'));
				break;
		}
	}

	/**
	 * Called by view arcade/mainPage
	 * Used to invite another user to a battle.
	 */
	function invite() {
		try {
			$login = $this -> input -> get('login');

			if (! isset($login))
				goto loginerror;

			$user1 = $_SESSION['user'];
			$user2 = null;

			$this -> load -> model('user_model');
			$this -> load -> model('invite_model');

			// start transactional mode
			$this -> db -> trans_begin();

			// lock both user records in alphabetic order to prevent deadlocks
			if (strcmp($user1 -> login, $login) < 0) {
				$user1 = $this -> user_model -> getExclusive($user1 -> login);
				$user2 = $this -> user_model -> getExclusive($login);
			} else {
				$user2 = $this -> user_model -> getExclusive($login);
				$user1 = $this -> user_model -> getExclusive($user1 -> login);
			}

			if (!isset($user2) || $user2 -> user_status_id != User::AVAILABLE)
				goto nouser2;

			// update status of both users
			$this -> user_model -> updateStatus($user1 -> id, User::WAITING);
			$this -> user_model -> updateStatus($user2 -> id, User::INVITED);

			// create an invite entry
			$invite = new Invite();
			$invite -> user1_id = $user1 -> id;
			$invite -> user2_id = $user2 -> id;

			$this -> invite_model -> insert($invite);

			$inviteId = mysql_insert_id();

			$this -> user_model -> updateInvitation($user1 -> id, $inviteId);
			$this -> user_model -> updateInvitation($user2 -> id, $inviteId);

			if ($this -> db -> trans_status() === FALSE)
				goto transactionerror;

			// if all went well commit changes
			$this -> db -> trans_commit();

			redirect('combat/index', 'refresh');
			//redirect to combat stage

			return;

			// something went wrong
			transactionerror:
			nouser2:
				$this -> db -> trans_rollback();

			loginerror:
				$_SESSION["errmsg"] = "Sorry, this user is no longer available.";

			redirect('arcade/index', 'refresh');
			//redirect to the main application page
		} catch(Exception $e) {
			$this -> db -> trans_rollback();
		}
	}
}

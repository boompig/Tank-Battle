<?php

class Account extends CI_Controller {

	private $fromEmail = "noreply@tankbattle.slav";

	function __construct() {
		// Call the Controller constructor
		parent::__construct();
		
		session_start();
		$this -> load -> library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		date_default_timezone_set('UTC');
	}

	public function _remap($method, $params = array()) {
		// enforce access control to protected functions

		$protected = array('updatePasswordForm', 'updatePassword', 'index', 'logout');

		if (in_array($method, $protected) && !isset($_SESSION['user']))
			redirect('account/loginForm', 'refresh');
		//Then we redirect to the index page again

		return call_user_func_array(array($this, $method), $params);
	}

	function loginForm() {
		$this -> load -> view('account/loginForm');
	}

	/**
	 * Login mechanism
	 */
	function login() {
		$this -> form_validation -> set_rules('username', 'Username', 'required');
		$this -> form_validation -> set_rules('password', 'Password', 'required');

		if ($this -> form_validation -> run() == FALSE) {
			$this -> load -> view('account/loginForm');
		} else {
			$login = $this -> input -> post('username');
			$clearPassword = $this -> input -> post('password');

			$this -> load -> model('user_model');

			$user = $this -> user_model -> get($login);

			if (isset($user) && $user -> comparePassword($clearPassword)) {
				$_SESSION['user'] = $user;
				$data['user'] = $user;

				$this -> user_model -> updateStatus($user -> id, User::AVAILABLE);

				redirect('arcade/index', 'refresh');
				//redirect to the main application page
			} else {
				$data['errorMsg'] = 'Incorrect username or password!';
				$this -> load -> view('account/loginForm', $data);
			}
		}
	}

	/**
	 * Logout mechanism
	 */
	function logout() {
		$user = $_SESSION['user'];
		$this -> load -> model('user_model');
		$this -> user_model -> updateStatus($user -> id, User::OFFLINE);
		session_destroy();
		redirect('account/index', 'refresh');
		//Then we redirect to the index page again
	}

	/**
	 * Open view to create new account.
	 */
	function newForm() {
		$this -> load -> view('account/newForm');
	}
	
	/**
	 * This function handles creation of new accounts.
	 * Data sent from newForm view.
	 */
	function createNew() {
		$this -> form_validation -> set_rules('username', 'Username', 'required|is_unique[user.login]');
		$this -> form_validation -> set_rules('password', 'Password', 'required');
		$this -> form_validation -> set_rules('first', 'First', "required");
		$this -> form_validation -> set_rules('last', 'last', "required");
		$this -> form_validation -> set_rules('email', 'Email', "required|is_unique[user.email]");

		if ($this -> form_validation -> run() == FALSE) {
			$this -> load -> view('account/newForm');
		} else {
			$user = new User();

			$user -> login = $this -> input -> post('username');
			$user -> first = $this -> input -> post('first');
			$user -> last = $this -> input -> post('last');
			$clearPassword = $this -> input -> post('password');
			$user -> encryptPassword($clearPassword);
			$user -> email = $this -> input -> post('email');

			$this -> load -> model('user_model');

			$this -> user_model -> insert($user);

			$this -> load -> view('account/loginForm');
		}
	}

	/**
	 * Load the form to change the password.
	 */
	function updatePasswordForm() {
		$this -> load -> view('account/updatePasswordForm');
	}

	/**
	 * Called by view account/updatePasswordForm to change the password.
	 */
	function updatePassword() {
		$this -> form_validation -> set_rules('oldPassword', 'Old Password', 'required');
		$this -> form_validation -> set_rules('newPassword', 'New Password', 'required');

		if ($this -> form_validation -> run() == FALSE) {
			$this -> load -> view('account/updatePasswordForm');
		} else {
			$user = $_SESSION['user'];

			$oldPassword = $this -> input -> post('oldPassword');
			$newPassword = $this -> input -> post('newPassword');

			if ($user -> comparePassword($oldPassword)) {
				$user -> encryptPassword($newPassword);
				$this -> load -> model('user_model');
				$this -> user_model -> updatePassword($user);
				$data['user'] = $user;
				$this -> load -> view('mainPage', $data);
			} else {
				$data['errorMsg'] = "Incorrect password!";
				$this -> load -> view('account/updatePasswordForm', $data);
			}
		}
	}

	/**
	 * Load the form to reset the password.
	 */
	function recoverPasswordForm() {
		$this -> load -> view('account/recoverPasswordForm');
	}

	/**
	 * Called by account/recoverPasswordForm view.
	 * Used to send an email with a new temporary password.
	 */
	function recoverPassword() {
		$this -> form_validation -> set_rules('email', 'email', 'required');

		if ($this -> form_validation -> run() == FALSE) {
			$this -> load -> view('account/recoverPasswordForm');
		} else {
			$email = $this -> input -> post('email');
			$this -> load -> model('user_model');
			$user = $this -> user_model -> getFromEmail($email);

			if (isset($user)) {
				$newPassword = $user -> initPassword();
				$this -> user_model -> updatePassword($user);

				$this -> load -> library('email');
				
				$config['protocol'] = 'smtp';
				$config['smtp_host'] = 'ssl://smtp.gmail.com';
				$config['smtp_port'] = '465';
				$config['smtp_timeout'] = '7';
				$config['smtp_user'] = 'dbkats@gmail.com';
				$config['smtp_pass'] = 'potatogreatgoodthink';
				$config['charset'] = 'utf-8';
				$config['newline'] = "\r\n";
				$config['mailtype'] = 'text';
				// or html
				$config['validation'] = TRUE;
				// bool whether to validate email or not

				$this -> email -> initialize($config);

				$this -> email -> from ($this->fromEmail);
				$this -> email -> to ($email);
				$this -> email -> subject ("Tank Battle - Password Recovery");
				$this -> email -> message ("Your new password is $newPassword");

				$result = $this -> email -> send();

				$this -> load -> view('account/emailPage');

			} else {
				$data['errorMsg'] = "No account with that email address exists!";
				$this -> load -> view('account/recoverPasswordForm', $data);
			}
		}
	}

}

<?php
$this->load->model("html_utils");
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Login | Tank Battle</title>
		<meta charset="UTF-8" />
		<link rel="icon" type="image/gif" href="<?=base_url() ?>images/tank_transparent.GIF" />
		
		<!-- Google-hosted libaries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		
		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/south-street/jquery-ui.css" />
		
		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/header.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/login.css" />
				
		<script>
			$(function() {
				$("input[type=submit], button").button();
			});
		</script>

	</head>
	<body>
		<?php $this->load->view("general/header"); ?>
		
		<div id="content">
			<div id="logoPane">
				<div>
					<span>Login to Tank Battle. If you don't have an account, you can</span>
					 <?php
					 	echo anchor('account/newForm', 'create an account now');
					 ?>
					 <span>.</span>
				</div>
				
				<img class="big-logo" src="<?=base_url() ?>images/tank.svg" />
			</div>
			
			<div id="loginForm">
				<?php
					if (isset($errorMsg)) {
						echo "<p>" . $errorMsg . "</p>";
					}
		
					echo form_open('account/login');
					echo form_label('Username');
					echo form_error('username');
					echo form_input('username', set_value('username'), "required");
					echo form_label('Password');
					echo form_error('password');
					echo form_password('password', '', "required");
					
				
					echo anchor('account/recoverPasswordForm', 'Forgot your password', array("id" => "forgotPasswordLink"));
				
					echo form_submit('submit', 'Login');
					echo form_close();
				?>
			</div>
		</div>
	</body>

</html>


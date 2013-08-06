<?php
$this->load->model("html_utils");
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Login | Tank Battle</title>
		<meta charset="UTF-8" />
		
		<!-- Google-hosted libaries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		
		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/sunny/jquery-ui.css" />
		
		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/login.css" />
				
		<script>
			$(function() {
				$("input[type=submit], button").button();
			});
		</script>

	</head>
	<body>
		<header>
			<h1>Login</h1>
		</header>
		
		<div id="content">
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
	
				echo form_submit('submit', 'Login');
	
				echo "<p>" . anchor('account/newForm', 'Create Account') . "</p>";
	
				echo "<p>" . anchor('account/recoverPasswordForm', 'Recover Password') . "</p>";
	
				echo form_close();
			?>
		</div>
	</body>

</html>


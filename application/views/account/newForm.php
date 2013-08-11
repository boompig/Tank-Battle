<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Create Account | Tank Battle</title>
		<meta charset="UTF-8" />
		<link rel="icon" type="image/gif" href="<?=base_url() ?>images/tank_transparent.GIF" />

		<!-- Google-hosted libaries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		
		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/south-street/jquery-ui.css" />
		
		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/register_user.css" />
		
		<script>
			$(function() {
				"use strict";
				
				$("input[type=submit], button").button();
				
				$("#content").tooltip();
				
				$(".refresh-captcha").click(function () {
					var src = '<?=base_url() ?>securimage/securimage_show.php?' + Math.random();
					$("#captcha").attr("src", src);
					return false;
				});
			});
		
			/**
			 * HTML5 password checking
			 */
			function checkPassword() {
				var p1 = $("#pass1");
				var p2 = $("#pass2");

				if (p1.val() == p2.val()) {
					p1.get(0).setCustomValidity("");
					// All is well, clear error message
					return true;
				} else {
					p1.get(0).setCustomValidity("Passwords do not match");
					return false;
				}
			}
		</script>
	</head>
	<body>
		<?php $this->load->view("general/header"); ?>
		<div id="content">
			
			<!-- it's a huge pain to put this in another view, so will just copy-paste -->
			<link rel="stylesheet" href="<?=base_url() ?>css/logopane.css" />
			<div id="logoPane">
				<div class="logoMsg">
					<h3>Register</h3>
					
					<span>Register for a Tank Battle account by filling in all of the fields on the right.</span>
				</div>
				
				<img class="big-logo" src="<?=base_url() ?>images/tank.svg" />
			</div> <!-- end logoPane -->

			<div id="registration">
				<?php
					echo form_open('account/createNew');
					
					echo form_label('Username');
					echo form_error('username');
					echo form_input('username', set_value('username'), "required");
					
					echo form_label('Password');
					echo form_error('password');
					echo form_password('password', '', "id='pass1' required");
					
					echo form_label('Confirm Password');
					echo form_error('passconf');
					echo form_password('passconf', '', "id='pass2' required oninput='checkPassword();'");
					
					echo form_label('First Name');
					echo form_error('first');
					echo form_input('first', set_value('first'), "required");
					
					echo form_label('Last Name');
					echo form_error('last');
					echo form_input('last', set_value('last'), "required");
					
					echo form_label('Email');
					echo form_error('email');
					echo form_input('email', set_value('email'), "required");
				?>
				
				<img id="captcha" src="<?=base_url() ?>securimage/securimage_show.php" alt="CAPTCHA Image" />
				<input type="text" name="captcha_code" size="10" maxlength="6" />
				
				<?php
					$contents = '<span class="ui-icon ui-icon-refresh"></span>';
					echo anchor("#", $contents, array("title" => "Different Captcha", "class" => "refresh-captcha"));
				?>
				
				<?php
					echo form_submit('submit', 'Register');
					echo form_close();
				?>
			</div>
		</div>
	</body>

</html>


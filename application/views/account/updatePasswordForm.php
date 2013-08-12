<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Update Password | Tank Battle</title>
		<meta charset="UTF-8" />
		<link rel="icon" type="image/gif" href="<?=base_url() ?>images/tank_transparent.GIF" />
		
		<!-- Google-hosted libaries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		
		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/south-street/jquery-ui.css" />
		
		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/recover.css" />
		
		<script>
			$(function() {
				"use strict";
				
				$("input[type=submit], button").button();
				
				var err = "<?=isset($errorMsg) ? $errorMsg : '' ?>";
				if (err) {
					$("[name=oldPassword]").keypress(function() {
						$(this)[0].setCustomValidity('');
						$(".error").hide();
					})[0].setCustomValidity(err);
				}
			});
		
			function checkPassword() {
				var p1 = $("#pass1"); 
				var p2 = $("#pass2");
				
				if (p1.val() == p2.val()) {
					p1.get(0).setCustomValidity("");  // All is well, clear error message
					return true;
				}	
				else	 {
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
					<h3>Change Password</h3>
					
					<span class="result">
						<?php
							if (isset($result)) {
								echo $result;
							}
						?>
					</span>
					<span class="msg"><?=anchor("arcade/index", "Click here") ?> to go back to the lobby.</span>
				</div>
				
				<img class="big-logo" src="<?=base_url() ?>images/tank.svg" />
			</div> <!-- end logoPane -->
			
			<div class="otherPane">
				<?php 
					if (isset($errorMsg)) {
						echo "<div class='error'>" . $errorMsg . "</div>";
					}
				
					echo form_open('account/updatePassword');
					echo form_label('Current Password'); 
					echo form_error('oldPassword');
					echo form_password('oldPassword',set_value('oldPassword'),"required");
					echo form_label('New Password'); 
					echo form_error('newPassword');
					echo form_password('newPassword','',"id='pass1' required");
					echo form_label('Password Confirmation'); 
					echo form_error('passconf');
					echo form_password('passconf','',"id='pass2' required oninput='checkPassword();'");
					echo form_submit('submit', 'Change Password');
					echo form_close();
				?>	
			</div>
		</div>
	</body>
</html>


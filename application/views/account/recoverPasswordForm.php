<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
?>

<!DOCTYPE html>

<html>
	<head>
		<title>Recover Password | Tank Battle</title>
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
				$("button, input[type=submit]").button();
			});
		</script>
	</head> 
	<body>
		<?php $this->load->view("general/header"); ?>
		
		<div id="content">
			
			<!-- it's a huge pain to put this in another view, so will just copy-paste -->
			<link rel="stylesheet" href="<?=base_url() ?>css/logopane.css" />
			<div id="logoPane">
				<div class="logoMsg">
					<h3>Forgot your password?</h3>
					<span>Enter your email address to reset your password. You may need to check your spam folder or unblock noreply@tankbattle.slav</span>
				</div>
				
				<img class="big-logo" src="<?=base_url() ?>images/tank.svg" />
			</div> <!-- end logoPane -->
			
			<div class='otherPane'>
				<?php 
					if (isset($errorMsg)) {
						echo "<div class='error'>" . $errorMsg . "</div>";
					}
				
					echo form_open('account/recoverPassword');
					echo form_label('Email'); 
					echo form_error('email');
					
					$arr = array(
						"name" => "email",
						"value" => set_value ("email"),
						"required" => "required",
						"size" => "22"
					);
					
					echo form_input($arr);
					echo form_submit('submit', 'Recover Password');
					echo form_close();
				?>
			</div> <!-- end otherPane -->
		</div> <!-- end content -->
	</body>
</html>


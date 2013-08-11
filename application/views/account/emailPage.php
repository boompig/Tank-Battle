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
		<link rel="stylesheet" href="<?=base_url() ?>css/recover.css" />
		
		<script>
			$(function() {
				$("button").button();
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
					<h3>Email Sent</h3>
					<span>Check your email for your new password. You may need to check your spam folder or unblock noreply@tankbattle.slav</span>
				</div>
				
				<img class="big-logo" src="<?=base_url() ?>images/tank.svg" />
			</div> <!-- end logoPane -->
			
			<div class='otherPane'>
				<?php 
					if (isset($errorMsg)) {
						echo "<div class='error'>" . $errorMsg . "</div>";
					}
					
					$content = "<button type='button'>Tank Battle Home</button>";
					echo anchor("account/index", $content);
				?>
			</div> <!-- end otherPane -->
		</div> <!-- end content -->
	</body>
</html>


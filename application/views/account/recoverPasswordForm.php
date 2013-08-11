
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
		<link rel="stylesheet" href="<?=base_url() ?>css/header.css" />
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
			<p></p>
			<?php 
				if (isset($errorMsg)) {
					echo "<p>" . $errorMsg . "</p>";
				}
			
				echo form_open('account/recoverPassword');
				echo form_label('Email'); 
				echo form_error('email');
				echo form_input('email',set_value('email'),"required");
				echo form_submit('submit', 'Recover Password');
				echo form_close();
			?>
		</div>
	</body>

</html>


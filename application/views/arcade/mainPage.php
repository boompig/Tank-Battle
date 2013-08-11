<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
?>

<!DOCTYPE html>

<html>
	
	<head>
		<title>Lobby | Tank Battle</title>
		<meta charset="UTF-8" />
		<link rel="icon" type="image/gif" href="<?=base_url() ?>images/tank_transparent.GIF" />		

		<!-- Google-hosted libaries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		
		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/south-street/jquery-ui.css" />
		
		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/lobby.css" />
		
		<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
		<script>
			$(function(){
				
				$('#availableUsers').everyTime(500, function(){
						$('#availableUsers').load('<?= base_url() ?>arcade/getAvailableUsers');
	
						$.getJSON('<?= base_url() ?>arcade/getInvitation',function(data, text, jqZHR){
								if (data && data.invited) {
									var user=data.login;
									var time=data.time;
									if(confirm('Battle ' + user)) 
										$.getJSON('<?= base_url() ?>arcade/acceptInvitation',function(data, text, jqZHR){
											if (data && data.status == 'success')
												window.location.href = '<?= base_url() ?>combat/index'
										});
									else  
										$.post("<?= base_url() ?>arcade/declineInvitation");
								}
							});
					});
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
					<h3>Lobby</h3>
					<span>Hello, <?=$user->fullName() ?>. Are you ready for a Tank Battle?</span>
				</div>
				
				<img class="big-logo" src="<?=base_url() ?>images/tank.svg" />
			</div> <!-- end logoPane -->
			
			<div class="otherPane">
			
				<?php
					if (isset($errmsg)) {
						//TODO why would there be an error message here?
						echo "<div class='error'>$errmsg</div>";
					}
				?>
				<h2>Available Users</h2>
				<div id="availableUsers"><!-- auto-gen --></div>
			</div>
		</div> <!-- end content -->
	</body>
</html>

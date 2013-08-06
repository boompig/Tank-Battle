
<!DOCTYPE html>

<html>
	<head>
		<title>Battlefield | Tank Battle</title>
		<meta charset="UTF-8" />
		
		<!-- Google-hosted libraries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		
		<!-- local libraries -->
		<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
		
		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/sunny/jquery-ui.css" />
		
		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/battlefield.css" />
		
		<!-- custom scripts -->
		<script src="<?= base_url() ?>/js/vector.js"></script>
		<script src="<?= base_url() ?>/js/tanks.js"></script>
		
		<script>
			function clearCanvas () {
				var canvas = $("#arena")[0];
				canvas.width = canvas.width;
			}
			
			function redrawCanvas (tank) {
				clearCanvas();
				tank.draw();
			}
		
			var otherUser = "<?= $otherUser->login ?>";
			var user = "<?= $user->login ?>";
			var status = "<?= $status ?>";
			
			$(function(){
				var tank = new Tank(new Vector(100, 100), "canvas#arena");
				tank.draw();
				
				$(document).keypress (function(e) {
					var c = String.fromCharCode(e.which).toLowerCase();
					
					if (c === 'd') {
						tank.move ("right");
						redrawCanvas(tank);
					} else if  (c === 's') {
						tank.move("down");
						redrawCanvas(tank);
					} else if (c === "a") {
						tank.move("left");
						redrawCanvas(tank);
					} else if (c === "w") {
						tank.move("up");
						redrawCanvas(tank);
					}
				});
				
				
				// poll server for new messages every 2 seconds
				$('body').everyTime(2000, function(){
						if (status == 'waiting') {
							$.getJSON('<?= base_url() ?>arcade/checkInvitation',function(data, text, jqZHR){
									if (data && data.status=='rejected') {
										alert("Sorry, your invitation to battle was declined!");
										window.location.href = '<?= base_url() ?>arcade/index';
									}
									if (data && data.status=='accepted') {
										status = 'battling';
										$('#status').html('Battling ' + otherUser);
									}
									
							});
						}
						var url = "<?= base_url() ?>combat/getMsg";
						$.getJSON(url, function (data,text,jqXHR){
							if (data && data.status=='success') {
								var conversation = $('[name=conversation]').val();
								var msg = data.message;
								if (msg.length > 0)
									$('[name=conversation]').val(conversation + "\n" + otherUser + ": " + msg);
							}
						});
				});
	
				// when send button is pressed
				$('form').submit(function(){
					var arguments = $(this).serialize();
					var url = "<?= base_url() ?>combat/postMsg";
					$.post(url,arguments, function (data, textStatus, jqXHR){
						var conversation = $('[name=conversation]').val();
						var msg = $('[name=msg]').val();
						
						// add most recent submission to conversation
						$('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
						
						// blank input field
						$("[name=msg]").val("");
					});
					
					// since everything submitted using AJAX, don't actually submit the form
					return false;
				});	
			});
		
		</script>
	</head> 
	<body>  
		<header>
			<h1>Battle Field</h1>
			
			<div id="greeting">
				Hello <?= $user->fullName() ?>  <?= anchor('account/logout','(Logout)') ?>  <?= anchor('account/updatePasswordForm','(Change Password)') ?>
			</div>
		</header>
		
		<canvas id="arena" width="600" height="300">
			
		</canvas>	
		
		<div id="chatContainer">
			<div id='status'>
				<?php 
					if ($status == "battling")
						echo "Battling " . $otherUser->login;
					else
						echo "Wating on " . $otherUser->login;
				?>
			</div>
			
			<?php 
				$attrs = array("name" => "conversation", "readonly" => "readonly");
				echo form_textarea($attrs);
				echo form_open();
				echo form_input('msg');
				echo form_submit('Send','Send');
				echo form_close();
				
			?>
		</div>
	</body>
</html>
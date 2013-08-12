<?php
header('Cache-Control: no-cache, no-store, must-revalidate'); // HTTP 1.1.
header('Pragma: no-cache'); // HTTP 1.0.
header('Expires: 0'); // Proxies.
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Battle! | Tank Battle</title>
        <meta charset="UTF-8" />
        <link rel="icon" type="image/gif" href="<?=base_url() ?>images/tank_transparent.GIF" />

		<!-- Google-hosted libaries -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

		<!-- JQuery UI CSS -->
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/south-street/jquery-ui.css" />

		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/style.css" />
		<link rel="stylesheet" href="<?=base_url() ?>css/battlefield.css" />

        <!-- local scripts -->
        <script src="<?=base_url() ?>js/battle/utils.js"></script>
        <script src="<?=base_url() ?>js/battle/vector.js"></script>
        <script src="<?=base_url() ?>js/battle/shot.js"></script>
        <script src="<?=base_url() ?>js/battle/tanks.js"></script>
        <script src="<?=base_url() ?>js/battle/game.js"></script>
        <script src="<?=base_url() ?>js/battle/battlefield.js"></script>

        <script>
        	function endBattle () {
        		window.location.href = "<?=base_url() ?>combat/leaveBattle";
        		console.log("zoom");
        	}
        
			$(function() {
				"use strict";
				
				$("#backButton").click(function() {
					endBattle();
				})
				
				$("button, input[type=submit]").button();
				
				Arena.serverURL = "<?=base_url() ?>";
				Arena.serverPushPull();
			});
        </script>
    </head>
    <body>
    	<?php $this -> load -> view ("general/header"); ?>
    	
    	<div id="content">
	    	<?php //$this -> load -> view ("battle/arena"); ?>
	    	
	    	<div id="noPlayer2">
	    		Please wait while the other player connects<span id="dots"></span>
	    	</div>
	    	
	    	<div id="gamePanel" style="display: none;">
		    	<div id="instructions">
		    		<h3>Instructions</h3>
		    		You control the blue tank while your opponent controls the green tank.
		    		<h4>Controls</h4>
		    		<span id="controls">Use WASD or the arrow keys to move the tank. Aim your turret with the mouse. Click or press space bar to fire.</span>
		    		
		    		<h4>Objective</h4>
		    		<span id="objective">Shoot the other tank before it shoots you. Avoid bullets.</span>
		    	</div>
		    	
		    	<canvas id="arena" height="400" width="600"></canvas>
		        <!-- <button type="button" onlick="Arena.stop">Stop</button> -->
		        
		        <div id="results" style="display: none;">
		        	<span class="banner">Game Over.&nbsp;</span>
		        	<span class="msg"><!-- results go here --></span>
		        	<button type="button" id="backButton">Return to Lobby</button>
		        </div>
	        </div>
     </div> <!-- end content -->
    </body>
</html>

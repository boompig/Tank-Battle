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
			$(function() {
				"use strict";
				
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
	    	
	    	<canvas id="arena" height="400" width="600" style="display: none;"></canvas>
	        <!-- <button type="button" onlick="Arena.stop">Stop</button> -->
	        
	        <div id="results" style="display: none;">
	        	<span>The winner is&nbsp;<span id="winner">Player 5</span></span>
	        </div>
     </div> <!-- end content -->
    </body>
</html>

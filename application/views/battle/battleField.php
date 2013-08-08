<!DOCTYPE html>

<html>
    <head>
        <title>Battle! | Tank Battle</title>
        <meta charset="UTF-8" />

		<!-- custom styling -->
		<link rel="stylesheet" href="<?=base_url() ?>css/battlefield.css" />

        <!-- local JQuery -->
        <script src="<?=base_url() ?>js/lib/jquery-1.9.1.js"></script>

        <!-- local scripts -->
        <script src="<?=base_url() ?>js/battle/utils.js"></script>
        <script src="<?=base_url() ?>js/battle/vector.js"></script>
        <script src="<?=base_url() ?>js/battle/shot.js"></script>
        <script src="<?=base_url() ?>js/battle/tanks.js"></script>
        <script src="<?=base_url() ?>js/battle/game.js"></script>

        <script>
			$(function() {
				"use strict";
				
				var target = null;
				
				// create the game
				var game = new Game("#arena");
				game.redraw();
				
				
	        	function redraw () {
	        		var result = game.redraw();
	        		
	        		if (target) {
	        			game.shoot(0);
	        		}
	        		
	        		if (result) {
	        			// don't redraw on game over
	        			setTimeout(redraw, 30);
	        		} else {
	        			var winner = game.getWinner() + 1;
	        			$("#results").show().find("#winner").text("Player " + winner);
	        		}
	        	}
				
				// trigger a redraw every 1/5 second
				setTimeout(redraw, 100);
				
				$("#playAgainButton").click(function() {
					// delete(game)
					game = new Game("#arena");
					game.redraw();
					
					// TODO this does not work at all because we are breaking all the references to game
				});
				
				$("#encodeButton").click(function() {
					console.log(game.encode());
				});
				
				$("#submitButton").click(function() {
					game.pushServer();
				});
				
				// create bindings
				$(document).keypress (function(e) {
					var c = String.fromCharCode(e.which).toLowerCase();
					
					// bindings for tank 0
					if (c === 'w') {
						// up
						game.moveTankUp(0);
					} else if (c === 'd') {
						// right
						game.moveTankRight(0);
					} else if (c === 's') {
						// down
						game.moveTankDown(0);
					} else if (c === 'a') {
						// left
						game.moveTankLeft(0);
					} else if (c === ' ') {
						game.shoot(1);
					}
					
					// bindings for tank 1
					if (c === 'j') {
						game.moveTankLeft(1);
					} else if (c === 'l') {
						game.moveTankRight(1);
					} else if (c === 'k') {
						game.moveTankDown(1);
					} else if (c === 'i') {
						game.moveTankUp(1);
					}
					
					if ($.inArray(e.keyCode, [37, 38, 39, 40])) {
						e.preventDefault();
					} 
					
					if (e.keyCode === 37) {
						// left

						game.rotateTurret(1, 1 / 10 * Math.PI);
					} else if (e.keyCode === 38) {
						// up
						game.rotateTurret(1, 1 / 10 * Math.PI);
					} else if (e.keyCode === 39) {
						// right
						game.rotateTurret(1, -1 / 10 * Math.PI);
					}if (e.keyCode === 40) {
						// down
						game.rotateTurret(1, -1 / 10 * Math.PI);
					}
				});
				
				$("#arena").mousemove (function (e) {
					var coords = Utils.toCanvasCoords (e);
					game.turnTurretTo(coords);
				});
				
				$("#arena").mousedown(function (e) {
					target = Utils.toCanvasCoords (e);
					game.shoot(0);
				});
				
				$("#arena").mouseup(function (e) {
					target = null;
				});
			});
        </script>
    </head>
    <body>
    	<header>
    		<h1>Battle!</h1>
    	</header>
    	
    	<div id="content">
        	<canvas id="arena" height="300" width="500"></canvas>
        </div>
        
        <div id="results" style="display: none;">
        	<span>The winner is&nbsp;<span id="winner">Player 5</span></span>
        	
        	<button type="button" id="playAgainButton">Play Again</button>
        </div>
        
        <footer>
        	<button type="button" id="encodeButton">Encode</button>
        	<button type="button" id="submitButton">Submit</button>
        </footer>
    </body>
</html>

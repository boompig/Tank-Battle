<script>
	$(function() {
		"use strict";
		
		var target = null;
		var keys = [];
		
		// create the game
		var game = new Game("#arena");
		game.redraw();
		
		/**
		 * Keep querying server until the game is over.
		 */
		function queryServer () {
			var serverURL = "<?=base_url() ?>";
			
			game.pushServer(serverURL);
			game.pullServer(serverURL);
			
			setTimeout(queryServer, 50);
		}
		
		/**
		 * Keep redrawing until the game is over.
		 */
    	function redraw () {
    		var result = game.redraw();
    		
    		// for persistant mouse down
    		if (target) {
    			game.shoot(0);
    		}
    		
    		if (result) {
    			// don't redraw on game over
    			setTimeout(redraw, 50);
    		} else {
    			var winner = game.getWinner() + 1;
    			$("#results").show().find("#winner").text("Player " + winner);
    		}
    	}
		
		// do this before redraw to get initial info
		queryServer();
		
		// trigger a redraw every so often
		setTimeout(redraw, 100);
		
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

<canvas id="arena" height="300" width="500"></canvas>
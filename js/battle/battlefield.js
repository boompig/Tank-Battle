/**
 * Used while waiting for the other user to connect
 */

function Arena () {
	
}

/**
 * Possible states:
 * 		waiting for another user to connect
 * 		playing the tank battle
 * 		the tank battle is over
 */
Arena.STATES = {
	"waiting" : 1,
	"playing" : 2,
	"done" : 3
};

Arena.CONNECT_INTERVAL = 500;
Arena.GAME_INTERVAL = 500;
Arena.REFRESH_INTERVAL = 50;

/**
 * For mouse down purposes
 */
Arena.target = null;

/**
 * Filled in from calling view
 */
Arena.serverURL = null;

Arena.pulling = false;

/*
 * Number of dots to start with.
 */
Arena.numDots = 0;
Arena.maxNumDots = 3;

/**
 * initial state is waiting
 */
Arena.state = Arena.STATES.waiting;

Arena.serverPushPull = function () {
	"use strict";
	
	switch (Arena.state) {
		case Arena.STATES.waiting:
			Arena.checkInviteStatus();
			break;
		case Arena.STATES.playing:
			Arena.updateGameServer();
			break;
		case Arena.STATES.done:
			break;
	}
};

/**
 * Check whether the invite has an answer
 */
Arena.checkInviteStatus = function () {
	"use strict";
	
	$.getJSON (Arena.serverURL + "arcade/checkInvitation", function(data, text, jqZHR) {
		if (data && data.status && (data.status !== 'pending')) {
			if (data.status === 'accepted') {
				// the other player has connected
				Arena.state = Arena.STATES.playing;
				
				$("#noPlayer2").hide();
				Arena.game = new Game($("#arena"));
				
				Arena.serverPushPull();
			} else if (data.status === 'rejected') {
				// TODO redirect back to main page and indicate invitation rejected
			}
		} else {
			// no communication with server or incorrect response or pending status
			Arena.drawDots ();
			setTimeout(Arena.serverPushPull, Arena.CONNECT_INTERVAL);
		}
	});
};

/**
 * Redraw quickly.
 */
Arena.redraw = function () {
	"use strict";
	
	if (Arena.target) {
		Arena.game.shoot(0);
	}
	
	Arena.game.redraw();
	
	if (! Arena.game.isGameOver() && Arena.state === Arena.STATES.playing) {
		setTimeout(Arena.redraw, Arena.REFRESH_INTERVAL);
	}
};

/**
 * Attach keyboard and mouse events to the canvas.
 */
Arena.attachEvents = function () {
	// create keyboard bindings
	$(document).keypress (function(e) {
		var c = String.fromCharCode(e.which).toLowerCase();
		
		// prevent scrolling with arrow keys
		if ($.inArray(e.keyCode, [37, 38, 39, 40])) {
			e.preventDefault();
		} 
		
		// bindings for tank 0
		if (c === 'w' || e.keyCode === 38) {
			// up
			Arena.game.moveTankUp(0);
		} else if (c === 'd' || e.keyCode === 39) {
			// right
			Arena.game.moveTankRight(0);
		} else if (c === 's' || e.keyCode === 40) {
			// down
			Arena.game.moveTankDown(0);
		} else if (c === 'a' || e.keyCode === 37) {
			// left
			Arena.game.moveTankLeft(0);
		} else if (c === ' ') {
			Arena.game.shoot(0);
		}
	});
	
	$("#arena").mousemove (function (e) {
		var coords = Utils.toCanvasCoords (e);
		Arena.game.turnTurretTo(coords);
	});
	
	$("#arena").mousedown(function (e) {
		Arena.target = Utils.toCanvasCoords (e);
		Arena.game.shoot(0);
	});
	
	$("#arena").mouseup(function (e) {
		Arena.target = null;
	});
};

Arena.stop = function () {
	Arena.state = Arena.STATES.done;
};

/**
 * Update remote server with status of game
 * Update game with status of server.
 * Push (own info) before pull (other info)
 */
Arena.updateGameServer = function () {
	var url;
	
	if (Arena.state === Arena.STATES.done || Arena.game.isGameOver()) {
		// stop
		return;
	}
	
	if (Arena.pulling) {
		setTimeout(Arena.serverPushPull, 50);
		return;
	}
	
	if (Arena.game.hasInitPos()) {
		var obj = Arena.game.encode();
		url = Arena.serverURL + "combat/postBattle";
		
		// this is the push
		$.post (url, obj, function (data, textStatus, jqXHR) {
			// now can trigger pull
			Arena.pulling = true;
			url = Arena.serverURL + "combat/getBattle";
			
			$.getJSON (url, function (data, textStatus, jqXHR) {
				if (data) {
					Arena.game.updateOtherTank (data.x, data.y, data.angle);
					
					if (data.status !== 'active') {
						Arena.state = Arena.STATES.done;
					}
				}
				
				Arena.pulling = false;
			}).fail(function () {
				Arena.pulling = false;
			});
			
			return false;
		});
		
		// update the server again in a bit
		setTimeout(Arena.serverPushPull, Arena.GAME_INTERVAL);
	} else {
		Arena.pulling = true;
		url = Arena.serverURL + "combat/getBattle";
		
		// first pull
		$.getJSON (url, function (data, textStatus, jqXHR) {
			console.log("first pull data:");
			console.log(data);
			
			if (data) {
				Arena.game.updateOtherTank (data.x, data.y, data.angle);
				Arena.game.updateOwnTank (data.your_x, data.your_y, data.your_angle);
				
				if (data.status !== 'active') {
					Arena.state = Arena.STATES.done;
				}
				
				$("#arena").show();
				Arena.attachEvents();
				Arena.redraw();
			}
			
			Arena.pulling = false;
			setTimeout(Arena.serverPushPull, Arena.GAME_INTERVAL);
		}).fail(function () {
			console.log("first pull failed");
			Arena.pulling = false;
		});
	}
};

/**
 * Check battle status. Done so know when game has ended.
 * @deprecated
 */
Arena.checkBattleStatus = function () {
	"use strict";
	
	$.getJSON (Arena.serverURL + "combat/checkBattleStatus", function (data, text, jqZHR) {
		if (data && status && (data.status !== 'active')) {
			Arena.state = Arena.STATES.done;
			
			// TODO do some post-processing here
		} else {
			setTimeout(Arena.serverPushPull, Arena.GAME_INTERVAL);
		}
	});
};

/**
 * Add numDots number of dots to html of selector. Wipe other HTML in selector clean
 */
Arena.drawDots = function () {
	"use strict";
	
	var arr = [];
	
	for (var i = 0; i < Arena.numDots; i++) {
		arr.push(".");
	}
	
	$("#dots").html(arr.join(""));
	
	if (Arena.numDots === Arena.maxNumDots) {
		Arena.numDots = 0;
	} else {
		Arena.numDots += 1;
	}
};

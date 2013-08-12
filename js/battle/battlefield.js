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
			setTimeout(Arena.serverPushPull, 1000);
		}
	});
};

/**
 * Redraw quickly.
 */
Arena.redraw = function () {
	"use strict";
	
	Arena.game.redraw();
	
	if (! Arena.game.isGameOver() && Arena.state === Arena.STATES.playing) {
		setTimeout(Arena.redraw, 50);
	}
};

Arena.startGame = function () {
	"use strict";
	
	Arena.game = new Game($("#arena"));
	
	var doGame = function () {
		$("#arena").show();
		console.log("redraw cycle started");
		Arena.redraw();
		
		// start push-pull cycle
		setTimeout(Arena.updateGameServer, 500);
	};
	
	// get initial position of tanks. then show canvas
	Arena.game.pullServer(Arena.serverURL, true, doGame);
};

/**
 * Update remote server with status of game
 * Update game with status of server.
 * Push (own info) before pull (other info)
 */
Arena.updateGameServer = function () {
	var url;
	
	if (Arena.game.hasInitPos()) {
		// have Arena.pulling to avoid conflict of push & pull
		if (! Arena.game.isGameOver() && Arena.state === Arena.STATES.playing && !Arena.pulling) {
			var obj = Arena.game.encode();
			url = Arena.serverURL + "combat/postBattle";
			
			// this is the bush
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
			setTimeout(Arena.updateGameServer, 500);
		}
	} else {
		if (! Arena.pulling) {
			Arena.pulling = true;
			url = Arena.serverURL + "combat/getBattle";
			console.log(url);
			
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
					Arena.redraw();
				}
				
				Arena.pulling = false;
			}).fail(function () {
				console.log("first pull failed");
				Arena.pulling = false;
			});
		}
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
			setTimeout(Arena.serverPushPull, 500);
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

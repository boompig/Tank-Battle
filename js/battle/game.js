/**
 * Contains details about the game
 */

/**
 * Create a new game with the given canvas.
 */
function Game (canvasSelector) {
	"use strict";
	
	/**
	 * Contains JQuery canvas obj.
	 */
	this.canvas = $(canvasSelector);
	
	/**
	 * Contains canvas 2D context
	 */
	this.context = $(this.canvas)[0].getContext("2d");
	
	// based on canvas size
	this.width = $(this.canvas).attr("width");
	this.height = $(this.canvas).attr("height");
	
	/**
	 * Permanently set at 2
	 */
	this.numPlayers = 2;
	
	// create tanks
	this.tanks = Array(this.numPlayers);
	
	for (var i = 0; i < this.numPlayers; i++) {
		this.tanks[i] = new Tank(null, null, i);
	}
	
	/**
	 * @returns {Array}
	 */
	this.shots = new Array();
}

/***************************	INITIAL SETTINGS	************************************/

Game.SPAWN_OFFSET = new Vector(20, 20);
Game.MIN_MOVE = 4;
Game.SHOT_RADIUS = 10;

/***************************************************************************************/

/***************************	SHOT STUFF	************************************/

/**
 * Make the turret face the given target
 */
Game.prototype.turnTurretTo = function (target) {
	"use strict";
	
	this.tanks[0].setTurretTarget(target);
};

/**
 * Rotate the turret by the given angle.
 * @param {Number} player
 * @param {Number} angle
 */
Game.prototype.rotateTurret = function (player, angle) {
	"use strict";
	
	this.tanks[player].setTurretAngle(this.tanks[player].turretAngle + angle);
};

/**
 * Shoot.
 */
Game.prototype.shoot = function (player) {
	"use strict";
	
	var shot = this.tanks[player].shoot();
	
	if (shot) {
		this.shots.push(shot);
	}
};

/*******************************************************************************/

/****************************************	SPAWN STUFF	************************/

Game.prototype.getSpawnOffset = function () {
	"use strict";
	
	var t = Math.min(100, this.width / 4);
	// TODO for now
	return new Vector(t, t);
};

/**
 * Return the spawn point of the given player.
 */
Game.prototype.getPlayerSpawn = function (playerIndex) {
	"use strict";
	
	if (playerIndex === 0) {
		return this.getSpawnOffset();
	} else if (playerIndex === 1) {
		var farCorner = new Vector(this.width, this.height);
		return farCorner.sub(this.getSpawnOffset());
	} else {
		// if > 2 players, start in random places
		var x = Math.random() * this.width;
		var y = Math.random() * this.height;
		return new Vector(x, y);
	}
};

/**
 * Return a direction for the given player to face.
 */
Game.prototype.getPlayerFacing = function (playerIndex) {
	"use strict";
	
	if (playerIndex === 0) {
		return "right";
	} else if (playerIndex === 1) {
		return "left";
	} else {
		// if > 2 players, face random directions
		// i dunno
	}
};

Game.prototype.hasInitPos = function () {
	return ! isNaN(this.tanks[0].x);
};

/*******************************************************************************/

/**
 * Move the player's tank by the given amount.
 * Redraw.
 */
Game.prototype.moveTank = function (dx, dy, playerIndex) {
	// player's tank is always index 0
	this.tanks[playerIndex].move (new Vector (dx, dy));
	// this.redraw();
};

Game.prototype.moveTankUp = function (playerIndex) {
	this.moveTank (0, -1 * Game.MIN_MOVE, playerIndex);
};

Game.prototype.moveTankDown = function (playerIndex) {
	this.moveTank (0, Game.MIN_MOVE, playerIndex);
};

Game.prototype.moveTankRight = function (playerIndex) {
	this.moveTank (Game.MIN_MOVE, 0, playerIndex);
};

Game.prototype.moveTankLeft = function (playerIndex) {
	this.moveTank (-1 * Game.MIN_MOVE, 0, playerIndex);
};

Game.prototype.isOnscreen = function (item) {
	return item.pos.x >= 0 && item.pos.y >= 0 && item.pos.x <= this.width && item.pos.y <= this.height;
}

/**
 * Return true if successful, return false on game over.
 */
Game.prototype.redraw = function () {
	var over = false;
	
	Utils.clearCanvas(this.canvas);
	
	// drawn first as want shots over tank
	for (var i = 0; i < this.numPlayers; i++) {
		this.tanks[i].draw(this.context);
	}
	
	var normalShot;
	
	for (var i = 0; i < this.shots.length;) {
		if (! this.shots[i]) {
			console.log("fail");
			console.log(this.shots);
			continue;
		}
		
		normalShot = true;
		
		if (this.isOnscreen(this.shots[i])) {
			for (var p = 0; p < this.numPlayers; p++) {
				if (this.tanks[p].isHit(this.shots[i]) && this.shots[i].src != p) {
					this.shots[i].draw(this.context, true);
				
					this.tanks[p].processHit();
					this.shots.splice(i, 1);
					normalShot = false;
					
					if (this.tanks[p].isDead()) {
						over = true;
					}
					
					break; // bullet can only hit 1 tank
				}
			}
			
			if (normalShot) {
				this.shots[i].draw(this.context, false);
				
				// add velocity * speed
				var shot_vel = 5;
				var vec = Vector.fromRadiusTheta(shot_vel, this.shots[i].angle);
				this.shots[i].move(vec);
				
				i++;
			}
		} else {
			// remove shot, since no longer on screen. Also, don't draw it
			this.shots.splice(i, 1);
		}
	}
	
	// draw hit points for the tank (done after for layering purposes)
	for (var i = 0; i < this.numPlayers; i++) {
		this.tanks[i].drawHP(this.context);
	}
	
	return ! over;
};

/**
 * Return the winner of the game. Return null if no winner yet.
 * @returns {Number}
 */
Game.prototype.getWinner = function () {
	if (this.tanks[0].isDead()) {
		return 1;
	} else if (this.tanks[1].isDead()) {
		return 0;
	} else {
		return null;
	}
};

/**
 * Return true iff the game is over.
 * @returns {Boolean}
 */
Game.prototype.isGameOver = function () {
	return this.tanks[0].isDead() || this.tanks[1].isDead();
};

/**
 * Return a status string to denote game status
 * Possible values:
 * 		active	-	game ongoing
 * 		win		-	this player won
 * 		loss	-	other player won
 * 		draw	-	both players lost simultaneously
 */
Game.prototype.getStatus = function () {
	if (this.tanks[0].isDead() && this.tanks[1].isDead()) {
		return 'draw';
	} else if (this.tanks[0].isDead()) {
		return 'loss';
	} else if (this.tanks[1].isDead()) {
		return 'win';
	} else {
		return 'active';
	}
};

/**
 * Return JSON for the current state of the game.
 * @returns {Object}
 */
Game.prototype.encode = function () {
	// TODO for now make sure that we have the following here:
	// x1, y1 of this tank
	
	"use strict";
	
	var json = {
		"x" : this.tanks[0].pos.x,
		"y" : this.tanks[0].pos.y,
		"angle" : Math.round(Utils.RadToDeg(this.tanks[0].turretAngle)), // has to be an integer
		"status" : this.getStatus()
	};
	
	return json;
};

Game.prototype.updateOwnTank = function (x, y, angle) {
	if (! this.tanks[0].pos)
		this.tanks[0].pos = new Vector(0, 0);
	
	this.tanks[0].pos.x = Number(x);
	this.tanks[0].pos.y = Number(y);
	this.tanks[0].turretAngle = Utils.DegToRad (Number(angle));
};

Game.prototype.updateOtherTank = function (x, y, angle) {
	if (! this.tanks[1].pos)
		this.tanks[1].pos = new Vector(0, 0);
	
	this.tanks[1].pos.x = Number(x);
	this.tanks[1].pos.y = Number(y);
	this.tanks[1].turretAngle = Utils.DegToRad (Number(angle));
};

/**
 * Pull game info from the server.
 * f is usually undefined, but can be a callback function to call on success
 */
Game.prototype.pullServer = function (serverURL, first, f) {
	"use strict";
	
	var url = serverURL + "combat/getBattle";
	var that = this;
	
	if (this.tanks[0].x === null) {
		first = true;
	}
	
	$.getJSON (url, function (data, textStatus, jqXHR) {
		"use strict";
		
		if (data && data.status === 'success') {
			console.log(data);
			
			that.tanks[1].pos.x = Number(data.x);
			that.tanks[1].pos.y = Number(data.y);
			that.tanks[1].turretAngle = Utils.DegToRad (Number(data.angle));
			
			if (first) {
				that.tanks[0].pos.x = Number(data.your_x);
				that.tanks[0].pos.y = Number(data.your_y);
				that.tanks[0].turretAngle = Utils.DegToRad (Number(data.your_angle));
			}
			
			if (f) {
				f ();
			}
		}
	}).fail(function (data, textStatus) {
		// console.log(url);
		// someone else has the lock?
		function f2 () {
			console.log(serverURL);
			console.log(first);
			console.log(f);
			that.pullServer(serverURL, first, f);
		}
		
		// setTimeout(f2, 1000);
		
		// console.log("failed?");
		// console.log(data);
		// console.log(textStatus);
	});
};

/**
 * Push everything we know about the state of the game to the server
 */
Game.prototype.pushServer = function (serverURL) {
	"use strict";
	
	var obj = this.encode();
	var url = serverURL + "combat/postBattle";
	
	$.post(url, obj, function (data, textStatus, jqXHR) {
		return false;
	});
};

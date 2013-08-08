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
	
	
	this.numPlayers = 2;
	// create tanks
	this.tanks = Array(this.numPlayers);
	
	for (var i = 0; i < this.numPlayers; i++) {
		this.tanks[i] = new Tank(this.getPlayerSpawn(i), this.getPlayerFacing(i), i);
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
					
					if (this.tanks[p].isDead()) {
						over = true;
					}
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
 * Return JSON for the current state of the game.
 * @returns {Object}
 */
Game.prototype.encode = function () {
	var json = {
		"me" : this.tanks[0],
		"you" : this.tanks[1],
		"shots" : this.shots,
		"over" : this.isGameOver()
	};
	
	return json;
};

/**
 * Push everything we know about the state of the game to the server
 */
Game.prototype.pushServer = function () {
	var obj = this.encode();
	var url = 
	
	
	$.post();
};

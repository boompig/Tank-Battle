/**
 * A single shot.
 * 
 * Create with an initial position and initial velocity.
 * 
 * Velocity refers to direction of travel, normallized.
 * 
 * @param {Vector} initPos initial position
 * @param {Number} angle initial velocity angle
 * @param {Number} player The player who fired the shot
 */
function Shot (initPos, angle, player) {
	"use strict";
	
	this.pos = initPos;
	this.angle = angle;
	this.src = player;
	this.id = Game.SHOT_COUNT;
}

Shot.RADIUS = 2;

/**
 * Move the shot by the given amount.
 * @param {Vector} amt
 */
Shot.prototype.move = function (amt) {
	this.pos = this.pos.add(amt);
};

Shot.prototype.encode = function () {
	var o = {};
	
	for (prop in this) {
		if (this.hasOwnProperty(prop)) {
			if (prop == 'pos') {
				o[prop] = {'x' : this[prop].x, 'y' : this[prop].y};
			} else {
				o[prop] = this[prop];
			}
		}
	}
	
	return o;
};

/**
 * Draw the shot on the screen
 * @param {Object} context
 * @param {Boolean} hit
 */
Shot.prototype.draw = function (context, hit) {
	"use strict";

	context.save();
	
	if (hit) {
		context.fillStyle = "#FF0000";
	} else {
		context.fillStyle = "#000000";	
	}
	
	context.strokeStyle = "#000000";
	
	context.beginPath();
	context.arc(this.pos.x, this.pos.y, Shot.RADIUS, 2 * Math.PI, false);
	context.closePath();
	
	context.stroke();
	context.fill();
	context.restore();
};

/**
 * This file governs behaviour for Tanks
 * Requires Vector class
 */

/**
 * Create new tank object.
 * 
 * @param {Vector} spawn Spawn point
 * @param {Vector} facing Initial direction this tank is facing. Should be one of ["up", "down", "left", "right"]
 */
function Tank (spawn, facing, index) {
	"use strict";
	
	/**
	 * Position of this tank
	 * @returns {Vector}
	 */
	this.pos = spawn;
	
	/**
	 * @returns {Vector}
	 */
	this.facing = facing;
	
	this.index = index;
	
	/**
	 * Time of last shot.
	 * First shot set very far back in time
	 * @returns {Date}
	 */
	this.lastShot = new Date(1991, 10, 1);
	
	/**
	 * @returns {Number}
	 */
	this.turretAngle = 0;
	
	this.hp = Tank.MAX_HP;
}

/****************	CONSTANTS	****************/

Tank.DIRECTIONS = {
	"up" : new Vector (0, -1),
	"down" : new Vector (0, 1),
	"right" : new Vector (1, 0),
	"left" : new Vector (-1, 0)
};

Tank.BODY_COLORS = ["#1BA5E0", "#00FF00"];

Tank.TURRET_COLORS = ["#0000FF", "#00AA00"];

/**
 * @returns {Number}
 */
Tank.WIDTH = 40;

/**
 * @returns {Number}
 */
Tank.HEIGHT = 20;

Tank.TURRET_LENGTH = 40;
Tank.TURRET_HEIGHT = 7;

/**
 * Position of turret relative to the tank.
 */
Tank.TURRET_REL = new Vector(Tank.WIDTH / 2, Tank.HEIGHT / 2);

/**
 * How frequently the tank can shoot, in MS
 * @returns {Number}
 */
Tank.MAX_HP = 10;

/**
 * Governs how often a shot can be fired.
 */
Tank.SHOT_FREQ = 300;

/***********************************************/

/**
 * Return the shot object iff a shot is successful.
 * If a shot goes off, reset the shot clock.
 * Return false on failure.
 */
Tank.prototype.shoot = function () {
	"use strict";
	
	if (new Date() - this.lastShot > Tank.SHOT_FREQ) {
		this.lastShot = new Date();
		
		// get position of end of barrell
		return new Shot(this.getShotInitPos(), this.turretAngle, this.index);
	} else {
		return false;
	}
};

/**
 * Return normallized vector for turret position.
 */
Tank.prototype.getShotInitPos = function () {
	"use strict";
	
	var r = Tank.TURRET_LENGTH;
	var theta = this.turretAngle;
	
	return this.getTurretBasePos().add(Vector.fromRadiusTheta (r, theta));
};

/**
 * Return body color for this tank as hex.
 */
Tank.prototype.bodyColour = function () {
	"use strict";
	
	return Tank.BODY_COLORS [this.index];
};

Tank.prototype.turretColour = function () {
	"use strict";
	
	return Tank.TURRET_COLORS [this.index];
};

Tank.prototype.drawBody = function (context) {
	"use strict";

	context.save();
	context.fillStyle = this.bodyColour();
	context.strokeStyle = "#000000";
	
	context.beginPath();
	context.rect(this.pos.x, this.pos.y, Tank.WIDTH, Tank.HEIGHT);
	context.closePath();
	
	context.stroke();
	context.fill();
	context.restore();
};

Tank.prototype.drawTurret = function (context) {
	"use strict";
	
	var turretPos = this.getTurretBasePos();

	context.save();
	
	// rotate canvas about base of turret
	// rotation point is base of turret
	context.translate(turretPos.x, turretPos.y);
	context.rotate(this.turretAngle);
	
	context.fillStyle = this.turretColour();
	context.strokeStyle = "#000000";
	
	context.beginPath();
	
	context.rect(-1 / 8 * Tank.TURRET_LENGTH, -1 * Tank.TURRET_HEIGHT / 2, Tank.TURRET_LENGTH, Tank.TURRET_HEIGHT);
	context.closePath();
	
	context.stroke();
	context.fill();
	context.restore();
};

/**
 * Move the tank by the given amount
 * @param {Vector} moveVector
 */
Tank.prototype.move = function (moveVector) {
	this.pos = this.pos.add(moveVector);
};


/**
 * Draw the tank on the canvas.
 */
Tank.prototype.draw = function (context) {
	"use strict";
	
	this.drawBody(context);
	this.drawTurret(context);
};

/**
 * Return position of the base of the turret
 * @returns {Vector}
 */
Tank.prototype.getTurretBasePos = function () {
	"use strict";
	
	// this is the exact middle of the tank
	return this.pos.add(Tank.TURRET_REL);
};

/**
 * Set the turret angle to the given angle.
 * @param {Number} angle
 */
Tank.prototype.setTurretAngle = function (angle) {
	"use strict";
	
	this.turretAngle = angle;
};

/**
 * 
 * @param {Vector} targetPos
 */
Tank.prototype.setTurretTarget = function (targetPos) {
	"use strict";
	
	var a = this.getTurretBasePos();
	var b = targetPos;
	
	var v = b.sub(a);
	var angle = v.angle();
	
	if (angle !== null) {
		this.setTurretAngle(angle);
	}
};

/**
 * Return true iff this tank is hit by the given shot.
 * @param {Shot} shot
 */
Tank.prototype.isHit = function (shot) {
	"use strict";
	
	return shot.pos.x + Shot.RADIUS >= this.pos.x && shot.pos.y + Shot.RADIUS >= this.pos.y && shot.pos.x - Shot.RADIUS <= this.pos.x + Tank.WIDTH && shot.pos.y - Shot.RADIUS <= this.pos.y + Tank.HEIGHT;
};

/**
 * Process getting hit by a shot.
 * Don't allow going into negatives
 */
Tank.prototype.processHit = function () {
	if (this.hp > 0)
		this.hp -= 1;
};

Tank.prototype.drawHP = function (context) {
	// offset for HP should be 10 above tank
	// width can be say... 5 per hitpoint
	
	"use strict";

	var HP_WIDTH = 5;
	var HP_HEIGHT = 10;
	var HP_Y_OFFSET = 10;

	context.save();
	
	
	var start = this.getTurretBasePos().x - Tank.MAX_HP * HP_WIDTH / 2;

	// green
	context.fillStyle = "#00FF00";
	context.beginPath();
	context.rect(start, this.pos.y - HP_HEIGHT - HP_Y_OFFSET, this.hp * HP_WIDTH, HP_HEIGHT);
	context.closePath();
	context.fill();
	
	// red
	context.fillStyle = "#FF0000";
	context.beginPath();
	context.rect(start + this.hp * HP_WIDTH, this.pos.y - HP_HEIGHT - HP_Y_OFFSET, (Tank.MAX_HP - this.hp) * HP_WIDTH, HP_HEIGHT);
	context.closePath();
	context.fill();
	
	context.restore();
};

/**
 * Return true iff this tank is dead
 * @returns {Boolean}
 */
Tank.prototype.isDead = function () {
	return this.hp === 0;
};

Tank.prototype.encode = function () {
	var o = {};
	
	for (prop in this) {
		if (this.hasOwnProperty (prop)) {
			o[prop] = this[prop];
		}
	}
	
	return o;
};

/**
 * This file governs behaviour for Tanks
 * Requires Vector class
 */

/**
 * Create new tank object.
 * 
 * @param {Vector} spawn Spawn point
 * @param {String} The selector for the canvas.
 */
function Tank (spawn, canvas, index) {
	"use strict";
	
	/**
	 * @returns {Vector}
	 */
	this.pos = spawn;
	
	/**
	 * JQuery object for canvas
	 */
	this.canvas = $(canvas);
	
	this.context = $(this.canvas)[0].getContext("2d");
	
	/**
	 * @returns {Number}
	 */
	this.index = index === undefined ? 0 : index;
}

/**
 * @returns {Number}
 */
Tank.WIDTH = 40;

/**
 * @returns {Number}
 */
Tank.HEIGHT = 20;

Tank.TURRET_WIDTH = 30;
Tank.TURRET_HEIGHT = 10;

/**
 * Minimum movement of movement for the tank.
 * @returns {Number}
 */
Tank.MINMOVE = 10;

/**
 * Position of turret relative to the tank.
 */
Tank.TURRET_REL = new Vector(Tank.WIDTH / 2 - Tank.TURRET_WIDTH / 6, Tank.HEIGHT / 2 - Tank.TURRET_HEIGHT / 2);

/**
 * Return body color for this tank as hex.
 */
Tank.prototype.bodyColour = function () {
	"use strict";
	
	if (this.index === 0) {
		return "#FF0000";
	}
};

Tank.prototype.turretColour = function () {
	"use strict";
	
	if (this.index === 0) {
		return "#CC0000";
	}
};

Tank.prototype.drawBody = function () {
	"use strict";
	
	var context = this.context;

	context.save();
	context.fillStyle = this.bodyColour();
	context.strokeStyle = "#000000";
	
	context.beginPath();
	context.rect(this.pos.x - Tank.WIDTH / 2, this.pos.y - Tank.HEIGHT / 2, Tank.WIDTH, Tank.HEIGHT);
	context.closePath();
	
	context.stroke();
	context.fill();
	context.restore();
};

Tank.prototype.drawTurret = function () {
	"use strict";
	
	var context = this.context;

	context.save();
	context.fillStyle = this.turretColour();
	context.strokeStyle = "#000000";
	
	var turretPos = this.pos.add(Tank.TURRET_REL);
	
	context.beginPath();
	context.rect(turretPos.x - Tank.TURRET_WIDTH / 2, turretPos.y - Tank.TURRET_HEIGHT / 2, Tank.TURRET_WIDTH, Tank.TURRET_HEIGHT);
	context.closePath();
	
	context.stroke();
	context.fill();
	context.restore();
};

/**
 * Move the tank in the given direction
 */
Tank.prototype.move = function (dir) {
	if (dir === "right") {
		this.pos = this.pos.add(new Vector(Tank.MINMOVE, 0));
	} else if (dir == "left") {
		this.pos = this.pos.add(new Vector(-1 * Tank.MINMOVE, 0));
	} else if (dir == "up") {
		// a bit counter-intuitive here...
		this.pos = this.pos.add(new Vector(0, -1 * Tank.MINMOVE));
	} else if (dir === "down") {
		this.pos = this.pos.add(new Vector(0, Tank.MINMOVE));
	}
};


/**
 * Draw the tank on the canvas.
 */
Tank.prototype.draw = function () {
	"use strict";
	
	this.drawBody();
	this.drawTurret();
};

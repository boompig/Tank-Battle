/**
 * If a is specified, return a random number in the range [a, b].
 * If b is not specified, return a random number in the range [0, a].
 * 
 * @param {Number} a
 * @param {Number} b
 */
Math.randInt = function (a, b) {
	if (b === undefined) {
		b = a;
		a = 0;
	}
        
    return Math.floor(Math.random() * (b - a + 1)) + a;
};


/**
 * Container for utility functions
 */
function Utils () {
	
}

/**
 * Clear the given canvas.
 * @param {String} canvasSelector
 */
Utils.clearCanvas = function (canvasSelector) {
	"use strict";
	
	var canvas = $(canvasSelector)[0];
	canvas.width = canvas.width;
};

/**
 * Return a vector, relative to the canvas, of where the event was triggered.
 * Assumed that the event target is the canvas.
 * @returns {Vector}
 */
Utils.toCanvasCoords = function(evt) {
	"use strict";
    return new Vector(evt.pageX - $(evt.target).offset().left, evt.pageY - $(evt.target).offset().top);
};

/**
 * Return a random element from given array.
 * @param {Array} arr
 */
Utils.randomChoice = function (arr) {
        return arr[Math.randInt(0, arr.length - 1)];
};
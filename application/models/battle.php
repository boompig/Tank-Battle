<?php
/**
 * Model for DB table Battle
 */
class Battle {
	const ACTIVE = 1;
	const U1WON = 2;
	const U2WON = 3;
	const DRAW = 4;

	public $id;

	public $user1_id;
	public $user2_id;

	public $u1_msg = "";
	public $u1_x1;
	public $u1_y1;
	public $u1_x2;
	public $u1_y2;
	public $u1_angle = 0;
	public $u1_shot = false;
	public $u1_hit = false;

	public $u2_msg = "";
	public $u2_x1;
	public $u2_y1;
	public $u2_x2;
	public $u2_y2;
	public $u2_angle = 180;
	public $u2_shot = false;
	public $u2_hit = false;

	public $battle_status_id = self::ACTIVE;

	function getTextBattleStatus () {
		switch ($this -> battle_status_id) {
			case (Battle::ACTIVE):
				return "active";
			case (Battle::U1WON):
				return "u1won";
			case (Battle::U2WON):
				return "u2won";
			case (Battle::DRAW):
				return "draw";
		}
	}
}

<?php
class Battle_model extends CI_Model {
	
	function getExclusive($id)
	{
		$sql = "select * from battle where id=? for update";
		$query = $this->db->query($sql,array($id));
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Battle');
		else
			return null;
	}

	function get($id)
	{
		$this->db->where('id',$id);
		$query = $this->db->get('battle');
		if ($query && $query->num_rows() > 0)
			return $query->row(0,'Battle');
		else
			return null;
	}
	
	
	function insert($battle) {
		return $this->db->insert('battle',$battle);
	}
	
	function updateU1($id, $x1, $y1, $x2, $y2, $angle, $shot, $hit) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('u1_x1'=>$x1,
												'u1_y1'=>$y1,							    
												'u1_x2'=>$x2,
												'u1_y2'=>$y2,
												'u1_angle'=>$angle,
												'u1_shot'=>$shot,
												'u1_hit'=>$hit
												));
	}
	
	function clearShotU1($id) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('u1_shot'=>false));
	}
	
	function updateU2($id, $x1, $y1, $x2, $y2, $angle, $shot, $hit) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('u2_x1'=>$x1,
				'u2_y1'=>$y1,
				'u2_x2'=>$x2,
				'u2_y2'=>$y2,
				'u2_angle'=>$angle,
				'u2_shot'=>$shot,
				'u2_hit'=>$hit
		));
	}

	function clearShotU2($id) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('u2_shot'=>false));
	}
	
	
	function updateMsgU1($id,$msg) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('u1_msg'=>$msg));
	}
	
	function updateMsgU2($id,$msg) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('u2_msg'=>$msg));
	}
	
	
	
	function updateStatus($id, $status) {
		$this->db->where('id',$id);
		return $this->db->update('battle',array('battle_status_id'=>$status));
	}
	
}
?>
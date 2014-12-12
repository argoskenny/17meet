<?php
class Meet_model extends CI_Model{
	
	// 建構
	public function __construct()
	{
		$this->load->database();
	}
	
	
	// 問題建議儲存
	public function save_feedback()
	{
		//return $this->db->insert('r_feedback', $data);
	}
	
}
?>
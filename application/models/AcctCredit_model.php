<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCredit_model extends CI_Model {
		var $table = "acct_credits";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}
			public function getData(){
			$this->db->select('*');
			$this->db->from('acct_credits');
			$this->db->where('data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
			}
		
		
	}
?>
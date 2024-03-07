<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsAccountBlockir_model extends CI_Model {
		var $table = "acct_savings_account_blockir";
		var $column_order = array(null, 'acct_savings_account_blockir.savings_account_no','core_member.member_name','core_member.member_address',); //field yang ada di table user
		var $column_search = array('acct_savings_account_blockir.savings_account_no','core_member.member_name','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_savings_account_blockir.savings_account_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getAcctSavingsAccountBlockir(){
			$this->db->select('acct_savings_account_blockir.savings_account_blockir_id, acct_savings_account_blockir.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account_blockir.member_id, core_member.member_name, core_member.member_address, acct_savings_account_blockir.savings_account_blockir_date, acct_savings_account_blockir.savings_account_blockir_type, acct_savings_account_blockir.savings_account_blockir_status, acct_savings_account_blockir.savings_account_blockir_amount, acct_savings_account_blockir.savings_account_unblockir_date');
			$this->db->from('acct_savings_account_blockir');
			$this->db->join('core_member', 'acct_savings_account_blockir.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_savings_account_blockir.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->where('acct_savings_account_blockir.savings_account_blockir_status', 1);
			return $this->db->get()->result_array();
		}

		private function _get_datatables_query()
	    {
	        $this->db->select('acct_savings_account_blockir.savings_account_blockir_id, acct_savings_account_blockir.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account_blockir.member_id, core_member.member_name, core_member.member_address, acct_savings_account_blockir.savings_account_blockir_date, acct_savings_account_blockir.savings_account_blockir_type, acct_savings_account_blockir.savings_account_blockir_status, acct_savings_account_blockir.savings_account_blockir_amount, acct_savings_account_blockir.savings_account_unblockir_date');
	        $this->db->from('acct_savings_account_blockir');
	        $this->db->join('acct_savings_account', 'acct_savings_account_blockir.savings_account_id = acct_savings_account.savings_account_id');
	        $this->db->join('core_member', 'acct_savings_account_blockir.member_id = core_member.member_id');
	 		$this->db->order_by('acct_savings_account_blockir.savings_account_blockir_date', 'ASC');
	        $i = 0;
	     
	        foreach ($this->column_search as $item) // looping awal
	        {
	            if($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
	            {
	                 
	                if($i===0) // looping awal
	                {
	                    $this->db->group_start(); 
	                    $this->db->like($item, $_POST['search']['value']);
	                }
	                else
	                {
	                    $this->db->or_like($item, $_POST['search']['value']);
	                }
	 
	                if(count($this->column_search) - 1 == $i) 
	                    $this->db->group_end(); 
	            }
	            $i++;
	        }
	         
	        if(isset($_POST['order'])) 
	        {
	            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
	        } 
	        else if(isset($this->order))
	        {
	            $order = $this->order;
	            $this->db->order_by(key($order), $order[key($order)]);
	        }
	    }
 
	    function get_datatables()
	    {
	        $this->_get_datatables_query();
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered()
	    {
	        $this->_get_datatables_query();
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all()
	    {
	        $this->db->from($this->table);
	        return $this->db->count_all_results();
	    }

	    public function getSavingsAccountBlockirStatus($savings_account_id){
	    	$this->db->select('savings_account_blockir_status');
	    	$this->db->from('acct_savings_account');
	    	$this->db->where('savings_account_id', $savings_account_id);
	    	$result = $this->db->get()->row_array();
	    	return $result['savings_account_blockir_status'];
	    }

	    public function insertAcctSavingsAccountBlockir($data){
			$query = $this->db->insert('acct_savings_account_blockir',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function updateAcctSavingsAccount($data){
			$this->db->where('savings_account_id', $data['savings_account_id']);
			$query = $this->db->update('acct_savings_account',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_member.kecamatan_id, core_member.identity_id, core_member.member_job, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.voided_remark, acct_savings_account.validation, acct_savings_account.validation_on, acct_savings_account.validation_id, acct_savings_account.office_id');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccountBlockir_Detail($savings_account_blockir_id){
			$this->db->select('acct_savings_account_blockir.savings_account_blockir_id, acct_savings_account_blockir.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.savings_account_last_balance, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account_blockir.member_id, core_member.member_name, core_member.member_address, core_member.member_identity_no, acct_savings_account_blockir.savings_account_blockir_date, acct_savings_account_blockir.savings_account_blockir_type, acct_savings_account_blockir.savings_account_blockir_status, acct_savings_account_blockir.savings_account_blockir_amount, acct_savings_account_blockir.savings_account_unblockir_date');
			$this->db->from('acct_savings_account_blockir');
			$this->db->join('core_member', 'acct_savings_account_blockir.member_id = core_member.member_id');
			$this->db->join('acct_savings_account', 'acct_savings_account_blockir.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account_blockir.savings_account_blockir_id', $savings_account_blockir_id);
			return $this->db->get()->row_array();
		}

		 public function updateAcctSavingsAccountBlockir($data){
		 	$this->db->where('savings_account_blockir_id', $data['savings_account_blockir_id']);
			$query = $this->db->update('acct_savings_account_blockir',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}	
	}
?>
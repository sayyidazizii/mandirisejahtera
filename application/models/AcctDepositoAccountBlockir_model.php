<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctDepositoAccountBlockir_model extends CI_Model {
		var $table = "acct_deposito_account_blockir";
		var $column_order = array(null, 'acct_deposito_account_blockir.deposito_account_no','core_member.member_name','core_member.member_address',); //field yang ada di table user
		var $column_search = array('acct_deposito_account_blockir.deposito_account_no','core_member.member_name','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_deposito_account_blockir.deposito_account_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

		public function getAcctDepositoAccountBlockir(){
			$this->db->select('acct_deposito_account_blockir.deposito_account_blockir_id, acct_deposito_account_blockir.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account_blockir.member_id, core_member.member_name, core_member.member_address, acct_deposito_account_blockir.deposito_account_blockir_date, acct_deposito_account_blockir.deposito_account_blockir_type, acct_deposito_account_blockir.deposito_account_blockir_status, acct_deposito_account_blockir.deposito_account_blockir_amount, acct_deposito_account_blockir.deposito_account_unblockir_date');
			$this->db->from('acct_deposito_account_blockir');
			$this->db->join('core_member', 'acct_deposito_account_blockir.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_account_blockir.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_account_blockir.deposito_account_blockir_status', 1);
			return $this->db->get()->result_array();
		}

		private function _get_datatables_query()
	    {
	        $this->db->select('acct_deposito_account_blockir.deposito_account_blockir_id, acct_deposito_account_blockir.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account_blockir.member_id, core_member.member_name, core_member.member_address, acct_deposito_account_blockir.deposito_account_blockir_date, acct_deposito_account_blockir.deposito_account_blockir_type, acct_deposito_account_blockir.deposito_account_blockir_status, acct_deposito_account_blockir.deposito_account_blockir_amount, acct_deposito_account_blockir.deposito_account_unblockir_date');
	        $this->db->from('acct_deposito_account_blockir');
	        $this->db->join('acct_deposito_account', 'acct_deposito_account_blockir.deposito_account_id = acct_deposito_account.deposito_account_id');
	        $this->db->join('core_member', 'acct_deposito_account_blockir.member_id = core_member.member_id');
	 		$this->db->order_by('acct_deposito_account_blockir.deposito_account_blockir_date', 'ASC');
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

	    public function getDepositoAccountBlockirStatus($deposito_account_id){
	    	$this->db->select('deposito_account_blockir_status');
	    	$this->db->from('acct_deposito_account');
	    	$this->db->where('deposito_account_id', $deposito_account_id);
	    	$result = $this->db->get()->row_array();
	    	return $result['deposito_account_blockir_status'];
	    }

	    public function insertAcctDepositoAccountBlockir($data){
			$query = $this->db->insert('acct_deposito_account_blockir',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function updateAcctDepositoAccount($data){
			$this->db->where('deposito_account_id', $data['deposito_account_id']);
			$query = $this->db->update('acct_deposito_account',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctDepositoAccount_Detail($deposito_account_id){
			$this->db->select('acct_deposito_account.deposito_account_id, acct_deposito_account.member_id, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_member.kecamatan_id, core_member.identity_id, core_member.member_job, acct_deposito_account.deposito_id, acct_deposito.deposito_code, acct_deposito.deposito_name, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_amount, acct_deposito_account.voided_remark, acct_deposito_account.validation, acct_deposito_account.validation_on, acct_deposito_account.validation_id, acct_deposito_account.office_id');
			$this->db->from('acct_deposito_account');
			$this->db->join('core_member', 'acct_deposito_account.member_id = core_member.member_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_id', $deposito_account_id);
			return $this->db->get()->row_array();
		}

		public function getAcctDepositoAccountBlockir_Detail($deposito_account_blockir_id){
			$this->db->select('acct_deposito_account_blockir.deposito_account_blockir_id, acct_deposito_account_blockir.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.deposito_account_amount, acct_deposito_account.deposito_id, acct_deposito.deposito_name, acct_deposito_account_blockir.member_id, core_member.member_name, core_member.member_address, core_member.member_identity_no, acct_deposito_account_blockir.deposito_account_blockir_date, acct_deposito_account_blockir.deposito_account_blockir_type, acct_deposito_account_blockir.deposito_account_blockir_status, acct_deposito_account_blockir.deposito_account_blockir_amount, acct_deposito_account_blockir.deposito_account_unblockir_date');
			$this->db->from('acct_deposito_account_blockir');
			$this->db->join('core_member', 'acct_deposito_account_blockir.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account', 'acct_deposito_account_blockir.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->join('acct_deposito', 'acct_deposito_account.deposito_id = acct_deposito.deposito_id');
			$this->db->where('acct_deposito_account_blockir.deposito_account_blockir_id', $deposito_account_blockir_id);
			return $this->db->get()->row_array();
		}

		 public function updateAcctDepositoAccountBlockir($data){
		 	$this->db->where('deposito_account_blockir_id', $data['deposito_account_blockir_id']);
			$query = $this->db->update('acct_deposito_account_blockir',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}	
	}
?>
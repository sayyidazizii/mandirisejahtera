<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCreditsAgunan_model extends CI_Model {
		var $table = "acct_credits_agunan";
		var $column_order = array(null, 'acct_credits_account.credits_account_serial','core_member.member_name','acct_credits_agunan.credits_agunan_bpkb_nomor', 'acct_credits_agunan.credits_agunan_shm_no_sertifikat'); //field yang ada di table user
		var $column_search = array('acct_credits_account.credits_account_serial','core_member.member_name','acct_credits_agunan.credits_agunan_bpkb_nomor', 'acct_credits_agunan.credits_agunan_shm_no_sertifikat'); //field yang diizin untuk pencarian 
		var $order = array('acct_credits_agunan.credits_agunan_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 

        public function getCoreBranch(){
            $this->db->select('core_branch.branch_id, core_branch.branch_name');
            $this->db->from('core_branch');
            $this->db->where('core_branch.data_state', 0);
            $result = $this->db->get()->result_array();
            return $result;
        }

		public function getMemberName($member_id){
			$this->db->select('core_member.member_name');
			$this->db->from('core_member');
			$this->db->where('core_member.member_id', $member_id);
			$this->db->where('core_member.data_state', '0');
			$result = $this->db->get()->row_array();
			return $result['member_name'];
		}

		
		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

        public function updateAcctCreditsAgunan($data){
            $this->db->where('credits_agunan_id', $data['credits_agunan_id']);
            if($this->db->update('acct_credits_agunan', $data)){
                return true;
            } else {
                return false;
            }
        }

        public function updateAgunanStatus($credits_agunan_id){
            $this->db->where('credits_agunan_id', $credits_agunan_id);
            if($this->db->update('acct_credits_agunan', array('credits_agunan_status'=>1))){
                return true;
            } else {
                return false;
            }
        }

		public function getExportAcctCreditsAgunan($branch_id){
			$this->db->select('acct_credits_agunan.*, acct_credits_account.credits_account_serial, acct_credits_account.member_id');
	        $this->db->from('acct_credits_agunan');
	        $this->db->join('acct_credits_account','acct_credits_agunan.credits_account_id = acct_credits_account.credits_account_id');
	        $this->db->join('core_member','acct_credits_account.member_id = core_member.member_id');
	 		$this->db->where('acct_credits_account.data_state', 0);
            if(!empty($branch_id)){
                 $this->db->where('acct_credits_account.branch_id', $branch_id);
            }
	 		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
	 		return $this->db->get();
		}

		public function getAcctCreditAgunanDetail($credits_agunan_id){
			$this->db->select('acct_credits_agunan.*, acct_credits_account.credits_id, acct_credits_account.credits_account_date, acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_identity_no, core_member_working.member_company_job_title, core_member.member_address, core_member.member_phone');
	        $this->db->from('acct_credits_agunan');
	        $this->db->join('acct_credits_account','acct_credits_agunan.credits_account_id = acct_credits_account.credits_account_id');
	        $this->db->join('core_member','acct_credits_account.member_id = core_member.member_id');
	        $this->db->join('core_member_working','core_member_working.member_id = core_member.member_id');
	 		$this->db->where('acct_credits_agunan.data_state', 0);
	 		$this->db->where('acct_credits_agunan.credits_agunan_id', $credits_agunan_id);
            $result = $this->db->get()->row_array();
	 		return $result;
		}
		

		private function _get_datatables_query($branch_id)
    {
    	$this->db->select('acct_credits_agunan.*, acct_credits_account.credits_account_serial, acct_credits_account.credits_id, acct_credits_account.member_id');
        $this->db->from('acct_credits_agunan');
        $this->db->join('acct_credits_account','acct_credits_agunan.credits_account_id = acct_credits_account.credits_account_id');
        $this->db->join('core_member','acct_credits_account.member_id = core_member.member_id');
 		$this->db->where('acct_credits_account.data_state', 0);
        if(!empty($branch_id)){
             $this->db->where('acct_credits_account.branch_id', $branch_id);
        }
       
 		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
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
 
    function get_datatables($branch_id)
    {
        $this->_get_datatables_query($branch_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered($branch_id)
    {
        $this->_get_datatables_query($branch_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all($branch_id)
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    
	}
?>
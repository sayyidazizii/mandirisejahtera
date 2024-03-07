<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsCashMutationBranch_model extends CI_Model {
		var $table = "acct_savings_cash_mutation";
		var $column_order = array(null, 'acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address',); //field yang ada di table user
		var $column_search = array('acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_savings_cash_mutation.savings_cash_mutation_id' => 'asc');

		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavingsCashMutationBranch($start_date, $end_date, $branch_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.member_id, core_member.member_name, acct_savings_cash_mutation.savings_id, acct_savings.savings_name, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.mutation_id, acct_mutation.mutation_name, acct_savings_cash_mutation.validation, acct_savings_cash_mutation.validation_on, acct_savings_cash_mutation.validation_id');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_mutation', 'acct_savings_cash_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_cash_mutation.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date >=', $start_date);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date <=', $end_date);
			if(!empty($savings_account_id)){
				$this->db->where('acct_savings_cash_mutation.branch_id', $branch_id);
			}
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		function get_datatables($start_date, $end_date, $branch_id)
	    {
	        $this->_get_datatables_query($start_date, $end_date, $branch_id);
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered($start_date, $end_date, $branch_id)
	    {
	        $this->_get_datatables_query($start_date, $end_date, $branch_id);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all($start_date, $end_date, $branch_id)
	    {
	        $this->db->from($this->table);
	        $this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date >=', $start_date);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date <=', $end_date);
			if(!empty($savings_account_id)){
				$this->db->where('acct_savings_cash_mutation.branch_id', $branch_id);
			}
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
	        return $this->db->count_all_results();
	    }

	    private function _get_datatables_query($start_date, $end_date, $branch_id)
	    {
	    	// $this->db->select('acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address');
	    	$this->db->select('acct_savings_cash_mutation.*, acct_mutation.*, acct_savings_account.*, core_member.*, acct_savings.*, acct_savings_cash_mutation.validation AS validation_status');
	       	$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_mutation', 'acct_savings_cash_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_cash_mutation.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date >=', $start_date);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_date <=', $end_date);
			if(!empty($savings_account_id)){
				$this->db->where('acct_savings_cash_mutation.branch_id', $branch_id);
			}
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
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


		public function getAcctSavingsAccount($savings_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name, core_member.member_address, acct_savings_account.savings_id, acct_savings.savings_name');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			if(!empty($savings_id)){
				$this->db->where('acct_savings_account.savings_id', $savings_id);
			}
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			return $this->db->get()->result_array();
		}

		public function getAcctMutation(){
			$this->db->select('mutation_id, mutation_name');
			$this->db->from('acct_mutation');
			$this->db->where('data_state', 0);
			$this->db->where('mutation_module', 'TAB');
			return $this->db->get()->result_array();
		}

		public function getAcctSavingsCashMutationToken($savings_cash_mutation_token){
			$this->db->select('savings_cash_mutation_token');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->where('savings_cash_mutation_token', $savings_cash_mutation_token);
			return $this->db->get();
		}

		public function getAcctJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
		}

		public function getAcctJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.branch_id, acct_savings_account.savings_account_no, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_identity, core_member.member_identity_no, acct_savings_account.savings_account_blockir_type, acct_savings_account.savings_account_blockir_status, acct_savings_account.savings_account_blockir_amount');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function getMutationFunction($mutation_id){
			$this->db->select('mutation_function');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_function'];
		}

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getCityName($city_id){
			$this->db->select('city_name');
			$this->db->from('core_city');
			$this->db->where('city_id', $city_id);
			$result = $this->db->get()->row_array();
			return $result['city_name'];
		}

		public function getKecamatanName($kecamatan_id){
			$this->db->select('kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('kecamatan_id', $kecamatan_id);
			$result = $this->db->get()->row_array();
			return $result['kecamatan_name'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}
		
		public function insertAcctSavingsCashMutationBranch($data){
			return $query = $this->db->insert('acct_savings_cash_mutation',$data);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}

		public function getAcctSavingsCashMutationBranch_Last($created_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.member_id, core_member.member_name');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_savings_account','acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member','acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->where('acct_savings_cash_mutation.created_id', $created_id);
			$this->db->order_by('acct_savings_cash_mutation.savings_cash_mutation_id','DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getMutationJournalDesc($mutation_id){
			$this->db->select('mutation_journal_desc');
			$this->db->from('acct_mutation');
			$this->db->where('mutation_id', $mutation_id);
			$result = $this->db->get()->row_array();
			return $result['mutation_journal_desc'];
		}

		public function insertAcctJournalVoucher($data){
			if ($this->db->insert('acct_journal_voucher', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getJournalVoucherID($created_id){
			$this->db->select('acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher');
			$this->db->where('acct_journal_voucher.created_id', $created_id);
			$this->db->order_by('acct_journal_voucher.journal_voucher_id', 'DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_id'];
		}

		public function getAccountID($savings_id){
			$this->db->select('acct_savings.account_id');
			$this->db->from('acct_savings');
			$this->db->where('acct_savings.savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['account_id'];
		}

		public function getAccountRAKID($branch_id){
			$this->db->select('core_branch.account_rak_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_rak_id'];
		}

		public function getAccountAKAID($branch_id){
			$this->db->select('core_branch.account_aka_id');
			$this->db->from('core_branch');
			$this->db->where('core_branch.branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['account_aka_id'];
		}

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}
		
		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}
		
		public function getAcctSavingsCashMutationBranch_Detail($savings_cash_mutation_id){
			$this->db->select('acct_savings_cash_mutation.savings_cash_mutation_id, acct_savings_cash_mutation.savings_account_id, acct_savings_account.savings_account_no, acct_savings_cash_mutation.savings_id, acct_savings.savings_name, acct_savings_cash_mutation.mutation_id, acct_mutation.mutation_name, acct_savings_cash_mutation.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_member.kecamatan_id, acct_savings_cash_mutation.branch_id, core_branch.branch_city,  core_member.identity_id, core_member.member_identity_no, acct_savings_cash_mutation.savings_cash_mutation_date, acct_savings_cash_mutation.savings_cash_mutation_amount, acct_savings_cash_mutation.savings_cash_mutation_opening_balance, acct_savings_cash_mutation.savings_cash_mutation_last_balance, acct_savings_cash_mutation.voided_remark, acct_savings_cash_mutation.validation, acct_savings_cash_mutation.validation_on, acct_savings_cash_mutation.validation_id');
			$this->db->from('acct_savings_cash_mutation');
			$this->db->join('acct_mutation', 'acct_savings_cash_mutation.mutation_id = acct_mutation.mutation_id');
			$this->db->join('acct_savings_account', 'acct_savings_cash_mutation.savings_account_id = acct_savings_account.savings_account_id');
			$this->db->join('core_member', 'acct_savings_cash_mutation.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_cash_mutation.savings_id = acct_savings.savings_id');
			$this->db->join('core_branch', 'acct_savings_cash_mutation.branch_id = core_branch.branch_id');
			$this->db->where('acct_savings_cash_mutation.data_state', 0);
			$this->db->where('acct_savings_cash_mutation.savings_cash_mutation_id', $savings_cash_mutation_id);
			return $this->db->get()->row_array();
		}

		public function validationAcctSavingsCashMutationBranch($data){
			$this->db->where("savings_cash_mutation_id",$data['savings_cash_mutation_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function voidAcctSavingsCashMutationBranch($data){
			$this->db->where("savings_cash_mutation_id",$data['savings_cash_mutation_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>
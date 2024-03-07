<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctZakatFund_model extends CI_Model {
		var $table = "acct_zakat_fund";
		var $column_order = array(null, 'zakat_fund_description'); //field yang ada di table user
		var $column_search = array('zakat_fund_id','zakat_fund_description'); //field yang diizin untuk pencarian 
		var $order = array('zakat_fund_id' => 'asc');
		
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
		
		
		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getBranchCode($branch_id){
			$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getZakatFundLastBalance(){
			$this->db->select('acct_zakat_fund.zakat_fund_last_balance');
			$this->db->from('acct_zakat_fund');
			$this->db->where('acct_zakat_fund.data_state', 0);
			$this->db->limit(1);
			$this->db->order_by('acct_zakat_fund.zakat_fund_id', 'DESC');
			$result =  $this->db->get()->row_array();
			return $result['zakat_fund_last_balance'];
		}
		
		public function insertAcctZakatFund($data){
			$query = $this->db->insert('acct_zakat_fund',$data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function getLastAcctZakatFundReceived($created_id){
			$this->db->select('acct_zakat_fund.zakat_fund_id, acct_zakat_fund.zakat_fund_description');
			$this->db->from('acct_zakat_fund');
			$this->db->where('acct_zakat_fund.data_state', 0);
			$this->db->where('acct_zakat_fund.zakat_fund_type', 0);
			$this->db->where('acct_zakat_fund.created_id', $created_id);
			$this->db->limit(1);
			$this->db->order_by('acct_zakat_fund.zakat_fund_id', 'DESC');
			return $this->db->get()->row_array();
		}

		public function getLastAcctZakatFundDistribution($created_id){
			$this->db->select('acct_zakat_fund.zakat_fund_id, acct_zakat_fund.zakat_fund_description');
			$this->db->from('acct_zakat_fund');
			$this->db->where('acct_zakat_fund.data_state', 0);
			$this->db->where('acct_zakat_fund.zakat_fund_type', 1);
			$this->db->where('acct_zakat_fund.created_id', $created_id);
			$this->db->limit(1);
			$this->db->order_by('acct_zakat_fund.zakat_fund_id', 'DESC');
			return $this->db->get()->row_array();
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

		public function getAcctAccountSetting($account_setting_code){
			$this->db->select('acct_account_setting.account_id, acct_account_setting.account_setting_status, acct_account_setting.account_setting_name, acct_account_setting.section_id');
			$this->db->from('acct_account_setting');
			$this->db->where('acct_account_setting.account_setting_code', $account_setting_code);
			$this->db->where('acct_account_setting.data_state', 0);
			$result = $this->db->get()->result_array();
			
			return $result;
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

		private function _get_datatables_query_zakat_received($branch_id)
    	{
         
	        $this->db->from($this->table);
	 		$this->db->where('data_state', 0);
	 		$this->db->where('zakat_fund_type', 0);
	 		$this->db->where('branch_id', $branch_id);
	 		$this->db->order_by('zakat_fund_id', 'ASC');
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
	 
	    function get_datatables_zakat_received($branch_id)
	    {
	        $this->_get_datatables_query_zakat_received($branch_id);
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered_zakat_received($branch_id)
	    {
	        $this->_get_datatables_query_zakat_received($branch_id);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all_zakat_received($branch_id)
	    {
	        $this->db->from($this->table);
	        return $this->db->count_all_results();
	    }

	    private function _get_datatables_query_zakat_distribution($branch_id)
    	{
         
	        $this->db->from($this->table);
	 		$this->db->where('data_state', 0);
	 		$this->db->where('zakat_fund_type', 1);
	 		$this->db->where('branch_id', $branch_id);
	 		$this->db->order_by('zakat_fund_id', 'ASC');
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
	 
	    function get_datatables_zakat_distribution($branch_id)
	    {
	        $this->_get_datatables_query_zakat_distribution($branch_id);
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
	 
	    function count_filtered_zakat_distribution($branch_id)
	    {
	        $this->_get_datatables_query_zakat_distribution($branch_id);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
	 
	    public function count_all_zakat_distribution($branch_id)
	    {
	        $this->db->from($this->table);
	        return $this->db->count_all_results();
	    }
	}
?>
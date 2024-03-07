<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctJournalVoucher_model extends CI_Model {
		var $table = "acct_mutation";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctJournalVoucher($start_date, $end_date, $branch_id){
			$this->db->select('acct_journal_voucher_item.journal_voucher_item_id, acct_journal_voucher_item.journal_voucher_description, acct_journal_voucher_item.journal_voucher_debit_amount, acct_journal_voucher_item.journal_voucher_credit_amount, acct_journal_voucher_item.account_id, acct_account.account_code, acct_account.account_name, acct_journal_voucher_item.account_id_status, acct_journal_voucher.transaction_module_code, acct_journal_voucher.journal_voucher_date, acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->join('acct_journal_voucher','acct_journal_voucher_item.journal_voucher_id = acct_journal_voucher.journal_voucher_id');
			$this->db->join('acct_account','acct_journal_voucher_item.account_id = acct_account.account_id');
			$this->db->where('acct_journal_voucher.journal_voucher_date >=',$start_date);
			$this->db->where('acct_journal_voucher.journal_voucher_date <=',$end_date);	
			$this->db->where('acct_journal_voucher.transaction_module_id', 10);
			if(!empty($branch_id)){
				$this->db->where('acct_journal_voucher.branch_id', $branch_id);
			}
			$this->db->where('acct_journal_voucher.data_state', 0);		
			$this->db->where('acct_journal_voucher_item.journal_voucher_amount <>', 0);		
			$this->db->order_by('acct_journal_voucher.created_on','desc');
			$this->db->order_by('acct_journal_voucher.journal_voucher_date','desc');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccount(){
			$hasil = $this->db->query("
							SELECT acct_account.account_id, 
							CONCAT(acct_account.account_code,' - ', acct_account.account_name) as account_code 
							from acct_account
							where acct_account.data_state = 0");
			return $hasil->result_array();
		}

		public function getAccountCode($account_id){
			$this->db->select('account_code');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_code'];
		}

		public function getAccountName($account_id){
			$this->db->select('account_name');
			$this->db->from('acct_account');
			$this->db->where('account_id', $account_id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}

		public function getTransactionModuleID($transaction_module_code){
			$this->db->select('preference_transaction_module.transaction_module_id');
			$this->db->from('preference_transaction_module');
			$this->db->where('preference_transaction_module.transaction_module_code', $transaction_module_code);
			$result = $this->db->get()->row_array();
			return $result['transaction_module_id'];
		}
		
		public function insertAcctJournalVoucher($data){
			if($this->db->insert('acct_journal_voucher',$data)){
				return true;
			}else{
				return false;
			}
		}

		public function getJournalVoucherToken($journal_voucher_token){
			$this->db->select('journal_voucher_token');
			$this->db->from('acct_journal_voucher');
			$this->db->where('journal_voucher_token', $journal_voucher_token);
			return $this->db->get();
		}

		public function getJournalVoucherItemToken($journal_voucher_item_token){
			$this->db->select('journal_voucher_item_token');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_item_token', $journal_voucher_item_token);
			return $this->db->get();
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

		public function getAccountIDDefaultStatus($account_id){
			$this->db->select('acct_account.account_default_status');
			$this->db->from('acct_account');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('acct_account.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['account_default_status'];
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}
		
		public function insertAcctJournalVoucherItem($data){
			if($this->db->insert('acct_journal_voucher_item', $data)){
				return true;
			}else{
				return false;
			}
		}

		public function getMinID($journal_voucher_id){
			$this->db->select_min('journal_voucher_item_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->where('journal_voucher_id', $journal_voucher_id);
			$result = $this->db->get()->row_array();
			return $result['journal_voucher_item_id'];
		}
		
		// public function getAcctJournalVoucher_Detail($mutation_id){
		// 	$this->db->select('acct_mutation.mutation_id, acct_mutation.mutation_code, acct_mutation.mutation_name, acct_mutation.mutation_function, acct_mutation.mutation_status');
		// 	$this->db->from('acct_mutation');
		// 	$this->db->where('acct_mutation.mutation_id', $mutation_id);
		// 	return $this->db->get()->row_array();
		// }

		public function getAcctJournalVoucher_Detail($journal_voucher_id){
			$this->db->select('acct_journal_voucher.journal_voucher_id, acct_journal_voucher.journal_voucher_date, acct_journal_voucher.journal_voucher_description,acct_journal_voucher.journal_voucher_no, acct_journal_voucher.branch_id, core_branch.branch_name');
			$this->db->from('acct_journal_voucher');
			$this->db->join('core_branch','acct_journal_voucher.branch_id = core_branch.branch_id');
			$this->db->where('acct_journal_voucher.journal_voucher_id', $journal_voucher_id);
			return $this->db->get()->row_array();
		}
		
		public function getAcctJournalVoucherItem_Detail($journal_voucher_id){
			$this->db->select('acct_journal_voucher_item.journal_voucher_item_id, acct_journal_voucher_item.journal_voucher_id, acct_journal_voucher_item.account_id, acct_journal_voucher_item.journal_voucher_credit_amount, acct_journal_voucher_item.journal_voucher_debit_amount, acct_account.account_code, acct_account.account_name, acct_journal_voucher_item.journal_voucher_amount, acct_journal_voucher_item.account_id_status');
			$this->db->from('acct_journal_voucher_item');
			$this->db->join('acct_account','acct_journal_voucher_item.account_id = acct_account.account_id');
			$this->db->where('acct_journal_voucher_item.journal_voucher_id', $journal_voucher_id);
			return $this->db->get()->result_array();
		}

		public function updateAcctJournalVoucher($data){
			$this->db->where("mutation_id",$data['mutation_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function deleteAcctJournalVoucher($mutation_id){
			$this->db->where("mutation_id",$mutation_id);
			$query = $this->db->update($this->table, array('data_state'=>1));
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>
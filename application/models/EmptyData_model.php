<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class EmptyData_model extends CI_Model {
		var $table = "empty_data_log";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function insertEmptyData($data){
			$query = $this->db->insert('empty_data_log',$data);
			if($query){
				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('core_member');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_account');

				$this->db->empty_table('acct_savings_account_blockir');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_account_detail');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_cash_mutation');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_index');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_profit_sharing');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_transfer_mutation');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_transfer_mutation_from');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_savings_transfer_mutation_to');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_deposito_account');

				$this->db->empty_table('acct_deposito_account_blockir');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_deposito_account_extra');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_deposito_index');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_deposito_profit_sharing');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_journal_voucher');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_credits_account');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_credits_account_reschedule');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_credits_payment');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_account_balance');

				$this->db->where('branch_id', $data['branch_id']);
				$this->db->empty_table('acct_account_balance_detail');

				return true;
			}else{
				return false;
			}
		}
	}
?>
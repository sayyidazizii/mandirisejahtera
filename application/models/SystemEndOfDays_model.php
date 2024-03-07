<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class SystemEndOfDays_model extends CI_Model {
		var $table = "system_end_of_days";
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctJournalVoucher($date_history){
			
			$this->db->select('acct_journal_voucher_item.journal_voucher_item_id, acct_journal_voucher_item.journal_voucher_description, acct_journal_voucher_item.journal_voucher_debit_amount, acct_journal_voucher_item.journal_voucher_credit_amount, acct_journal_voucher_item.account_id, acct_account.account_code, acct_account.account_name, acct_journal_voucher_item.account_id_status, acct_journal_voucher.transaction_module_code, acct_journal_voucher.journal_voucher_date, acct_journal_voucher.journal_voucher_id');
			$this->db->from('acct_journal_voucher_item');
			$this->db->join('acct_journal_voucher','acct_journal_voucher_item.journal_voucher_id = acct_journal_voucher.journal_voucher_id');
			$this->db->join('acct_account','acct_journal_voucher_item.account_id = acct_account.account_id');
			$this->db->where('acct_journal_voucher.journal_voucher_date >=',$date_history);
			$this->db->where('acct_journal_voucher.data_state', 0);		
			$this->db->where('acct_journal_voucher_item.journal_voucher_amount <>', 0);		
					
			$this->db->order_by('acct_journal_voucher.created_on','desc');
			$this->db->order_by('acct_journal_voucher.journal_voucher_date','desc');
			$result = $this->db->get()->result_array();
			return $result;
		}
		public function getSystemEndOfDaysDate(){
			$this->db->select('system_end_of_days.*');
			$this->db->from('system_end_of_days');
			$this->db->order_by('system_end_of_days.created_at','desc');
			$result = $this->db->get()->row_array();
			return $result;
		}
		public function insertSystemEndOfDaysDate($data){
			if($this->db->insert('system_end_of_days', $data)){
				return true;
			}else{
				return false;
			}
		}
		public function updateSystemEndOfDaysDate($data,$id){
			$this->db->where('end_of_days_id',$id);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
	}
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class Dashboard_model extends CI_Model{
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $this->CI->load->model('Connection_model');
			// $this->CI->load->dbforge();

			// $auth 			= $this->session->userdata('auth');
			// $db_user 		= $this->Connection_model->define_database($auth['database']);
			// $this->db 	= $this->load->database($db_user, true);
			
		}
		
		public function getAcctCreditsAccount($date){
			$this->db->select('SUM(acct_credits_account.credits_account_amount) AS total_pencairan');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_account_date', $date);
			$this->db->where('acct_credits_account.data_state', 0);
			$this->db->where('acct_credits_account.credits_account_approve_status', 1);
			$this->db->where('acct_credits_account.credits_account_status', 0);
			// $this->db->where('acct_credits_account.credits_account_payment_to', 0);
			$result = $this->db->get()->row_array();
			return $result['total_pencairan'];
		}

		public function getAcctCreditsPayment_outstanding($date){
			$this->db->select('acct_credits_payment.credits_account_id, SUM(acct_credits_payment.credits_principal_last_balance) AS total_outstanding');
			$this->db->from('acct_credits_payment');
			$this->db->where('acct_credits_payment.credits_payment_date', $date);
			$this->db->where('acct_credits_payment.data_state', 0);
			$this->db->group_by('acct_credits_payment.credits_account_id');
			$this->db->group_by('acct_credits_payment.credits_principal_last_balance');
			$this->db->order_by('acct_credits_payment.credits_account_id', 'DESC');
			$this->db->order_by('acct_credits_payment.credits_principal_last_balance', 'DESC');
			$result = $this->db->get()->row_array();
			return $result['total_outstanding'];
		}
		
		public function getCreditsPayment($date){
			$query = $this->db->query("
					SELECT SUM(acct_credits_account.credits_account_last_balance) as total_outstanding
					FROM acct_credits_account
					WHERE acct_credits_account.credits_account_id NOT IN (SELECT acct_credits_payment.credits_account_id from acct_credits_payment WHERE acct_credits_payment.credits_payment_date <= '".$date."' GROUP BY acct_credits_payment.credits_account_id)
					AND acct_credits_account.credits_account_date <= '".$date."'
					AND acct_credits_account.data_state = 0
					and acct_credits_account.credits_account_status = 0
					");
		//print_r($this->db->last_query()); exit;
			$hasil = $query->row_array();
			return $hasil['total_outstanding'];

			// $this->db->select('SUM(acct_credits_account.credits_account_last_balance) as total_outstanding');
			// $this->db->from('acct_credits_account');
			// $this->db->join('acct_credits_payment','acct_credits_account.credits_account_id = acct_credits_payment.credits_account_id');
			// $this->db->WHERE('acct_credits_account.credits_account_id NOT IN(SELECT acct_credits_payment.credits_account_id from acct_credits_payment where acct_credits_payment.credits_payment_date = '.$date.')');
			// //$this->db->where('acct_credits_account.credits_account_approve_status', 1);
			// $this->db->WHERE('acct_credits_account.credits_account_date', $date);
			// $this->db->group_by('acct_credits_account.credits_account_id');
			// $this->db->order_by('acct_credits_account.created_on', 'DESC');
			// $result = $this->db->get()->row_array();
			// return $result['total_outstanding'];

		}

		public function getHitungAccount($date){
			$this->db->select('count(acct_credits_account.credits_account_amount) as total_akun');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_account_date', $date);
			$this->db->where('acct_credits_account.data_state', 0);
			$this->db->where('acct_credits_account.credits_account_approve_status', 1);
			$this->db->where('acct_credits_account.credits_account_status', 0);
			$result = $this->db->get()->row_array();
			return $result['total_akun'];
		}
		public function getHitungAkunOutstanding($date){
			$this->db->select('count(acct_credits_account.credits_account_last_balance) as total_akun');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.credits_account_date <=', $date);
			$this->db->where('acct_credits_account.data_state', 0);
			$this->db->where('acct_credits_account.credits_account_approve_status', 1);
			$this->db->where('acct_credits_account.credits_account_status', 0);
			$result = $this->db->get()->row_array();
			return $result['total_akun'];
		}
		/*public function getInvtItem($date){
			$this->db->select('sales_invoice_item.item_id');
			$this->db->from('sales_invoice_item');
			$this->db->join('sales_invoice', 'sales_invoice_item.sales_invoice_id = sales_invoice.sales_invoice_id');
			$this->db->where('sales_invoice.sales_invoice_date', $date);
			$this->db->where('sales_invoice.data_state', 0);
			$this->db->group_by('sales_invoice_item.item_id');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSalesInvoiceItem($month, $year){
			$this->db->select('sales_invoice_item.item_id, invt_item.item_name, SUM(sales_invoice_item.quantity) AS total_quantity');
			$this->db->from('sales_invoice_item');
			$this->db->join('sales_invoice', 'sales_invoice_item.sales_invoice_id = sales_invoice.sales_invoice_id');
			$this->db->join('invt_item', 'sales_invoice_item.item_id = invt_item.item_id');
			$this->db->where('MONTH(sales_invoice.sales_invoice_date)', $month);
			$this->db->where('YEAR(sales_invoice.sales_invoice_date)', $year);
			$this->db->where('sales_invoice.data_state', 0);
			$this->db->order_by('total_quantity', 'DESC');
			$this->db->group_by('sales_invoice_item.item_id');
			$this->db->limit(10);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getSalesInvoiceWeekly($date){
			$this->db->select('SUM(sales_invoice.total_amount) AS total_sales_invoice');
			$this->db->from('sales_invoice');
			$this->db->where('sales_invoice.sales_invoice_date', $date);
			$this->db->where('sales_invoice.data_state', 0);
			$result = $this->db->get()->row_array();
			return $result['total_sales_invoice'];
		}

*/
		public function getCreditsAccount(){
			$this->db->select('acct_credits_account.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_last_payment_date, acct_credits_account.credits_account_payment_date');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.data_state ', 0);
			// $this->db->where('acct_credits_account.credits_account_last_payment_date >=', $start);
			// $this->db->where('acct_credits_account.credits_account_last_payment_date <=', $end);
			$this->db->where('acct_credits_account.credits_account_approve_status', 1);
			$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');	
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}
		public function getPreferenceCollectibility(){
			$this->db->select('*');
			$this->db->from('preference_collectibility');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCreditsPaymentAmount($date){
			$this->db->select('SUM(acct_credits_payment.credits_payment_amount) AS total_credits_payment_amount');
			$this->db->from('acct_credits_payment');
			$this->db->join('acct_credits_account','acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->where('acct_credits_payment.data_state', 0);
			$this->db->where('acct_credits_account.credits_account_status', 0);
			$this->db->where('acct_credits_payment.credits_payment_date <=', $date);
			$result = $this->db->get()->row_array();
			return $result['total_credits_payment_amount'];
		}

		public function getCreditsAccountAmount($date){
			$this->db->select('SUM(credits_account_amount) AS total_credits_account_amount');
			$this->db->from('acct_credits_account');
			$this->db->where('data_state', 0);
			$this->db->where('credits_account_status', 0);
			$this->db->where('credits_account_approve_status', 1);
			$this->db->where('credits_account_date <=', $date);
			$result = $this->db->get()->row_array();
			return $result['total_credits_account_amount'];
		}
	}
?>
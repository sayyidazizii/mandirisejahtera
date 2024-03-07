<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctCashPaymentBranch_model extends CI_Model {
		var $table = "acct_credits_payment";
		var $column_order = array(null, 'acct_credits_account.credits_account_serial','core_member.member_name','acct_credits_payment.credits_payment_date',); //field yang ada di table user
		var $column_search = array('acct_credits_account.credits_account_serial','core_member.member_name','acct_credits_payment.credits_payment_date'); //field yang diizin untuk pencarian 
		var $order = array('acct_credits_payment.credits_payment_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		} 

		function get_datatables($start_date, $end_date, $credits_id, $branch_id)
	    {
	        $this->_get_datatables_query($start_date, $end_date, $credits_id, $branch_id);
			// $this->db->join('core_member','acct_credits_account.member_id=core_member.member_id');
	        if($_POST['length'] != -1)
	        $this->db->limit($_POST['length'], $_POST['start']);
	        $query = $this->db->get();
	        return $query->result();
	    }
 
	    function count_filtered($start_date, $end_date, $credits_id, $branch_id)
	    {
	        $this->_get_datatables_query($start_date, $end_date, $credits_id, $branch_id);
	        $query = $this->db->get();
	        return $query->num_rows();
	    }
 
	    public function count_all($start_date, $end_date, $credits_id, $branch_id)
	    {
	    	
	        $this->db->from('acct_credits_payment');
			$this->db->join('core_member','acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account','acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->where('acct_credits_payment.credits_payment_date >=', $start_date);
			$this->db->where('acct_credits_payment.credits_payment_date <=', $end_date);
			if(!empty($credits_id)){
				$this->db->where('acct_credits_payment.credits_id', $created_id);
			}
			if(!empty($branch_id)){
				$this->db->where('acct_credits_payment.branch_id', $branch_id);
			}
	        return $this->db->count_all_results();
	    }

		private function _get_datatables_query($start_date, $end_date, $credits_id, $branch_id)
	    {
	         
	        $this->db->from('acct_credits_payment');
			$this->db->join('core_member','acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits','acct_credits_payment.credits_id = acct_credits.credits_id');
			$this->db->join('acct_credits_account','acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->where('acct_credits_payment.credits_payment_date >=', $start_date);
			$this->db->where('acct_credits_payment.credits_payment_date <=', $end_date);
			if(!empty($credits_id)){
				$this->db->where('acct_credits_payment.credits_id', $created_id);
			}
			if(!empty($branch_id)){
				$this->db->where('acct_credits_payment.branch_id', $branch_id);
			}
	 
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
		
		public function insert($data){
			// print_r($data);exit;
			return $query = $this->db->insert('acct_credits_payment',$data);
		}

		public function AcctCashPaymentLast($created_id){
			$this->db->select('acct_credits_payment.credits_payment_id, acct_credits_payment.member_id, core_member.member_name, acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.credits_id, acct_credits.credits_name');
			$this->db->from('acct_credits_payment');
			$this->db->join('core_member','acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account','acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('acct_credits','acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_payment.created_id', $created_id);
			$this->db->order_by('acct_credits_payment.created_on','DESC');
			$this->db->limit(1);
			$result = $this->db->get()->row_array();
			// print_r($result);exit;
			return $result;
		}

		public function getSavingsAccountNO($savings_account_id){
			$this->db->select('savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_account_id', $savings_account_id);
			$result = $this->db->get()->row_array();
			return $result['savings_account_no'];
		}

		public function getSavingsAccountID($savings_id){
			$this->db->select('account_id');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
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
		
		public function getDataByIDCredit($id){
			$this->db->select('*');
			$this->db->from($this->table);
			$this->db->where('credits_account_id',$id);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getAcctCreditspayment_Detail($credits_payment_id){
			$this->db->select('acct_credits_payment.credits_payment_id, acct_credits_payment.member_id, core_member.member_name, core_member.member_address, acct_credits_payment.credits_account_id, acct_credits_account.credits_account_serial, acct_credits_account.credits_id, acct_credits.credits_name, acct_credits_payment.credits_payment_to, acct_credits_payment.credits_payment_amount, acct_credits_payment.savings_account_id');
			$this->db->from('acct_credits_payment');
			$this->db->join('core_member','acct_credits_payment.member_id = core_member.member_id');
			$this->db->join('acct_credits_account','acct_credits_payment.credits_account_id = acct_credits_account.credits_account_id');
			$this->db->join('acct_credits','acct_credits_account.credits_id = acct_credits.credits_id');
			$this->db->where('acct_credits_payment.credits_payment_id', $credits_payment_id);
			$result = $this->db->get()->row_array();
			// print_r($result);exit;
			return $result;
		}
	}
?>
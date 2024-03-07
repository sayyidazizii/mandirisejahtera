<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class AcctSavingsAccountUtility_model extends CI_Model {
		var $table = "acct_savings_account";
		var $column_order = array(null, 'acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address',); //field yang ada di table user
		var $column_search = array('acct_savings_account.savings_account_no','core_member.member_name','core_member.member_address'); //field yang diizin untuk pencarian 
		var $order = array('acct_savings_account.savings_account_id' => 'asc');
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			// $auth = $this->session->userdata('auth');
			// $this->CI->load->model('Connection_model');

			// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
			// $this->database = $this->load->database($database, true);
		} 
		
		public function getAcctSavingsAccountUtility($start_date, $end_date, $savings_id, $branch_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.validation, acct_savings_account.validation_on');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			// $this->db->where('acct_savings_account.savings_account_date >=', $start_date);
			// $this->db->where('acct_savings_account.savings_account_date <=', $end_date);
			$this->db->where('acct_savings_account.branch_id', $branch_id);

			if(!empty($savings_id)){
				$this->db->where('acct_savings_account.savings_id', $savings_id);
			}
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreMember($city_id, $kecamatan_id){
			$this->db->select('core_member.member_id, core_member.member_name, core_member.member_address, core_member.member_no, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_mother');
			$this->db->from('core_member');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			if(!empty($city_id)){
				$this->db->where('core_member.city_id', $city_id);
			}

			if(!empty($kecamatan_id)){
				$this->db->where('core_member.kecamatan_id', $kecamatan_id);
			}
			$this->db->where('core_member.data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state', 0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getCoreCity(){
			$this->db->select('city_id, city_name');
			$this->db->from('core_city');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreKecamatan($city_id){
			$this->db->select('core_kecamatan.kecamatan_id, core_kecamatan.kecamatan_name');
			$this->db->from('core_kecamatan');
			$this->db->where('core_kecamatan.city_id', $city_id);
			$this->db->where('core_kecamatan.data_state', '0');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctSavings(){
			$this->db->select('savings_id, savings_name');
			$this->db->from('acct_savings');
			$this->db->where('data_state', 0);
			$this->db->where('savings_status', 0);
			$this->db->order_by('savings_number','ASC');
			return $this->db->get()->result_array();
		}

		public function getCoreOffice(){
			$this->db->select('office_id, office_name');
			$this->db->from('core_office');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getBranchCode($branch_id){
			$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getSavingsName($savings_id){
			$this->db->select('savings_name');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_name'];
		}

		public function getSavingsCode($savings_id){
			$this->db->select('savings_code');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_code'];
		}

		public function getSavingsNisbah($savings_id){
			$this->db->select('savings_nisbah');
			$this->db->from('acct_savings');
			$this->db->where('savings_id', $savings_id);
			$result = $this->db->get()->row_array();
			return $result['savings_nisbah'];
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

		public function getUsername($user_id){
			$this->db->select('username');
			$this->db->from('system_user');
			$this->db->where('user_id', $user_id);
			$result = $this->db->get()->row_array();
			return $result['username'];
		}

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}

		public function getLastAccountSavingsNo($branch_id, $savings_id){
			$this->db->select('RIGHT(acct_savings_account.savings_account_no,5) as last_savings_account_no');
			$this->db->from('acct_savings_account');
			$this->db->where('acct_savings_account.branch_id', $branch_id);
			$this->db->where('acct_savings_account.savings_id', $savings_id);
			$this->db->limit(1);
			$this->db->order_by('acct_savings_account.savings_account_id', 'DESC');
			$result = $this->db->get();
			return $result;
		}

		public function getSavingsAccountToken($savings_account_token){
			$this->db->select('savings_account_token');
			$this->db->from('acct_savings_account');
			$this->db->where('savings_account_token', $savings_account_token);
			return $this->db->get();
		}
		
		public function insertAcctSavingsAccountUtility($data){
			return $query = $this->db->insert('acct_savings_account',$data);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		public function getPreferenceInventory(){
			$this->db->select('*');
			$this->db->from('preference_inventory');
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

		public function getAcctSavingsAccountUtility_Last($created_on){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_name');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member','acct_savings_account.member_id = core_member.member_id');
			$this->db->where('acct_savings_account.created_on', $created_on);
			$this->db->limit(1);
			$this->db->order_by('acct_savings_account.created_on','DESC');
			$result = $this->db->get()->row_array();
			return $result;
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
		
		public function getAcctSavingsAccountUtility_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, core_member.member_no, core_member.member_gender, core_member.member_address, core_member.member_phone, core_member.member_date_of_birth, core_member.member_identity_no, core_member.city_id, core_member.kecamatan_id, core_member.identity_id, core_member.member_job, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.voided_remark, acct_savings_account.validation, acct_savings_account.validation_on, acct_savings_account.validation_id, acct_savings_account.office_id');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function voidAcctSavingsAccountUtility($data){
			$this->db->where("savings_account_id",$data['savings_account_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		
		public function validationAcctSavingsAccountUtility($data){
			$this->db->where("savings_account_id",$data['savings_account_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getExport(){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.member_id, core_member.member_name, acct_savings_account.savings_id, acct_savings.savings_code, acct_savings.savings_name, acct_savings_account.savings_account_no, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance, acct_savings_account.validation, acct_savings_account.validation_on');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings.savings_status', 0);
			$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
			$result = $this->db->get();
			return $result;
		}

		public function updatedata($data,$id){
			$this->db->where("savings_account_id",$id);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		private function _get_datatables_query($branch_id)
    {
    	$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_no, core_member.member_name, core_member.member_address');
        $this->db->from('acct_savings_account');
        $this->db->join('core_member','acct_savings_account.member_id = core_member.member_id');
        $this->db->join('acct_savings','acct_savings_account.savings_id = acct_savings.savings_id');
 		$this->db->where('acct_savings_account.data_state', 0);
 		$this->db->where('acct_savings.savings_status', 0);
 		if(!empty($branch_id)){
       	 	$this->db->where('acct_savings_account.branch_id', $branch_id);
       	 }
 		$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
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
        if(!empty($branch_id)){
       	 	$this->db->where('acct_savings_account.branch_id', $branch_id);
       	 }
        return $this->db->count_all_results();
    }

    private function _get_datatables_query_master($start_date, $end_date, $savings_id, $branch_id)
    {
    	$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_no,  core_member.member_name, core_member.member_address, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
        $this->db->from('acct_savings_account');
        $this->db->join('core_member','acct_savings_account.member_id = core_member.member_id');
        $this->db->join('acct_savings','acct_savings_account.savings_id = acct_savings.savings_id');
        // if(!empty($start_date)){
        // 	$this->db->where('acct_savings_account.savings_account_date >=', $start_date);
        // }

        // if(!empty($end_date)){
        // 	$this->db->where('acct_savings_account.savings_account_date <=', $end_date);
        // }
        if(!empty($branch_id)){
       	 	$this->db->where('acct_savings_account.branch_id', $branch_id);
       	 }

        if(!empty($savings_id)){
        	$this->db->where('acct_savings_account.savings_id', $savings_id);
        }
 		$this->db->where('acct_savings_account.data_state', 0);
 		$this->db->where('acct_savings.savings_status', 0);
 		$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
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
 
    function get_datatables_master($start_date, $end_date, $savings_id, $branch_id)
    {
        $this->_get_datatables_query_master($start_date, $end_date, $savings_id, $branch_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered_master($start_date, $end_date, $savings_id, $branch_id)
    {
        $this->_get_datatables_query_master($start_date, $end_date, $savings_id, $branch_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_master($start_date, $end_date, $savings_id, $branch_id)
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function get_datatables_mbayar($member_id)
    {
        $this->_get_datatables_query_mbayar($member_id);
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered_mbayar($member_id)
    {
        $this->_get_datatables_query_mbayar($member_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all_mbayar($member_id)
    {
        $this->db->from($this->table);
        $this->db->join('acct_savings','acct_savings_account.savings_id = acct_savings.savings_id');
        $this->db->where('acct_savings.savings_status', 0);
        $this->db->where('acct_savings_account.member_id', $member_id);
        return $this->db->count_all_results();
    }

    private function _get_datatables_query_mbayar($member_id)
    {
    	$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.member_id, core_member.member_no,  core_member.member_name, core_member.member_address, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_date, acct_savings_account.savings_account_first_deposit_amount, acct_savings_account.savings_account_last_balance');
        $this->db->from('acct_savings_account');
        $this->db->join('core_member','acct_savings_account.member_id = core_member.member_id');
        $this->db->join('acct_savings','acct_savings_account.savings_id = acct_savings.savings_id');
		$this->db->where('acct_savings_account.member_id', $member_id);
 		$this->db->where('acct_savings_account.data_state', 0);
 		$this->db->where('acct_savings.savings_status', 0);
 		$this->db->order_by('acct_savings_account.savings_account_no', 'ASC');
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

	}
?>
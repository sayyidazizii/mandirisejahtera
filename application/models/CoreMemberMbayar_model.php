<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class CoreMemberMbayar_model extends CI_Model {
		var $table = "core_member";
		var $column_order = array(null, 'member_no','member_name','user_alamat','member_address',); //field yang ada di table user
		var $column_search = array('member_id','member_name','member_no','member_address'); //field yang diizin untuk pencarian 
		var $order = array('member_id' => 'asc');
		
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

		public function getBranchCity($branch_id){
			$this->db->select('branch_city');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_city'];
		}
		
		public function getCoreMember_Detail($member_id){
			$this->db->select('core_member.member_id, core_member.branch_id, core_branch.branch_name, core_member.member_no, core_member.member_name, core_member.member_gender, core_member.member_place_of_birth, core_member.member_date_of_birth, core_member.member_address, core_member.province_id, core_province.province_name, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.kelurahan_id, core_kelurahan.kelurahan_name, core_member.dusun_id, core_member.member_phone, core_member.member_job, core_member.member_identity, core_member.member_identity_no, core_member.member_postal_code, core_member.member_mother, core_member.member_heir, core_member.member_family_relationship, core_member.member_status, core_member.member_register_date, core_member.member_principal_savings, core_member.member_special_savings, core_member.member_mandatory_savings, core_member.member_character, core_member.member_token, core_member.member_principal_savings_last_balance, core_member.member_special_savings_last_balance, core_member.member_mandatory_savings_last_balance');
			$this->db->from('core_member');
			$this->db->join('core_province', 'core_member.province_id = core_province.province_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->join('core_kelurahan', 'core_member.kelurahan_id = core_kelurahan.kelurahan_id');
			// $this->db->join('core_dusun', 'core_member.dusun_id = core_dusun.dusun_id');
			$this->db->join('core_branch', 'core_member.branch_id = core_branch.branch_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->where('core_member.member_id', $member_id);
			return $this->db->get()->row_array();
		}

		public function getAcctSavingsAccount_Detail($savings_account_id){
			$this->db->select('acct_savings_account.savings_account_id, acct_savings_account.savings_account_no, acct_savings_account.branch_id, acct_savings_account.savings_id, acct_savings.savings_name, acct_savings_account.savings_account_last_balance, acct_savings_account.member_id, core_member.member_name, core_member.member_address, core_member.city_id, core_city.city_name, core_member.kecamatan_id, core_kecamatan.kecamatan_name, core_member.identity_id, core_member.member_identity_no');
			$this->db->from('acct_savings_account');
			$this->db->join('core_member', 'acct_savings_account.member_id = core_member.member_id');
			$this->db->join('acct_savings', 'acct_savings_account.savings_id = acct_savings.savings_id');
			$this->db->join('core_city', 'core_member.city_id = core_city.city_id');
			$this->db->join('core_kecamatan', 'core_member.kecamatan_id = core_kecamatan.kecamatan_id');
			$this->db->where('acct_savings_account.data_state', 0);
			$this->db->where('acct_savings_account.savings_account_id', $savings_account_id);
			return $this->db->get()->row_array();
		}

		public function updateCoreMember($data){
			$this->db->where("member_id",$data['member_id']);
			$query = $this->db->update($this->table, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			$this->db->limit(1);
			return $this->db->get()->row_array();
		}

		private function _get_datatables_query($branch_id)
    {
         $this->db->select('core_member.*, acct_savings_account.savings_account_no');
        $this->db->from('core_member');
        $this->db->join('acct_savings_account', 'core_member.savings_account_id = acct_savings_account.savings_account_id');
 		$this->db->where('core_member.data_state', 0);
 		// if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
 		
 		$this->db->order_by('core_member.member_no', 'ASC');
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
         $this->db->join('acct_savings_account', 'core_member.savings_account_id = acct_savings_account.savings_account_id');
   //      if(!empty($branch_id)){
 		// 	$this->db->where('branch_id', $branch_id);
 		// }
        return $this->db->count_all_results();
    }
	}
?>
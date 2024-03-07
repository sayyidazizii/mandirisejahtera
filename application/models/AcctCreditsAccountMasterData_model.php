<?php
defined('BASEPATH') or exit('No direct script access allowed');
class AcctCreditsAccountMasterData_model extends CI_Model
{
	var $table = "acct_credits_account";
	var $column_order = array(null, 'acct_credits_account.credits_account_serial', 'acct_savings_account.savings_account_no', 'core_member.member_name', 'core_member.member_address',); //field yang ada di table user
	var $column_search = array('acct_credits_account.credits_account_serial', 'acct_savings_account.savings_account_no', 'core_member.member_name', 'core_member.member_address'); //field yang diizin untuk pencarian 
	var $order = array('acct_credits_account.savings_account_id' => 'asc');

	public function __construct()
	{
		parent::__construct();
		$this->CI = get_instance();

		// $auth = $this->session->userdata('auth');
		// $this->CI->load->model('Connection_model');

		// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
		// $this->database = $this->load->database($database, true);
	}

	public function getCoreBranch()
	{
		$this->db->select('core_branch.branch_id, core_branch.branch_name');
		$this->db->from('core_branch');
		$this->db->where('core_branch.data_state', 0);
		$result = $this->db->get()->result_array();
		return $result;
	}

	public function getAcctCredits()
	{
		$this->db->select('acct_credits.credits_id, acct_credits.credits_name');
		$this->db->from('acct_credits');
		$this->db->where('acct_credits.data_state', 0);
		$result = $this->db->get()->result_array();
		return $result;
	}


	private function _get_datatables_query($start_date, $end_date, $branch_id, $credits_id)
	{
		$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.savings_account_id,  acct_credits_account.member_id, core_member.member_name, core_member.member_address, core_member.member_gender, core_member.member_date_of_birth, core_member.member_job, core_member.member_identity, core_member_working.member_working_type, core_member_working.member_company_name ,core_member.member_identity_no, acct_credits_account.credits_account_period, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_interest, acct_credits_account.credits_account_last_balance,acct_credits_account.credits_id, acct_credits.credits_name, acct_credits_account.credits_account_provisi, acct_credits_account.credits_account_komisi, acct_credits_account.credits_account_insurance, acct_credits_account.credits_account_stash, acct_credits_account.credits_account_adm_cost, acct_credits_account.credits_account_materai, acct_credits_account.credits_account_risk_reserve, acct_credits_account.credits_account_principal, acct_credits_account.credits_account_sales_name');
		$this->db->from('acct_credits_account');
		$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
		$this->db->join('core_member_working', 'acct_credits_account.member_id = core_member_working.member_id');
		$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
		// $this->db->join('acct_savings_account','acct_credits_account.savings_account_id = acct_savings_account.savings_account_id');
		$this->db->where('acct_credits_account.data_state', 0);
		$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
		$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
		if (!empty($branch_id)) {
			$this->db->where('acct_credits_account.branch_id', $branch_id);
		}
		if (!empty($credits_id)) {
			$this->db->where('acct_credits_account.credits_id', $credits_id);
		}

		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
		$i = 0;

		foreach ($this->column_search as $item) // looping awal
		{
			if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
			{

				if ($i === 0) // looping awal
				{
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($start_date, $end_date, $branch_id, $credits_id)
	{
		$this->_get_datatables_query($start_date, $end_date, $branch_id, $credits_id);
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($start_date, $end_date, $branch_id, $credits_id)
	{
		$this->_get_datatables_query($start_date, $end_date, $branch_id, $credits_id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($start_date, $end_date, $branch_id, $credits_id)
	{
		$this->db->from($this->table);
		$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
		$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
		if (!empty($branch_id)) {
			$this->db->where('acct_credits_account.branch_id', $branch_id);
		}
		if (!empty($credits_id)) {
			$this->db->where('acct_credits_account.credits_id', $credits_id);
		}
		return $this->db->count_all_results();
	}

	public function getAcctSavingsAccountNo($savings_account_id)
	{
		$this->db->select('savings_account_no');
		$this->db->from('acct_savings_account');
		$this->db->where('savings_account_id', $savings_account_id);
		$result = $this->db->get()->row_array();
		return $result['savings_account_no'];
	}

	public function getExport($branch_id)
	{
		$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.savings_account_id, core_member_working.member_working_type, core_member_working.member_company_name, acct_credits_account.member_id, core_member.member_name, core_member.member_address, core_member.member_gender, core_member.member_date_of_birth, core_member.member_job, core_member.member_identity, core_member.member_identity_no, acct_credits_account.credits_account_period, core_member.member_phone ,acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_principal_amount, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_interest, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_id, acct_credits.credits_name');
		$this->db->from('acct_credits_account');
		$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
		$this->db->join('core_member_working', 'acct_credits_account.member_id = core_member_working.member_id');
		$this->db->join('acct_credits', 'acct_credits_account.credits_id = acct_credits.credits_id');
		// $this->db->join('acct_savings_account','acct_credits_account.savings_account_id = acct_savings_account.savings_account_id');
		$this->db->where('acct_credits_account.data_state', 0);
		if (!empty($branch_id)) {
			$this->db->where('acct_credits_account.branch_id', $branch_id);
		}
		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
		return $this->db->get();
	}
}

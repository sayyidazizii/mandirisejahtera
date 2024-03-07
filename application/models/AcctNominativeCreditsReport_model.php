<?php
defined('BASEPATH') or exit('No direct script access allowed');
class AcctNominativeCreditsReport_model extends CI_Model
{
	var $table = "acct_savings_account";

	public function __construct()
	{
		parent::__construct();
		$this->CI = get_instance();

		// $auth = $this->session->userdata('auth');
		// $this->CI->load->model('Connection_model');

		// $database = $this->Connection_model->define_database($auth['user_id'], $auth['database']);
		// $this->database = $this->load->database($database, true);
	}

	public function getAcctNomintiveCreditsReport($start_date, $end_date, $branch_id)
	{
		$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_period, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_interest_last_balance,
				acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_payment_amount, acct_credits_account.credits_account_provisi, acct_credits_account.credits_account_komisi, acct_credits_account.credits_account_insurance, acct_credits_account.credits_account_stash, acct_credits_account.credits_account_adm_cost, acct_credits_account.credits_account_materai, acct_credits_account.credits_account_risk_reserve, acct_credits_account.credits_account_principal, acct_credits_account.credits_account_sales_name');
		$this->db->from('acct_credits_account');
		$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
		$this->db->where('acct_credits_account.data_state ', 0);
		$this->db->where('acct_credits_account.credits_approve_status', 1);
		$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
		$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
		$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
		if (!empty($branch_id)) {
			$this->db->where('acct_credits_account.branch_id', $branch_id);
		}
// 		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
// 		$this->db->order_by('acct_credits_account.member_id', 'ASC');
// 		$this->db->order_by('core_member.member_name', 'ASC');
// 		$this->db->order_by('core_member.member_id', 'ASC');
// 		$this->db->order_by('core_member.member_address', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_amount', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_last_balance', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_date', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_provisi', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_komisi', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_insurance', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_stash', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_adm_cost', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_materai', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_risk_reserve', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_principal', 'ASC');
// 		$this->db->order_by('acct_credits_account.credits_account_sales_name', 'ASC');
		$this->db->order_by('acct_credits_account.created_on', 'ASC');
		$result = $this->db->get()->result_array();
		// print_r($result);exit;
		return $result;
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
		return $this->db->get()->result_array();
	}

	public function getPreferenceCompany()
	{
		$this->db->select('*');
		$this->db->from('preference_company');
		$result = $this->db->get()->row_array();
		return $result;
	}

	public function getAcctSourceFund()
	{
		$this->db->select('acct_source_fund.source_fund_id, acct_source_fund.source_fund_name');
		$this->db->from('acct_source_fund');
		$this->db->where('acct_source_fund.data_state', 0);
		return $this->db->get()->result_array();
	}

	public function getUsername($user_id)
	{
		$this->db->select('username');
		$this->db->from('system_user');
		$this->db->where('user_id', $user_id);
		$result = $this->db->get()->row_array();
		return $result['username'];
	}

	public function getAcctNomintiveCreditsReport_Credits($start_date, $end_date, $credits_id, $branch_id)
	{
		$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_interest_last_balance ,acct_credits_account.credits_account_interest, acct_credits_account.credits_account_period, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_interest_last_balance,
				acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_payment_amount ');
		$this->db->from('acct_credits_account');
		$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
		$this->db->where('acct_credits_account.credits_approve_status', 1);
		$this->db->where('acct_credits_account.data_state ', 0);
		$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
		$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
		$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
		if (!empty($credits_id)) {
			$this->db->where('acct_credits_account.credits_id', $credits_id);
		}
		if (!empty($branch_id)) {
			$this->db->where('acct_credits_account.branch_id', $branch_id);
		}
		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
		$this->db->order_by('acct_credits_account.member_id', 'ASC');
		$this->db->order_by('core_member.member_name', 'ASC');
		$this->db->order_by('core_member.member_address', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_last_balance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_date', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_amount', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_interest_last_balance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_interest', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_period', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_provisi', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_komisi', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_insurance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_stash', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_adm_cost', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_materai', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_risk_reserve', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_principal', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_sales_name', 'ASC');
		$result = $this->db->get()->result_array();
		return $result;
	}

	public function getAcctNomintiveCreditsReport_SourceFund($start_date, $end_date, $source_fund_id, $branch_id)
	{
		$this->db->select('acct_credits_account.credits_account_serial, acct_credits_account.member_id, core_member.member_name, core_member.member_address, acct_credits_account.credits_account_date, acct_credits_account.credits_account_due_date, acct_credits_account.credits_account_interest, acct_credits_account.credits_account_last_balance, acct_credits_account.credits_account_amount, acct_credits_account.credits_account_interest_last_balance, acct_credits_account.credits_account_period, acct_credits_account.credits_account_interest_amount, acct_credits_account.credits_account_interest_last_balance,
				acct_credits_account.credits_account_payment_to, acct_credits_account.credits_account_payment_amount');
		$this->db->from('acct_credits_account');
		$this->db->join('core_member', 'acct_credits_account.member_id = core_member.member_id');
		$this->db->where('acct_credits_account.credits_approve_status', 1);
		$this->db->where('acct_credits_account.data_state ', 0);
		$this->db->where('acct_credits_account.credits_account_last_balance >', 0);
		$this->db->where('acct_credits_account.credits_account_date >=', $start_date);
		$this->db->where('acct_credits_account.credits_account_date <=', $end_date);
		$this->db->where('acct_credits_account.source_fund_id', $source_fund_id);
		if (!empty($branch_id)) {
			$this->db->where('acct_credits_account.branch_id', $branch_id);
		}
		$this->db->order_by('acct_credits_account.credits_account_serial', 'ASC');
		$this->db->order_by('acct_credits_account.member_id', 'ASC');
		$this->db->order_by('core_member.member_name', 'ASC');
		$this->db->order_by('core_member.member_address', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_date', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_due_date', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_interest', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_last_balance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_amount', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_interest_last_balance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_period', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_provisi', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_komisi', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_insurance', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_stash', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_adm_cost', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_materai', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_risk_reserve', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_principal', 'ASC');
		$this->db->order_by('acct_credits_account.credits_account_sales_name', 'ASC');
		$result = $this->db->get()->result_array();
		return $result;
	}
}

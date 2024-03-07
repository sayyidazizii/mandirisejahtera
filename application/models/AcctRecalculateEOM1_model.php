<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctRecalculateEOM_model extends CI_Model{
		
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();

			$this->CI->load->model('Connection_model');
			$this->CI->load->dbforge();

			// $auth 			= $this->session->userdata('auth');
			// $db_user 		= $this->Connection_model->define_database($auth['database']);
			// $this->db_user 	= $this->load->database($db_user, true);
		}

		public function getPreferenceCompany(){
			$this->db->select('*');
			$this->db->from('preference_company');
			return $this->db->get()->row_array();
		}

		public function getCoreBranch(){
			$this->db->select('core_branch.branch_id, core_branch.branch_name');
			$this->db->from('core_branch');
			$this->db->where('core_branch.data_state',0);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getBranchName($branch_id){
			$this->db->select('branch_name');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_name'];
		}

		public function insertAcctRecalculateEOMLog($data){
			if($this->db->insert('acct_recalculate_log', $data)){
				return true;
			} else {
				return false;
			}
		}

		public function getAcctAccount(){
			$query = $this->db->query("SELECT account_id, concat(account_code,' - ',account_name) AS account_code FROM acct_account WHERE data_state = 0 ");
			$result = $query->result_array();	
			return $result;
		}

		public function getAccountOpeningBalance($account_id, $data){
			$this->db->select("opening_balance");
			$this->db->from("acct_account_opening_balance");
			$this->db->where("account_id", $account_id);
			$this->db->where("month_period", $data['month_period']);
			$this->db->where("year_period", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getTotalAccountIn($account_id, $data){
			$this->db->select("SUM(account_in) AS total_mutation_in");
			$this->db->from("acct_account_balance_detail");
			$this->db->where("account_id", $account_id);
			$this->db->where("MONTH(transaction_date)", $data['month_period']);
			$this->db->where("YEAR(transaction_date)", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			$result = $this->db->get()->row_array();
			return $result['total_mutation_in'];
		}

		public function getTotalAccountOut($account_id, $data){
			$this->db->select("SUM(account_out) AS total_mutation_out");
			$this->db->from("acct_account_balance_detail");
			$this->db->where("account_id", $account_id);
			$this->db->where("MONTH(transaction_date)", $data['month_period']);
			$this->db->where("YEAR(transaction_date)", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			$result = $this->db->get()->row_array();
			return $result['total_mutation_out'];
		}

		public function getTotalAccountIn2($account_id, $data){
			$this->db->select("SUM(account_in) AS total_mutation_in");
			$this->db->from("acct_account_balance_detail");
			$this->db->where("account_id", $account_id);
			// $this->db->where("MONTH(transaction_date)", $data['month_period']);
			$this->db->where("YEAR(transaction_date)", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			$result = $this->db->get()->row_array();
			return $result['total_mutation_in'];
		}

		public function getTotalAccountOut2($account_id, $data){
			$this->db->select("SUM(account_out) AS total_mutation_out");
			$this->db->from("acct_account_balance_detail");
			$this->db->where("account_id", $account_id);
			// $this->db->where("MONTH(transaction_date)", $data['month_period']);
			$this->db->where("YEAR(transaction_date)", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			$result = $this->db->get()->row_array();
			return $result['total_mutation_out'];
		}

		public function getCheckAcctAccountOpeningBalance($month, $year, $branch_id){
			$this->db->select("branch_id, account_id, opening_balance, month_period, year_period");
			$this->db->from("acct_account_opening_balance");
			$this->db->where("month_period", $month);
			$this->db->where("year_period", $year);
			$this->db->where("branch_id", $branch_id);
			return $this->db->get();
		}

		public function getCheckAcctAccountMutation($data){
			$this->db->select("branch_id, account_id, mutation_in_amount, mutation_out_amount, last_balance, month_period, year_period");
			$this->db->from("acct_account_mutation");
			$this->db->where("month_period", $data['month_period']);
			$this->db->where("year_period", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			return $this->db->get();
		}

		public function deleteAcctAccountOpeningBalance($month, $year, $branch_id){
			$this->db->where("month_period", $month);
			$this->db->where("year_period", $year);
			$this->db->where("branch_id", $branch_id);
			if($this->db->delete("acct_account_opening_balance")){
				return true;
			} else {
				return false;
			}
		}

		public function deleteAcctAccountMutation($data){
			$this->db->where("month_period", $data['month_period']);
			$this->db->where("year_period", $data['year_period']);
			$this->db->where("branch_id", $data['branch_id']);
			if($this->db->delete("acct_account_mutation")){
				return true;
			} else {
				return false;
			}
		}

		public function insertAcctAccountOpeningBalance($data){
			if($this->db->insert_batch('acct_account_opening_balance',$data)){
				
				return true;

			} else {
				return false;
			}

		}

		public function insertAcctAccountMutation($data){
			if($this->db->insert_batch('acct_account_mutation',$data)){
				
				return true;

			} else {
				return false;
			}

		}

		public function getAcctProfitLossReport_Top(){
			$this->db->select('acct_profit_loss_report.profit_loss_report_id, acct_profit_loss_report.report_no, acct_profit_loss_report.account_id, acct_profit_loss_report.account_code, acct_profit_loss_report.account_name, acct_profit_loss_report.report_formula, acct_profit_loss_report.report_operator, acct_profit_loss_report.report_type, acct_profit_loss_report.report_tab, acct_profit_loss_report.report_bold');
			$this->db->from('acct_profit_loss_report');
			$this->db->where('acct_profit_loss_report.account_name <> " " ');
			$this->db->where('acct_profit_loss_report.account_type_id', 2);
			$this->db->order_by('acct_profit_loss_report.report_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctProfitLossReport_Bottom(){
			$this->db->select('acct_profit_loss_report.profit_loss_report_id, acct_profit_loss_report.report_no, acct_profit_loss_report.account_id, acct_profit_loss_report.account_code, acct_profit_loss_report.account_name, acct_profit_loss_report.report_formula, acct_profit_loss_report.report_operator, acct_profit_loss_report.report_type, acct_profit_loss_report.report_tab, acct_profit_loss_report.report_bold');
			$this->db->from('acct_profit_loss_report');
			$this->db->where('acct_profit_loss_report.account_name <> " " ');
			$this->db->where('acct_profit_loss_report.account_type_id', 3);
			$this->db->order_by('acct_profit_loss_report.report_no', 'ASC');
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getAcctAccount_List($length, $account_code){
			$this->db->select('acct_account.account_id');
			$this->db->from('acct_account');
			$this->db->where('data_state', 0);
			$this->db->where('LEFT(account_code,'.$length.')', $account_code);
			$result = $this->db->get()->result_array();
			return $result;
		}

		public function getOpeningBalance_Account($account_id, $month, $year, $branch_id){
			$this->db->select('acct_account_opening_balance.opening_balance');
			$this->db->from('acct_account_opening_balance');
			$this->db->where('acct_account_opening_balance.account_id', $account_id);
			$this->db->where('acct_account_opening_balance.branch_id', $branch_id);
			$this->db->where('acct_account_opening_balance.month_period', $month);
			$this->db->where('acct_account_opening_balance.year_period', $year);
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getLastBalance_Account($account_id, $month, $year, $branch_id){
			$this->db->select('acct_account_mutation.last_balance');
			$this->db->from('acct_account_mutation');
			$this->db->where('acct_account_mutation.account_id', $account_id);
			$this->db->where('acct_account_mutation.branch_id', $branch_id);
			$this->db->where('acct_account_mutation.month_period', $month);
			$this->db->where('acct_account_mutation.year_period', $year);
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getCheckProfitLoss($data){
			$this->db->select("branch_id, profit_loss_amount, month_period, year_period");
			$this->db->from("acct_profit_loss");
			$this->db->where("branch_id", $data['branch_id']);
			$this->db->where("month_period", $data['month_period']);
			$this->db->where("year_period", $data['year_period']);
			return $this->db->get();
		}

		public function deleteAcctprofitLoss($data){
			$this->db->where("branch_id", $data['branch_id']);
			$this->db->where("month_period", $data['month_period']);
			$this->db->where("year_period", $data['year_period']);
			if($this->db->delete("acct_profit_loss")){
				return true;
			} else {
				return false;
			}
		}

		public function insertAcctProfitLoss($data){
			if($this->db->insert("acct_profit_loss", $data)){
				return true;
			} else {
				return false;
			}
		}





























		public function getIncomeStatement($id_report){
			$this->db->select('id, id_no, field_name, account_id, type, indent_tab, operator, formula, indent_bold, status');
			$this->db->from('acct_report');
			$this->db->where('id_report', $id_report);
			$this->db->order_by('id_no', 'ASC');
			return $this->db->get()->result_array();
		}

		public function getAccountListParent($length, $account_id){
			$this->db->select('account_id, account_code, account_name');
			$this->db->from('acct_account');
			$this->db->where('LEFT(account_code,'.$length.')', $account_id);
			$this->db->where('account_status', 1);
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getSaldoAccountChild($account_id, $month, $year, $branch_id){
			$this->db->select('acct_account_balance_detail.opening_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.parent_account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			if(!empty($branch_id) && $branch_id != 1){
				$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			}
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.transaction_date', 'ASC');
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}

		public function getSaldoAccountParent($account_id, $month, $year, $branch_id){
			$this->db->select('acct_account_balance_detail.opening_balance');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			if(!empty($branch_id) && $branch_id != 1){
				$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			}
			$this->db->limit(1);
			$this->db->order_by('acct_account_balance_detail.account_balance_detail_id', 'ASC');
			$result = $this->db->get()->row_array();
			return $result['opening_balance'];
		}


		public function getAccountChildAmount($account_id, $month, $year, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount, SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.parent_account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			if(!empty($branch_id) && $branch_id != 1){
				$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			}
			$result = $this->db->get()->row_array();
			return $result;
		}

		public function getAccountParentAmount($account_id, $month, $year, $branch_id){
			$this->db->select('SUM(acct_account_balance_detail.account_in) AS account_in_amount, SUM(acct_account_balance_detail.account_out) AS account_out_amount');
			$this->db->from('acct_account_balance_detail');
			$this->db->join('acct_account','acct_account_balance_detail.account_id = acct_account.account_id');
			$this->db->where('acct_account.account_id', $account_id);
			$this->db->where('MONTH(acct_account_balance_detail.transaction_date)', $month);
			$this->db->where('YEAR(acct_account_balance_detail.transaction_date)', $year);
			if(!empty($branch_id) && $branch_id != 1){
				$this->db->where('acct_account_balance_detail.branch_id', $branch_id);
			}
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getsettingminimalstock(){
			$this->db->select('minimal_stock_warning_percentage, minimal_stock_warning_type')->from("preference_company");
			$this->db->where('company_id = 1');
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getstockperitem($percentage){
			return $this->db->query('SELECT SUM(last_balance) as last_balance, item_name, item_reorder_point FROM ( SELECT invt_item_stock.last_balance, invt_item.item_name, invt_item.item_reorder_point, invt_item.item_id FROM invt_item_stock, invt_item WHERE invt_item_stock.item_id = invt_item.item_id )as ass WHERE last_balance <= (('.$percentage.'/100 * item_reorder_point)+item_reorder_point) GROUP BY item_name
			')->result_array();
		}
		
		public function getstockperwarehouse($percentage){
			return $this->db->query('SELECT invt_item_stock.last_balance, invt_item.item_name, invt_warehouse.warehouse_name FROM invt_item_stock, invt_item, invt_warehouse WHERE invt_item_stock.item_id = invt_item.item_id AND invt_warehouse.warehouse_id = invt_item_stock.warehouse_id AND invt_item_stock.last_balance <= (('.$percentage.'/100 * invt_item.item_reorder_point)+invt_item.item_reorder_point)')->result_array();
		}
		
		public function getsettingexpired(){
			$this->db->select('expired_days_notification')->from("preference_company");
			$this->db->where('company_id = 1');
			$result = $this->db->get()->row_array();
			return $result['expired_days_notification'];
		}

		public function getexpired($start, $end){
			$this->db->select('u.item_id, u.warehouse_id, u.date_in, u.expired_date, u.last_balance, ii.item_name, iw.warehouse_name')->from("invt_item_stock_date as u");
			$this->db->join('invt_item as ii','ii.item_id = u.item_id');
			$this->db->join('invt_warehouse as iw','u.warehouse_id = iw.warehouse_id');
			// if($start != '' && $end != ''){
				// $this->db->where('u.expired_date >= ',$start);
				// $this->db->where('u.expired_date <= ',$end); 
			// }
			$this->db->where('u.expired_date <= ',$end);
			// $this->db->order_by('u.expired_date', 'asc');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getMenu($level){
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu as m');
			// $this->db->join('system_menu_mapping as mm', 'mm.id_menu=m.id_menu');
			// $this->db->where('mm.user_group_level',$level);
			// $this->db->where('m.type','folder');
			$this->db->order_by('m.id_menu','asc');
			$result = $this->db->get()->result_array();
			// print_r($result); exit;
			return $result;
		}
		
		public function getMenu2($level){
			$hasil = $this->db->query("select * from system_menu as m where id_menu in (select DISTINCT(SUBSTR(m.id_menu,1,1)) as id_menu from system_menu as m
			join system_menu_mapping as mm on mm.id_menu=m.id_menu
			where mm.user_group_level='$level')");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getDataParentmenu($id){
			$hasil = $this->db->query("select * from system_menu WHERE id_menu='$id'");
			$hasil = $hasil->row_array();
			return $hasil;
		}
		
		public function getParentMenu($level){
			$hasil = $this->db->query("SELECT distinct(SUBSTR(id_menu,1,1)) as detect from system_menu_mapping where user_group_level='$level'");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getDataMenu($id){
		// print_r($id); exit;
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu as m');
			$this->db->where('m.id',$id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getDataMenu2($id){
		// print_r($id); exit;
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu_not_direct as m');
			$this->db->where('m.id',$id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getDataFolder($id){
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu as m');
			$this->db->where('m.id_menu',$id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getFolder($id){
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu as m');
			$this->db->where('m.id_menu',$id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getIDMenu($class){
			$hasil = $this->db->query("SELECT id_menu from system_menu where id = '$class'");
			$hasil = $hasil->row_array();
			return $hasil['id_menu'];
		}
		
		public function getID($class){
			$hasil = $this->db->query("SELECT id from system_menu where id_menu= '$class'");
			$hasil = $hasil->row_array();
			return $hasil['id'];
		}
		
		public function getSameFolder($level,$index){
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,2) as detect from system_menu_mapping where user_group_level='$level' And id_menu like '$index%'");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getActive($class){			
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,1) as detect from system_menu where id like '".$class."%'");			
			$hasil = $hasil->row_array();			
			if(count($hasil)>0){					
				return $hasil['detect'];			
			}else{			
				return 0;		
			}		
		}		
		
		public function getParentSubMenu($level, $index){
			$hasil = $this->db->query("SELECT b.* from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%'");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getParentSubMenu2($level, $index){			
			$hasil = $this->db->query("select DISTINCT(substr(t.id_menu,1,2)) as id_menu from (SELECT b.id_menu, b.id, b.type, b.text, b.image from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%') as t");		
			$hasil = $hasil->result_array();			
			return $hasil;	
		}				
		
		public function getParentSubMenu3($level, $index){			
			$hasil = $this->db->query("select DISTINCT(substr(t.id_menu,1,3)) as id_menu from (SELECT b.id_menu, b.id, b.type, b.text, b.image from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%') as t");			
			$hasil = $hasil->result_array();			
			return $hasil;		
		}	

		public function getParentSubMenu4($level, $index){			
			$hasil = $this->db->query("select DISTINCT(substr(t.id_menu,1,4)) as id_menu from (SELECT b.id_menu, b.id, b.type, b.text, b.image from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%') as t");
			$hasil = $hasil->result_array();		
			return $hasil;		
		}				
		
		public function getActive2($class){			
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,2) as detect from system_menu where id='".$class."'");			
			$hasil = $hasil->row_array();			
			if(count($hasil)>0){					
				return $hasil['detect'];			
			}else{				
				return 0;		
			}	
		}		
		
		public function getActive3($class){			
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,3) as detect from system_menu where id='".$class."'");		
			$hasil = $hasil->row_array();			
			if(count($hasil)>0){				
				return $hasil['detect'];			
			}else{				
				return 0;		
			}		
		}
	
		public function getSubMenu($level,$id){			$hasil = $this->db->query("select id_menu,id,type,text,image from system_menu where id_menu like '$id%' and type='file'");			$hasil = $hasil->result_array();			return $hasil;		}		
		
		public function getSubMenu2($level){
			$hasil = $this->db->query("select id_menu from system_menu where id_menu like '$level%'");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getLastActivity($user){
			$hasil = $this->db->query("SELECT log_time from system_log_user where username='$user' And id_previllage='1002' Order By log_time DESC LIMIT 0,1");
			$hasil = $hasil->row_array();
			if(count($hasil)>0){
				return $hasil['log_time'];
			}else{
				return '0000-00-00 00:00:00';
			}
		}
		
		public function getAva($username){
			$this->db->select('avatar')->from('system_user');
			$this->db->where('username',$username);
			$result = $this->db->get()->row_array();
			return $result['avatar'];
		}

		public function gettext($id){
			$this->db->select('text')->from('system_menu');
			$this->db->where('id',$id);
			$result = $this->db->get()->row_array();
			return $result['text'];
		}
	}
?>
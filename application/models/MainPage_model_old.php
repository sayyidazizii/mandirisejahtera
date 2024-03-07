<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class MainPage_model extends CI_Model{
		var $tabledepositoaccount = "acct_deposito_account";
		public function __construct(){
			parent::__construct();
			$this->CI = get_instance();
		}

		public function getBranchCode($branch_id){
			$this->db->select('branch_code');
			$this->db->from('core_branch');
			$this->db->where('branch_id', $branch_id);
			$result = $this->db->get()->row_array();
			return $result['branch_code'];
		}

		public function getParentMenu($level){
			$hasil = $this->db->query("SELECT distinct(SUBSTR(id_menu,1,1)) as detect from system_menu_mapping where user_group_level='$level'");
			$hasil = $hasil->result_array();
			return $hasil;
		}

		public function getSystemMenu_Parent($user_group_level){
			$this->db->select('system_menu_mapping.user_group_level, system_menu.menu_level');
			$this->db->distinct();
			$this->db->from('system_menu_mapping');
			$this->db->join('system_menu', 'system_menu_mapping.id_menu = system_menu.id_menu');
			$this->db->where('system_menu_mapping.user_group_level', $user_group_level);
			$result = $this->db->get()->result_array();
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
		
		/* public function getMenu($level){
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu as m');
			// $this->db->join('system_menu_mapping as mm', 'mm.id_menu=m.id_menu');
			// $this->db->where('mm.user_group_level',$level);
			// $this->db->where('m.type','folder');
			$this->db->order_by('m.id_menu','asc');
			$result = $this->db->get()->result_array();
			// print_r($result); exit;
			return $result;
		} */

		public function getMenu($level){
			$this->db->select('m.id_menu,m.id,m.type,m.text,m.image')->from('system_menu as m');
			// $this->db->join('system_menu_mapping as mm', 'mm.id_menu=m.id_menu');
			// $this->db->where('mm.user_group_level',$level);
			$this->db->where('m.type','folder');
			$this->db->or_where('m.type','file');
			$this->db->order_by('m.id_menu','asc');
			$result = $this->db->get()->result_array();
			// print_r($result); exit;
			return $result;
		}
		
		/* public function getMenu2($level){
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
		} */

		public function getMenu2($level){
			$hasil = $this->db->query("select * from system_menu as m where id_menu in (select DISTINCT(SUBSTR(m.id_menu,1,1)) as id_menu from system_menu as m
			join system_menu_mapping as mm on mm.id_menu=m.id_menu
			where mm.user_group_level='$level' and m.type in ('folder','file'))");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getDataParentmenu($id){
			$hasil = $this->db->query("select * from system_menu WHERE id_menu='$id' and type in ('folder','file')");
			$hasil = $hasil->row_array();
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
		
		/* public function getSameFolder($level,$index){
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,2) as detect from system_menu_mapping where user_group_level='$level' And id_menu like '$index%'");
			$hasil = $hasil->result_array();
			return $hasil;
		} */

		public function getSameFolder($level,$index){
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,2) as detect from system_menu_mapping where user_group_level='$level' And id_menu like '$index%' and type in ('folder','file')");
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
		
		public function getAutoDebetCreditsAccount(){
			$this->db->select('credits_account_id, savings_account_id, credits_account_principal_amount, credits_account_interest_amount, credits_account_id, credits_account_period, credits_account_payment_to, credits_payment_period, credits_account_payment_date, payment_type_id, credits_id, credits_account_interest_last_balance, credits_account_accumulated_fines, credits_account_serial');
			$this->db->from('acct_credits_account');
			$this->db->where('acct_credits_account.data_state', 0);
			$this->db->where('acct_credits_account.payment_preference_id', 2);
			$this->db->where('acct_credits_account.credits_approve_status', 1);
			$this->db->where('acct_credits_account.credits_account_status', 0);
			$this->db->where('acct_credits_account.credits_account_payment_date <=', date('Y-m-d'));
			$this->db->order_by('acct_credits_account.credits_account_id','ASC');
			return $this->db->get()->result_array();
		}

		public function getAutoDebetCreditsAccountToken($auto_debet_credits_account_token){
			$this->db->select('auto_debet_credits_account_token');
			$this->db->from('acct_credits_account');
			$this->db->where('auto_debet_credits_account_token', $auto_debet_credits_account_token);
			return $this->db->get()->result_array();
		}
		
		public function getAutoDebetCoreMember(){
			$this->db->select('core_member.member_id, core_member.member_mandatory_savings, core_member.member_mandatory_savings_last_balance,  acct_savings_account.savings_id, acct_savings_account.savings_account_id, acct_savings_account.savings_account_last_balance');
			$this->db->from('core_member');
			$this->db->join('acct_savings_account', 'acct_savings_account.savings_account_id = core_member.member_debet_savings_account_id');
			$this->db->where('core_member.data_state', 0);
			$this->db->order_by('core_member.member_id','ASC');
			return $this->db->get()->result_array();
		}

		public function getAutoDebetCoreMemberToken($auto_debet_member_account_token){
			$this->db->select('auto_debet_member_account_token');
			$this->db->from('core_member');
			$this->db->where('auto_debet_member_account_token', $auto_debet_member_account_token);
			return $this->db->get()->result_array();
		}
		
		public function getCoreMemberTransferMutationLast($member_id){
			$this->db->select('member_transfer_mutation_date');
			$this->db->from('core_member_transfer_mutation');
			$this->db->where('core_member_transfer_mutation.data_state', 0);
			$this->db->where('core_member_transfer_mutation.member_id', $member_id);
			$this->db->order_by('core_member_transfer_mutation.member_transfer_mutation_id','DESC');
			return $this->db->get()->row_array();
		}
		
		public function updateCoreMember($data){
			$this->db->where("member_id",$data['member_id']);
			$query = $this->db->update('core_member', $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}
		//getAcctDepositoAccountExtra_type
		public function getAcctDepositoAccountExtraType($date){
			$this->db->select('acct_deposito_account.deposito_account_extra_type, acct_deposito_account.deposito_account_id, acct_deposito_account.deposito_account_due_date, acct_deposito_account.deposito_account_date, acct_deposito_account.deposito_account_period, acct_deposito_account.deposito_id, acct_deposito_account.deposito_account_nisbah, acct_deposito_account.deposito_account_amount, acct_deposito_account.savings_account_id, acct_deposito_account.member_id, acct_deposito_account.deposito_account_extra_token , acct_deposito_account.deposito_account_closed_date');
			$this->db->from('acct_deposito_account');
			$this->db->where('acct_deposito_account.data_state', 0);
			$this->db->where('acct_deposito_account.deposito_account_extra_type', 1);
			$this->db->where('acct_deposito_account.deposito_account_closed_date', NULL);
			$this->db->where('acct_deposito_account.deposito_account_due_date >=', $date);
			$this->db->order_by('acct_deposito_account.deposito_account_id','ASC');
			return $this->db->get()->result_array();
		}
		//getAcctDepositoAccountExtra_type update
		public function automaticRoleOverDepositoAccountExtraType($data){
			$this->db->where("deposito_account_id",$data['deposito_account_id']);
			$query = $this->db->update($this->tabledepositoaccount, $data);
			if($query){
				return true;
			}else{
				return false;
			}
		}

		public function getDepositoAccountExtraToken($deposito_account_extra_token){
			$this->db->select('deposito_account_extra_token');
			$this->db->from('acct_deposito_account');
			$this->db->where('deposito_account_extra_token', $deposito_account_extra_token);
			return $this->db->get();
		}
		
		public function insertAcctDepositoProfitSharing($data){
			return $query = $this->db->insert('acct_deposito_profit_sharing',$data);
		}

		public function getAcctDepositoProfitSharingCheck($data){
			$this->db->select('acct_deposito_profit_sharing.deposito_profit_sharing_id, acct_deposito_profit_sharing.deposito_account_id, acct_deposito_account.deposito_account_no, acct_deposito_account.member_id, core_member.member_name');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('core_member','acct_deposito_profit_sharing.member_id = core_member.member_id');
			$this->db->join('acct_deposito_account','acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_account_id', $data['deposito_account_id']);
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date', $data['deposito_profit_sharing_due_date']);
			$result = $this->db->get();
			return $result;
		}
		/* public function getParentSubMenu($level, $index){
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
		}	 */
		
		public function getParentSubMenu($level, $index){
			$hasil = $this->db->query("SELECT b.* from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%' and type in ('folder','file')");
			$hasil = $hasil->result_array();
			return $hasil;
		}
		
		public function getParentSubMenu2($level, $index){			
			$hasil = $this->db->query("select DISTINCT(substr(t.id_menu,1,2)) as id_menu from (SELECT b.id_menu, b.id, b.type, b.text, b.image from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%' and type in ('folder','file')) as t");		
			$hasil = $hasil->result_array();			
			return $hasil;	
		}				
		
		public function getParentSubMenu3($level, $index){			
			$hasil = $this->db->query("select DISTINCT(substr(t.id_menu,1,3)) as id_menu from (SELECT b.id_menu, b.id, b.type, b.text, b.image from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%' and type in ('folder','file')) as t");			
			$hasil = $hasil->result_array();			
			return $hasil;		
		}	

		public function getParentSubMenu4($level, $index){			
			$hasil = $this->db->query("select DISTINCT(substr(t.id_menu,1,4)) as id_menu from (SELECT b.id_menu, b.id, b.type, b.text, b.image from system_menu_mapping as a, system_menu as b where user_group_level='$level' and a.id_menu=b.id_menu and a.id_menu like '$index%' and type in ('folder','file')) as t");
			$hasil = $hasil->result_array();		
			return $hasil;		
		}
		
		public function getActive2($class){			
			$hasil = $this->db->query("SELECT SUBSTR(id_menu,1,2) as detect from system_menu where id='".$class."'");			
			$hasil = $hasil->row_array();		
			if(isset($hasil)){
				if(count($hasil)>0){					
					return $hasil['detect'];			
				}else{				
					return 0;		
				}	
			} else {
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
		
		/* public function getSubMenu2($level){
			$hasil = $this->db->query("select id_menu from system_menu where id_menu like '$level%'");
			$hasil = $hasil->result_array();
			return $hasil;
		} */

		public function getSubMenu2($level){
			$hasil = $this->db->query("select id_menu from system_menu where id_menu like '$level%' and type in ('folder','file')");
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

		public function getAcctDepositoProfitSharing(){
			$this->db->select('* , acct_deposito_account.deposito_account_status');
			$this->db->from('acct_deposito_profit_sharing');
			$this->db->join('acct_deposito_account', 'acct_deposito_profit_sharing.deposito_account_id = acct_deposito_account.deposito_account_id');
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_due_date = CURDATE()');
			$this->db->where('acct_deposito_profit_sharing.deposito_profit_sharing_status', 0);
			$this->db->where('acct_deposito_account.data_state', 0);
			return $this->db->get()->result_array();
		}
	}
?>
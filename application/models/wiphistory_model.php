<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class wiphistory_model extends CI_Model {
		var $table = "production_wip_history";
		
		public function wiphistory_model(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function get_list($start_date, $end_date, $item_id){
			/**if($start_date != '' && $end_date != ''){
				if($item_id != ''){
					$hasil = $this->db->query("Select tabel.* from (
						SELECT a.* from production_wip_history as a where a.created_on=(select MAX(t.created_on) from production_wip_history as t where t.item_id=a.item_id) 
						) as tabel where tabel.item_id='$item_id' and (tabel.wip_history_date >= '$start_date' and tabel.wip_history_date <= '$end_date')");
				}else{
					$hasil = $this->db->query("Select tabel.* from (
						SELECT a.* from production_wip_history as a where a.created_on=(select MAX(t.created_on) from production_wip_history as t where t.item_id=a.item_id) 
						) as tabel where (tabel.wip_history_date >='$start_date' and tabel.wip_history_date <= '$end_date')");
				}
			}else if($item_id != ''){
				if($start_date != '' && $end_date != ''){
					$hasil = $this->db->query("Select tabel.* from (
						SELECT a.* from production_wip_history as a where a.created_on=(select MAX(t.created_on) from production_wip_history as t where t.item_id=a.item_id) 
						) as tabel where tabel.item_id='$item_id' and (tabel.wip_history_date >='$start_date' and tabel.wip_history_date <= '$end_date')");
				}else{
					$hasil = $this->db->query("Select tabel.* from (
						SELECT a.* from production_wip_history as a where a.created_on=(select MAX(t.created_on) from production_wip_history as t where t.item_id=a.item_id) 
						) as tabel where tabel.item_id='$item_id'");
				}
			}else{
				$hasil = $this->db->query("Select tabel.* from (
				SELECT a.* from production_wip_history as a where a.created_on=(select MAX(t.created_on) from production_wip_history as t where t.item_id=a.item_id) 
				) as tabel");
			}			
			return $hasil;*/
			$this->db->select('*');
			$this->db->from('production_wip_history');
			$this->db->where('wip_history_date >=', $start_date);
			$this->db->where('wip_history_date <=', $end_date);
			if($item_id != ''){
				$this->db->where('item_id', $item_id);
			}
			$this->db->group_by('wip_history_date');
			$this->db->order_by('wip_history_date', 'desc');
			$result = $this->db->get();
			// print_r($this->db->last_query());exit;
			return $result;
		}
		
		public function getitem(){
			$this->db->select('item_id, item_name');
			$this->db->from('invt_item');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			return $result->result_array();
		}
		
		public function getitemname($id){
			$this->db->select('item_name');
			$this->db->from('invt_item');
			$this->db->where('item_id',$id);
			$result = $this->db->get()->row_array();
			return $result['item_name'];
		}
		
		public function getwarehousename($id){
			$this->db->select('warehouse_name');
			$this->db->from('invt_warehouse');
			$this->db->where('warehouse_id',$id);
			$result = $this->db->get()->row_array();
			return $result['warehouse_name'];
		}
		
		public function getdetail($id){
			$this->db->select('*');
			$this->db->from('production_wip_history');
			$this->db->where('wip_history_id', $id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getitemunit($item_id){
			$this->db->select('item_unit_id');
			$this->db->from('invt_item');
			$this->db->where('item_id', $item_id);
			$result = $this->db->get()->row_array();
			return $result['item_unit_id'];
		}
		
		public function getproductionactivityno($id){
			$this->db->select('production_activity_no');
			$this->db->from('production_activity');
			$this->db->where('production_activity_id',$id);
			$result = $this->db->get()->row_array();
			return $result['production_activity_no'];
		}
		
		public function getdetailhistorywip($date, $item_id){
			/**$this->db->select('*');
			$this->db->from('production_wip_history');
			$this->db->where('wip_history_date', $date);
			if($item_id != ''){
				$this->db->where('item_id', $item_id);
			}
			$this->db->group_by('item_id');
			$this->db->order_by('created_on', 'desc');
			$result = $this->db->get()->result_array();	
			// print_r($this->db->last_query());exit;
			return $result;*/
			if($item_id != ''){
				$hasil = $this->db->query("select * from (
					select * from production_wip_history where wip_history_date='$date' order by created_on desc) as a where item_id = '$item_id' group by item_id");
			}else{
				$hasil = $this->db->query("select * from (
					select * from production_wip_history where wip_history_date='$date' order by created_on desc) as a group by item_id");
			}
			
			return $hasil->result_array();
		}
		
		public function getopeningbalance($id){
			$this->db->select('quantity_opening_balance');
			$this->db->from('production_wip_history');
			$this->db->where('wip_history_id', $id);
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_opening_balance'];
		}
		
		public function getopeningbalancebyitemanddate($id,$date){
			// print_r($date);exit;
			$this->db->select('quantity_wip');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			$this->db->where('wip_history_date', $date);
			$this->db->order_by('created_on', 'desc');
			$this->db->limit(1);
			$result=$this->db->get()->row_array();
			return $result['quantity_wip'];
		}
		
		public function getopeningbalancebyitemanddate2($id,$date){
			// print_r($date);exit;
			$this->db->select('quantity_wip');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			// $this->db->where('wip_history_date', $date);
			$this->db->where('wip_history_date < ', $date);
			$this->db->order_by('created_on', 'desc');
			$this->db->limit(1);
			$result=$this->db->get()->row_array();
			return $result['quantity_wip'];
		}
		
		public function getqtywipopeningbalance($id){
			/* $this->db->select('opening_balance_wip_quantity');
			$this->db->from('production_opening_balance_wip');
			$this->db->where('item_id',$id);
			$this->db->where('data_state','0');
			$result = $this->db->get()->row_array();
			// print_r($this->db->last_query()); exit;
			return $result['opening_balance_wip_quantity']; */
			$hasil = $this->db->query("Select tabel.opening_balance_wip_quantity from (
						SELECT a.* from production_opening_balance_wip as a where a.created_on=(select MAX(t.created_on) from production_opening_balance_wip as t where t.item_id=a.item_id) 
						) as tabel where tabel.item_id='$id' and data_state='0'");
			$return = $hasil->row_array();
			return $return['opening_balance_wip_quantity'];
		}
		
		public function getqtyopeningbalancewipfromhistory($id,$date){
			$this->db->select('quantity_opening_balance');
			$this->db->from('production_wip_history');
			$this->db->where('item_id',$id);
			$this->db->where('wip_history_date', $date);
			$this->db->where('data_state', '0');
			$this->db->order_by('created_on','desc');
			$this->db->order_by('wip_history_id', 'desc');
			$this->db->limit('1');
			$result = $this->db->get()->row_array();
			// print_r($this->db->last_query()); exit;
			return $result['quantity_opening_balance'];
		}
		
		public function getqty_wipbyitemanddate($id,$date){
			// print_r($date);exit;
			$this->db->select('quantity_wip');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			$this->db->where('wip_history_date', $date);
			$this->db->where('data_state', '0');
			$this->db->order_by('created_on', 'desc');
			$this->db->order_by('wip_history_id', 'desc');
			$this->db->limit(1);
			$result=$this->db->get()->row_array();
			return $result['quantity_wip'];
		}
		
		public function getquantitywip($id){
			$this->db->select('quantity_wip');
			$this->db->from('production_wip_history');
			$this->db->where('wip_history_id', $id);
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_wip'];
		}
		
		/* public function getquantityused($id, $date){
			$this->db->select_sum('quantity_used');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			$this->db->where('wip_history_date', $date);
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_used'];
		} */
		
		public function getquantityused($id, $date){
			/* $this->db->select_sum('a.quantity_used');
			$this->db->from('production_activity_material as a');
			$this->db->join('production_wip_history as b','b.production_activity_id = a.production_activity_id');
			$this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('a.item_id', $id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('c.data_state', '0');
			// $this->db->order_by('created_on', 'desc');
			$this->db->group_by('a.production_activity_id');
			$result=$this->db->get()->row_array();
			return $result['quantity_used']; */
			// print_r($id); exit;
			$query = $this->db->query("SELECT SUM(`tabel`.`quantity_used`) AS quantity_used from (select a.production_activity_id, a.item_id, a.quantity_used, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$id."' AND `c`.`production_activity_date` = '".$date."' and c.data_state='0'
								GROUP BY `a`.`production_activity_id`
								) as tabel");
								// print_r($this->db->last_query()); exit;
			$result=$query->row_array();
			if($result['quantity_used']!=''){
				$result['quantity_used'] = $result['quantity_used'];
			} else {
				$result['quantity_used'] = 0;
			}
			return $result['quantity_used'];
		}
		
		public function getquantityrelease($item_id, $date){
			/* $this->db->select_sum('a.quantity_release1');
			$this->db->from('production_activity_material as a');
			$this->db->join('production_wip_history as b','b.production_activity_id = a.production_activity_id');
			$this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('a.item_id', $item_id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('c.data_state', '0');
			// $this->db->order_by('created_on', 'desc');
			$this->db->group_by('a.production_activity_id');
			$result=$this->db->get()->row_array();
			return $result['quantity_release1']; */
			$query = $this->db->query("SELECT SUM(`tabel`.`quantity_release1`) AS quantity_release1 from (select a.production_activity_id, a.item_id, a.quantity_release1, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_wip_history` as b
								ON `b`.`production_activity_id` = `a`.`production_activity_id` JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$item_id."' AND `b`.`wip_history_date` = '".$date."'
								GROUP BY `a`.`production_activity_id`
								) as tabel");
			$result=$query->row_array();
			return $result['quantity_release1'];
		
		}
		
		public function getquantityreleasefromrelease($item_id, $date){
			$this->db->select_sum('b.quantity_release1');
			$this->db->from('production_wip_history as b');
			// $this->db->join('production_wip_history as b');
			// $this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('b.item_id', $item_id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('b.production_activity_id', '0');
			// $this->db->order_by('created_on', 'desc');
			$this->db->group_by('b.production_activity_id');
			$result=$this->db->get()->row_array();
			return $result['quantity_release1'];
			/* $query = $this->db->query("SELECT SUM(`tabel`.`quantity_release1`) AS quantity_release1 from (select a.production_activity_id, a.item_id, a.quantity_release1, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_wip_history` as b
								ON `b`.`production_activity_id` = `a`.`production_activity_id` JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$item_id."' AND `b`.`wip_history_date` = '".$date."'
								GROUP BY `a`.`production_activity_id`
								) as tabel");
			$result=$query->row_array();
			return $result['quantity_release1']; */
		
		}
		
		public function getquantityreturn($item_id, $date){
			$this->db->select_sum('b.quantity_return');
			$this->db->from('production_wip_history as b');
			// $this->db->join('production_wip_history as b');
			// $this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('b.item_id', $item_id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('b.production_activity_id', '0');
			// $this->db->order_by('created_on', 'desc');
			$this->db->group_by('b.production_activity_id');
			$result=$this->db->get()->row_array();
			return $result['quantity_return'];
			/* $query = $this->db->query("SELECT SUM(`tabel`.`quantity_release1`) AS quantity_release1 from (select a.production_activity_id, a.item_id, a.quantity_release1, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_wip_history` as b
								ON `b`.`production_activity_id` = `a`.`production_activity_id` JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$item_id."' AND `b`.`wip_history_date` = '".$date."'
								GROUP BY `a`.`production_activity_id`
								) as tabel");
			$result=$query->row_array();
			return $result['quantity_release1']; */
		
		}
		
		/* public function getquantityrejectfilling($id, $date){
			$this->db->select_sum('quantity_reject_filling');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			$this->db->where('wip_history_date', $date);
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_reject_filling'];
		} */
		
		public function getquantityrejectfilling($id, $date){
			/* $this->db->select_sum('a.quantity_reject_filling');
			$this->db->from('production_activity_material as a');
			$this->db->join('production_wip_history as b','b.production_activity_id = a.production_activity_id');
			$this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('a.item_id', $id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('c.data_state', '0');
			// $this->db->order_by('created_on', 'desc');
			$this->db->group_by('a.production_activity_id');
			$result=$this->db->get()->row_array();
			return $result['quantity_reject_filling']; */
			
			/* $query = $this->db->query("SELECT SUM(`tabel`.`quantity_reject_filling`) AS quantity_reject_filling from (select a.production_activity_id, a.item_id, a.quantity_reject_filling, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_wip_history` as b
								ON `b`.`production_activity_id` = `a`.`production_activity_id` JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$id."' AND `b`.`wip_history_date` = '".$date."' AND c.data_state='0'
								GROUP BY `a`.`production_activity_id`
								) as tabel"); */
			$query = $this->db->query("SELECT SUM(`a`.`quantity_reject_filling`) AS quantity_reject_filling from production_wip_history as a
										WHERE `a`.`item_id` = '".$id."' AND `a`.`wip_history_date` = '".$date."' 
										AND a.data_state='0' GROUP BY `a`.`production_activity_id`");
			$result=$query->row_array();
			// print_r($this->db->last_query()); exit;
			if($result['quantity_reject_filling']!=''){
				$result['quantity_reject_filling'] = $result['quantity_reject_filling'];
			} else {
				$result['quantity_reject_filling'] = 0;
			}
			return $result['quantity_reject_filling'];
		}
		
		/* public function getquantityrejectwarehouse($id,$date){
			$this->db->select_sum('quantity_reject_warehouse');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			$this->db->where('wip_history_date', $date);
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_reject_warehouse'];
		} */
		
		public function getquantityrejectwarehouse($id,$date){
			/* $this->db->select_sum('a.quantity_reject_warehouse');
			$this->db->from('production_activity_material as a');
			$this->db->join('production_wip_history as b','b.production_activity_id = a.production_activity_id');
			$this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('a.item_id', $id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('c.data_state', '0');
			// $this->db->order_by('created_on', 'desc');
			$this->db->group_by('a.production_activity_id');
			$result=$this->db->get()->row_array();
			return $result['quantity_reject_warehouse']; */
			
			/* $query = $this->db->query("SELECT SUM(`tabel`.`quantity_reject_warehouse`) AS quantity_reject_warehouse from (select a.production_activity_id, a.item_id, a.quantity_reject_warehouse, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_wip_history` as b
								ON `b`.`production_activity_id` = `a`.`production_activity_id` JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$id."' AND `b`.`wip_history_date` = '".$date."' AND c.data_state='0'
								GROUP BY `a`.`production_activity_id`
								) as tabel"); */
			$query = $this->db->query("SELECT SUM(`a`.`quantity_reject_warehouse`) AS quantity_reject_warehouse from production_wip_history as a
										WHERE `a`.`item_id` = '".$id."' AND `a`.`wip_history_date` = '".$date."' 
										AND a.data_state='0' GROUP BY `a`.`production_activity_id`");
			$result=$query->row_array();
			if($result['quantity_reject_warehouse']!=''){
				$result['quantity_reject_warehouse'] = $result['quantity_reject_warehouse'];
			} else {
				$result['quantity_reject_warehouse'] = 0;
			}
			return $result['quantity_reject_warehouse'];
		}
		
		/* public function getquantityrejectsupplier($id,$date){
			$this->db->select_sum('quantity_reject_supplier');
			$this->db->from('production_wip_history');
			$this->db->where('item_id', $id);
			$this->db->where('wip_history_date', $date);
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_reject_supplier'];
		} */
		
		public function getquantityrejectsupplier($id,$date){
			/* $this->db->select_sum('a.quantity_reject_supplier');
			$this->db->from('production_activity_material as a');
			$this->db->join('production_wip_history as b','b.production_activity_id = a.production_activity_id');
			$this->db->join('production_activity as c','c.production_activity_id = a.production_activity_id');
			$this->db->where('a.item_id', $id);
			$this->db->where('b.wip_history_date', $date);
			$this->db->where('c.data_state', '0');
			$this->db->group_by('a.production_activity_id');
			// $this->db->order_by('created_on', 'desc');
			$result=$this->db->get()->row_array();
			return $result['quantity_reject_supplier']; */
			
			/* $query = $this->db->query("SELECT SUM(`tabel`.`quantity_reject_supplier`) AS quantity_reject_supplier from (select a.production_activity_id, a.item_id, a.quantity_reject_supplier, c.created_on, c.data_state FROM (`production_activity_material` as a) 
								JOIN `production_wip_history` as b
								ON `b`.`production_activity_id` = `a`.`production_activity_id` JOIN `production_activity` as c
								ON `c`.`production_activity_id` = `a`.`production_activity_id`  WHERE `a`.`item_id` = '".$id."' AND `b`.`wip_history_date` = '".$date."' AND c.data_state='0'
								GROUP BY `a`.`production_activity_id`
								) as tabel"); */
			$query = $this->db->query("SELECT SUM(`a`.`quantity_reject_supplier`) AS quantity_reject_supplier from production_wip_history as a
										WHERE `a`.`item_id` = '".$id."' AND `a`.`wip_history_date` = '".$date."' 
										AND a.data_state='0' GROUP BY `a`.`production_activity_id`");
			$result=$query->row_array();
			if($result['quantity_reject_supplier']!=''){
				$result['quantity_reject_supplier'] = $result['quantity_reject_supplier'];
			} else {
				$result['quantity_reject_supplier'] = 0;
			}
			return $result['quantity_reject_supplier'];
		}
	}
?>
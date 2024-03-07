<?php
	class productionactivitycontrol_model extends CI_Model {

		public function productionactivitycontrol_model(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		function getCompany(){
			$this->db->select('refreshrate')->from("preference_company");
			$this->db->limit(1,0);
			$result=$this->db->get()->row_array();
			return $result['refreshrate'];
		}
		public function getitemcategory(){
			$this->db->select('item_category_id,item_category_name')->from('invt_item_category');
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getitem(){
			$this->db->select('item_id,item_name')->from('invt_item');
			$result = $this->db->get()->result_array();
			return $result;
		}		
		
		public function getwarehouse(){
			$this->db->select('warehouse_id,warehouse_name')->from('invt_warehouse');
			$result = $this->db->get()->result_array();
			return $result;
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

		public function getopeningstockdateawal($tanggalpertama, $expired, $item_id, $warehouse_id){
			$this->db->select_sum('last_balance');
			$this->db->from('invt_item_opening_stock_date');
			$this->db->where('item_id',$item_id);
			$this->db->where('warehouse_id',$warehouse_id);
			$this->db->where('expired_date > CAST("'.$expired.'" AS DATE)');
			$this->db->where('date_in <= CAST("'.$tanggalpertama.'" AS DATE)');
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getopeningstockdate($tanggalpertama, $item_id, $warehouse_id){
			$this->db->select_sum('last_balance');
			$this->db->from('invt_item_opening_stock_date');
			$this->db->where('item_id',$item_id);
			$this->db->where('warehouse_id',$warehouse_id);
			$this->db->where('date_in',$date);
			$result = $this->db->get()->row_array();
			return $result['last_balance'];
		}

		public function getproductionresultawal($date,$item_id,$warehouse_id){
			$this->db->select_sum('production_result_item.quantity');
			$this->db->from('production_result_item');
			$this->db->join('production_result','production_result_item.production_result_id = production_result.production_result_id');
			$this->db->where('production_result_item.item_id',$item_id);
			$this->db->where('production_result.production_result_date < ',$date);
			$this->db->where('production_result.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getproductionresult($date,$item_id,$warehouse_id){
			$this->db->select_sum('production_result_item.quantity');
			$this->db->from('production_result_item');
			$this->db->join('production_result','production_result_item.production_result_id = production_result.production_result_id');
			$this->db->where('production_result_item.item_id',$item_id);
			$this->db->where('production_result.production_result_date',$date);
			$this->db->where('production_result.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehouseinawal($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_in_item.quantity');
			$this->db->from('invt_warehouse_in_item');
			$this->db->join('invt_warehouse_in','invt_warehouse_in_item.warehouse_in_id = invt_warehouse_in.warehouse_in_id');
			$this->db->where('invt_warehouse_in_item.item_id',$item_id);
			$this->db->where('invt_warehouse_in.warehouse_in_date < ',$date);
			$this->db->where('invt_warehouse_in.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehousein($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_in_item.quantity');
			$this->db->from('invt_warehouse_in_item');
			$this->db->join('invt_warehouse_in','invt_warehouse_in_item.warehouse_in_id = invt_warehouse_in.warehouse_in_id');
			$this->db->where('invt_warehouse_in_item.item_id',$item_id);
			$this->db->where('invt_warehouse_in.warehouse_in_date',$date);
			$this->db->where('invt_warehouse_in.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehouseoutawal($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_out_item.quantity');
			$this->db->from('invt_warehouse_out_item');
			$this->db->join('invt_warehouse_out','invt_warehouse_out_item.warehouse_out_id = invt_warehouse_out.warehouse_out_id');
			$this->db->where('invt_warehouse_out_item.item_id',$item_id);
			$this->db->where('invt_warehouse_out.warehouse_out_date < ',$date);
			$this->db->where('invt_warehouse_out.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehouseout($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_out_item.quantity');
			$this->db->from('invt_warehouse_out_item');
			$this->db->join('invt_warehouse_out','invt_warehouse_out_item.warehouse_out_id = invt_warehouse_out.warehouse_out_id');
			$this->db->where('invt_warehouse_out_item.item_id',$item_id);
			$this->db->where('invt_warehouse_out.warehouse_out_date',$date);
			$this->db->where('invt_warehouse_out.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}
		
		public function getwarehousetransferinawal($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_transfer_item.quantity');
			$this->db->from('invt_warehouse_transfer_item');
			$this->db->join('invt_warehouse_transfer','invt_warehouse_transfer_item.warehouse_transfer_id = invt_warehouse_transfer.warehouse_transfer_id');
			$this->db->where('invt_warehouse_transfer_item.item_id',$item_id);
			$this->db->where('invt_warehouse_transfer.warehouse_transfer_date',$date);
			$this->db->where('invt_warehouse_transfer.warehouse_to_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehousetransferin($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_transfer_item.quantity');
			$this->db->from('invt_warehouse_transfer_item');
			$this->db->join('invt_warehouse_transfer','invt_warehouse_transfer_item.warehouse_transfer_id = invt_warehouse_transfer.warehouse_transfer_id');
			$this->db->where('invt_warehouse_transfer_item.item_id',$item_id);
			$this->db->where('invt_warehouse_transfer.warehouse_transfer_date',$date);
			$this->db->where('invt_warehouse_transfer.warehouse_to_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehousetransferoutawal($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_transfer_item.quantity');
			$this->db->from('invt_warehouse_transfer_item');
			$this->db->join('invt_warehouse_transfer','invt_warehouse_transfer_item.warehouse_transfer_id = invt_warehouse_transfer.warehouse_transfer_id');
			$this->db->where('invt_warehouse_transfer_item.item_id',$item_id);
			$this->db->where('invt_warehouse_transfer.warehouse_transfer_date < ',$date);
			$this->db->where('invt_warehouse_transfer.warehouse_from_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getwarehousetransferout($date,$item_id,$warehouse_id){
			$this->db->select_sum('invt_warehouse_transfer_item.quantity');
			$this->db->from('invt_warehouse_transfer_item');
			$this->db->join('invt_warehouse_transfer','invt_warehouse_transfer_item.warehouse_transfer_id = invt_warehouse_transfer.warehouse_transfer_id');
			$this->db->where('invt_warehouse_transfer_item.item_id',$item_id);
			$this->db->where('invt_warehouse_transfer.warehouse_transfer_date',$date);
			$this->db->where('invt_warehouse_transfer.warehouse_from_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getforecastfrommarketing($date,$item_id,$warehouse_id){
			$this->db->select_sum('production_work_order_item.quantity');
			$this->db->from('production_work_order_item');
			$this->db->join('production_work_order','production_work_order_item.work_order_id = production_work_order.work_order_id');
			$this->db->where('production_work_order_item.item_id',$item_id);
			$this->db->where('production_work_order.status','1');
			$this->db->where('production_work_order.work_order_start_date',$date);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getworkorderawal($date,$item_id,$warehouse_id){
			$this->db->select_sum('production_work_order_item.quantity');
			$this->db->from('production_work_order_item');
			$this->db->join('production_work_order','production_work_order_item.work_order_id = production_work_order.work_order_id');
			$this->db->where('production_work_order_item.item_id',$item_id);
			$this->db->where('production_work_order.status','1');
			$this->db->where('production_work_order.work_order_start_date < ',$date);
			$this->db->where('production_work_order.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getworkorder($date,$item_id,$warehouse_id){
			$this->db->select_sum('production_work_order_item.quantity');
			$this->db->from('production_work_order_item');
			$this->db->join('production_work_order','production_work_order_item.work_order_id = production_work_order.work_order_id');
			$this->db->where('production_work_order_item.item_id',$item_id);
			$this->db->where('production_work_order.status','1');
			$this->db->where('production_work_order.work_order_start_date',$date);
			$this->db->where('production_work_order.warehouse_id',$warehouse_id);
			$result = $this->db->get()->row_array();
			return $result['quantity'];
		}

		public function getsafetystock($date,$item_id){
			$this->db->select('production_safety_stock_item.safety_stock_quantity');
			$this->db->from('production_safety_stock_item');
			$this->db->join('production_safety_stock','production_safety_stock_item.safety_stock_id = production_safety_stock.safety_stock_id');
			$this->db->where('production_safety_stock.item_id',$item_id);
			$this->db->where('production_safety_stock.start_date <= ',$date);
			$this->db->where('production_safety_stock.end_date >= ',$date);
			$this->db->order_by('production_safety_stock.safety_stock_id','desc');
			$this->db->limit('1');
			$result = $this->db->get()->row_array();
			return $result['safety_stock_quantity'];
		}
		
		public function getestimasiwaktu($thisDate, $item_id){
			$tahun = date("Y",strtotime($thisDate));
			$bulan = date("m",strtotime($thisDate));
			$this->db->select('production_resource_requirement_item.*')->from('production_resource_requirement_item');
			$this->db->join('production_resource_requirement','production_resource_requirement_item.resource_requirement_id = production_resource_requirement.resource_requirement_id');
			$this->db->where('production_resource_requirement.resource_requirement_start_period LIKE "'.$tahun.'%"');
			$this->db->where('production_resource_requirement.item_id',$item_id);
			$this->db->where('production_resource_requirement_item.resource_requirement_type','2');
			$result = $this->db->get()->row_array();
			if($bulan==1){
				$mesin = intval($result['quantity_1']);
			}else if($bulan==2){
				$mesin = intval($result['quantity_2']);
			}else if($bulan==3){
				$mesin = intval($result['quantity_3']);
			}else if($bulan==4){
				$mesin = intval($result['quantity_4']);
			}else if($bulan==5){
				$mesin = intval($result['quantity_5']);
			}else if($bulan==6){
				$mesin = intval($result['quantity_6']);
			}else if($bulan==7){
				$mesin = intval($result['quantity_7']);
			}else if($bulan==8){
				$mesin = intval($result['quantity_8']);
			}else if($bulan==9){
				$mesin = intval($result['quantity_9']);
			}else if($bulan==10){
				$mesin = intval($result['quantity_10']);
			}else if($bulan==11){
				$mesin = intval($result['quantity_11']);
			}else if($bulan==12){
				$mesin = intval($result['quantity_12']);
			}
			
			$detailmachine = $this->getmachinedetail($mesin);
			$efisiensi = $this->getefficiency($detailmachine['machine_id'],$thisDate);
			if($detailmachine['machine_capacity']*$efisiensi['machine_efficiency']!=0){
				$nilainya = 1/$detailmachine['machine_capacity']*$efisiensi['machine_efficiency']/24;
			}else{
				$nilainya = 0;
			}

			return $nilainya;
		}
		
		public function getmachinedetail($id){
			$this->db->select('*')->from('core_machine');
			$this->db->where('machine_id',$id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getefficiency($machine_id,$date){
			$result = $this->db->query('SELECT * FROM `production_machine_efficiency` WHERE machine_efficiency_date <= "'.$date.'" AND machine_id = "'.$machine_id.'" ORDER BY machine_efficiency_date desc LIMIT 1 ')->row_array();
			return $result;
		}
		
		public function getdataitem($tanggalpertama,$tanggalterakhir){
			$this->db->select('pri.item_id, pr.warehouse_id');
			$this->db->from('production_result_item as pri');
			$this->db->join('production_result as pr','pri.production_result_id = pr.production_result_id');
			$this->db->where('pr.production_result_date >= ',$tanggalpertama);
			$this->db->where('pr.production_result_date <= ',$tanggalterakhir);
			$this->db->group_by('pri.item_id');
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}
		
		public function getdatawarehouse($tanggalpertama,$tanggalterakhir){
			$this->db->select('pr.warehouse_id');
			$this->db->from('production_result_item as pri');
			$this->db->join('production_result as pr','pri.production_result_id = pr.production_result_id');
			$this->db->where('pr.production_result_date >= ',$tanggalpertama);
			$this->db->where('pr.production_result_date <= ',$tanggalterakhir);
			$this->db->group_by('pr.warehouse_id');
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}
	}
?>
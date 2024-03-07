<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	class salesreturnreport_model extends CI_Model {
		var $table = "sales_return";
		
		public function salesreturnreport_model(){
			parent::__construct();
			$this->CI = get_instance();
		}
		
		public function getSalesReturnReport($start_date, $end_date, $section_id, $warehouse_id){
			$this->db->select('sales_return.sales_return_id, sales_return.section_id, core_section.section_name, sales_return.warehouse_id, invt_warehouse.warehouse_name, sales_return.customer_id, sales_customer.customer_name, sales_return.sales_return_date, sales_return_item.item_id, invt_item.item_name, sales_return_item.sales_return_item_id, sales_return_item.item_unit_id, invt_item_unit.item_unit_code, sales_return_item.item_unit_price, sales_return_item.subtotal_amount, sales_return_item.item_batch_number, sales_return_item.quantity, sales_return_item.sales_delivery_note_id, sales_delivery_note.sales_delivery_note_no');
			$this->db->from('sales_return_item');
			$this->db->join('sales_return', 'sales_return_item.sales_return_id = sales_return.sales_return_id');
			$this->db->join('invt_warehouse', 'sales_return.warehouse_id = invt_warehouse.warehouse_id');
			$this->db->join('core_section', 'sales_return.section_id = core_section.section_id');
			$this->db->join('sales_customer', 'sales_return.customer_id = sales_customer.customer_id');
			$this->db->join('invt_item', 'sales_return_item.item_id = invt_item.item_id');
			$this->db->join('invt_item_unit', 'sales_return_item.item_unit_id = invt_item_unit.item_unit_id');
			$this->db->join('sales_delivery_note', 'sales_return_item.sales_delivery_note_id = sales_delivery_note.sales_delivery_note_id');
			if($start_date != ''){
				$this->db->where('sales_return.sales_return_date >= ', $start_date);
			}
			if($end_date != ''){
				$this->db->where('sales_return.sales_return_date <= ', $end_date);
			}
			if($warehouse_id != ''){
				$this->db->where('sales_return.warehouse_id', $warehouse_id);
			}
			if($section_id != ''){
				$this->db->where('sales_return.section_id', $section_id);
			}
			$this->db->where('sales_return.data_state', 0);
			$this->db->order_by('sales_return.sales_return_date', 'ASC');
			$result = $this->db->get()->result_array();
			// print_r($result);exit;
			return $result;
		}

		public function getCoreSection(){
			$this->db->select('section_id, section_name');
			$this->db->from('core_section');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getInvtWarehouse(){
			$this->db->select('warehouse_id, warehouse_name');
			$this->db->from('invt_warehouse');
			$this->db->where('data_state', 0);
			return $this->db->get()->result_array();
		}

		public function getMinID($sales_return_id){
			$this->db->select_min('sales_return_item_id');
			$this->db->from('sales_return_item');
			$this->db->where('sales_return_id',$sales_return_id);
			$result=$this->db->get()->row_array();
			// print_r($this->db->last_query());exit;
			return $result['sales_return_item_id'];
		}

		public function getExportSalesreturnReport($start_date, $end_date, $section_id, $warehouse_id){
			$this->db->select('sales_return.sales_return_id, sales_return.section_id, core_section.section_name, sales_return.warehouse_id, invt_warehouse.warehouse_name, sales_return.customer_id, sales_customer.customer_name, sales_return.sales_return_date, sales_return_item.item_id, invt_item.item_name, sales_return_item.item_unit_id, invt_item_unit.item_unit_code, sales_return_item.item_unit_price, sales_return_item.subtotal_amount, sales_return_item.item_batch_number, sales_return_item.quantity, sales_return_item.sales_delivery_note_id, sales_delivery_note.sales_delivery_note_no');
			$this->db->from('sales_return_item');
			$this->db->join('sales_return', 'sales_return_item.sales_return_id = sales_return.sales_return_id');
			$this->db->join('invt_warehouse', 'sales_return.warehouse_id = invt_warehouse.warehouse_id');
			$this->db->join('core_section', 'sales_return.section_id = core_section.section_id');
			$this->db->join('sales_customer', 'sales_return.customer_id = sales_customer.customer_id');
			$this->db->join('invt_item', 'sales_return_item.item_id = invt_item.item_id');
			$this->db->join('invt_item_unit', 'sales_return_item.item_unit_id = invt_item_unit.item_unit_id');
			$this->db->join('sales_delivery_note', 'sales_return_item.sales_delivery_note_id = sales_delivery_note.sales_delivery_note_id');
			if($start_date != ''){
				$this->db->where('sales_return.sales_return_date >= ', $start_date);
			}
			if($end_date != ''){
				$this->db->where('sales_return.sales_return_date <= ', $end_date);
			}
			if($warehouse_id != ''){
				$this->db->where('sales_return.warehouse_id', $warehouse_id);
			}
			if($section_id != ''){
				$this->db->where('sales_return.section_id', $section_id);
			}
			$this->db->where('sales_return.data_state', 0);
			$this->db->order_by('sales_return.sales_return_date', 'ASC');
			$result = $this->db->get();
			// print_r($result);exit;
			return $result;
		}











		
		public function getheader($id){
			$this->db->select('*');
			$this->db->from("sales_return");
			$this->db->where('sales_return_id', $id);
			$result = $this->db->get()->row_array();
			return $result;
		}
		
		public function getcontent($id){
			$this->db->select('*');
			$this->db->from('sales_return_item');
			$this->db->where('sales_return_id',$id);
			$result = $this->db->get()->result_array();
			return $result;
		}
		
		public function getwarehouse(){
			$this->db->select('warehouse_id, warehouse_name');
			$this->db->from('invt_warehouse');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			return $result->result_array();
		}
		
		public function getsupplier(){
			$this->db->select('supplier_id, supplier_name');
			$this->db->from('core_supplier');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			return $result->result_array();
		}
		
		public function getitem(){
			$this->db->select('item_id, item_name');
			$this->db->from('invt_item');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			return $result->result_array();
		}
		
		public function getaccount(){
			$this->db->select('account_id, account_name');
			$this->db->from('acct_account');
			$this->db->where('data_state','0');
			$result = $this->db->get();
			return $result->result_array();
		}
		
		public function getcurrencycode($currency_id){
			$this->db->select('currency_code')->from('acct_currency');
			$this->db->where('currency_id', $currency_id);
			$result = $this->db->get()->row_array();
			return $result['currency_code'];
		}
		
		public function getwarehousename($id){
			$this->db->select('warehouse_name');
			$this->db->from('invt_warehouse');
			$this->db->where('warehouse_id',$id);
			$result = $this->db->get()->row_array();
			return $result['warehouse_name'];
		}
		
		public function getstockistname($id){
			$this->db->select('stockist_name');
			$this->db->from('sales_stockist');
			$this->db->where('stockist_id',$id);
			$result = $this->db->get()->row_array();
			return $result['stockist_name'];
		}
		
		public function getstockistcode($id){
			$this->db->select('stockist_code');
			$this->db->from('sales_stockist');
			$this->db->where('stockist_id',$id);
			$result = $this->db->get()->row_array();
			return $result['stockist_code'];
		}
		
		public function getsuppliername($id){
			$this->db->select('supplier_name');
			$this->db->from('core_supplier');
			$this->db->where('supplier_id',$id);
			$result = $this->db->get()->row_array();
			return $result['supplier_name'];
		}
		
		public function getsalesorderno($sales_order_id){
			$this->db->select('sales_order_no');
			$this->db->from('sales_order');
			$this->db->where('sales_order_id',$sales_order_id);
			$result=$this->db->get()->row_array();
			return $result['sales_order_no'];
		}

		public function getgoodsreturnednoteno($data){
			$this->db->select('sales_order_no');
			$this->db->from('sales_order');
			$this->db->where('sales_order_id',$sales_order_id);
			$result=$this->db->get()->row_array();
			return $result['sales_order_no'];
		}
	
		public function getitemname($id){
			$this->db->select('item_name');
			$this->db->from('invt_item');
			$this->db->where('item_id',$id);
			$result = $this->db->get()->row_array();
			return $result['item_name'];
		}
		
		public function getaccountname($id){
			$this->db->select('account_name');
			$this->db->from('acct_account');
			$this->db->where('account_id',$id);
			$result = $this->db->get()->row_array();
			return $result['account_name'];
		}
		
		public function getitemunitsymbol($id){
			$this->db->select('item_unit_symbol');
			$this->db->from('invt_item_unit');
			$this->db->where('item_unit_id',$id);
			$result = $this->db->get()->row_array();
			return $result['item_unit_symbol'];
		}
		
		public function getdatasalesreturnreport($id){
			$this->db->select('sales_order_id, sales_order_no, supplier_id, warehouse_id, sales_order_date, sales_order_shipment_date, sales_order_remark,
								subtotal_item, subtotal_received_item, subtotal, discount_percentage, discount_amount, tax_percentage, tax_amount, total_amount,
								down_payment_amount, paid_down_payment_account_id, down_payment_account_id, last_balance');
			$this->db->from('sales_order');
			$this->db->where('data_state', '0');
			$this->db->where('sales_order_id', $id);
			return $this->db->get()->row_array();
		}
		
		public function getdetailsalesreturnreport($id){
			$this->db->select('sales_order_item_id, sales_order_id, item_id, item_unit_id, sales_order_item_description, quantity, received_quantity,
								item_unit_cost, subtotal, discount_percentage, discount_amount, subtotal_after_discount');
			$this->db->from('sales_order_item');
			$this->db->where('sales_order_id', $id);
			return $this->db->get();
		}
		

		
		public function getexport($data){
			$this->db->select('*');
			$this->db->from('sales_return');
			$this->db->join('sales_return_item', 'sales_return.sales_return_id=sales_return_item.sales_return_id');
			$this->db->where('data_state', '0');
			if($data['start_date'] != ''){
				$this->db->where('sales_return_date >= ', $data['start_date']);
			}
			if($data['end_date'] != ''){
				$this->db->where('sales_return_date <= ', $data['end_date']);
			}
			if($data['warehouse_id'] != ''){
				$this->db->where('warehouse_id', $data['warehouse_id']);
			}
			if($data['sales_return_status'] != ''){
				$this->db->where('sales_return_status', $data['sales_return_status']);
			}
			$this->db->order_by('sales_return.created_on', 'desc');
			$result = $this->db->get();
			return $result;
		}
	}
?>
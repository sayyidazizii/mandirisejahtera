<?php
	Class productionactivitycontrol extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('productionactivitycontrol_model');
			$this->load->helper('sistem');
			$this->load->library('fungsi');
			$this->load->library('configuration');
		}

		public function index(){
			$sesi	= 	$this->session->userdata('filter-productionactivitycontrol');
			$data['main_view']['content'] = 'productionactivitycontrol/listproductionactivitycontrol_view';
			$this->load->view('mainpage_view',$data);
		}
		
		public function filter(){
			$data = array (
				'date' => $this->input->post('date',true),
				'item_category_id' => $this->input->post('item_category_id',true),
				'item_id' => $this->input->post('item_id',true),
				'warehouse_id' => $this->input->post('warehouse_id',true),
				'refreshrate' => $this->input->post('refreshrate',true),
			);
			$this->session->set_userdata('filter-productionactivitycontrol',$data);
			redirect('productionactivitycontrol');
		}
	}
?>
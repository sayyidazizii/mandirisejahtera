<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class wiphistory extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('wiphistory_model');
			$this->load->helper('sistem');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-wiphistory');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('d-m-Y');
				$sesi['end_date']		= date('d-m-Y');
				$sesi['item_id']		='';
			}
			
			$start_date = tgltodb($sesi['start_date']);
			$end_date = tgltodb($sesi['end_date']);
			
			$data['main_view']['wiphistory']	= $this->wiphistory_model->get_list($start_date,$end_date,$sesi['item_id']);
			$data['main_view']['item']		= create_double($this->wiphistory_model->getitem(),'item_id','item_name');
			$data['main_view']['content']	= 'wiphistory/listwiphistory_view';
			$this->load->view('mainpage_view',$data);
		}
		
		public function filter(){
			$data = array (
				'start_date'	=> $this->input->post('start_date',true),
				'end_date'		=> $this->input->post('end_date',true),
				'item_id'	=> $this->input->post('item_id',true),
			);
			$this->session->set_userdata('filter-wiphistory',$data);
			redirect('wiphistory');
		}
		
		public function reset_search(){
			$this->session->unset_userdata('filter-wiphistory');
			redirect('wiphistory');
		}
		
		function showdetail(){
			$data['main_view']['result']	= $this->wiphistory_model->getdetail($this->uri->segment(3));
			$data['main_view']['content']	= 'wiphistory/detailwiphistory_view';
			$this->load->view('mainpage_view',$data);
		}
	}
?>
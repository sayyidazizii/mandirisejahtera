<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class EmptyData extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('EmptyData_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){	
			$data['main_view']['content']				= 'EmptyData/ListEmptyData_view';
			$this->load->view('MainPage_view',$data);
		}
		
		
		public function processEmptyData(){
			$auth = $this->session->userdata('auth');

			$data = array (
				'branch_id'		=> $auth['branch_id'],
				'created_id'	=> $auth['user_id'],
				'created_on'	=> date('Y-m-d H:i:s'),
			);

			if($this->EmptyData_model->insertEmptyData($data)){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Proses Mengkosongkan Data Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('empty-data');
			}else{
				$this->session->set_userdata('addacctsavings',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Proses Mengkosongkan Data Gagal
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('empty-data');
			}
		}
	}
?>
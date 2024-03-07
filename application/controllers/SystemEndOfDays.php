<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class SystemEndOfDays extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('SystemEndOfDays_model');
			$this->load->model('ValidationProcess_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
        public function CloseBranch(){
			$history_date = $this->SystemEndOfDays_model->getSystemEndOfDaysDate();
			// var_dump($history_date['created_at']);exit;
			if(!$history_date){
				$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
						Silahkan hubungi admin untuk setting end of days!
					</div> ";
			$this->session->set_userdata('message',$msg);
			}
			if($history_date['end_of_days_status'] == '1'){
				$journal =  $this->SystemEndOfDays_model->getAcctJournalVoucher(date('Y-m-d',strtotime($history_date['created_at'])));
			}else{
				$journal = [];
			}
			$data['main_view']['endofdays']				= $history_date;	
			$data['main_view']['journal']				= $journal;	
			$data['main_view']['content']				= 'SystemEndOfDays/ListSystemEndOfDaysClose_view';
			$this->load->view('MainPage_view',$data);
		}
		public function OpenBranch(){
			$history_date = $this->SystemEndOfDays_model->getSystemEndOfDaysDate();
			if(!$history_date){
				$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
						Silahkan hubungi admin untuk setting end of days!
					</div> ";
			$this->session->set_userdata('message',$msg);
			}
			// print_r($history_date);exit;
			$data['main_view']['endofdays']				= $history_date;	
			$data['main_view']['content']				= 'SystemEndOfDays/ListSystemEndOfDaysOpen_view';
			$this->load->view('MainPage_view',$data);
		}
		public function ProcessCloseBranch(){
			$auth = $this->session->userdata('auth');
			$end_of_days_id = $this->input->post('process_close_branch', true);
			$data = array(
				'end_of_days_status' 			=> 0,
				'debit_amount'					=> $this->input->post('debit_amount', true),
				'credit_amount'					=> $this->input->post('credit_amount', true),
				'close_id'						=> $auth['user_id'],
				'closed_at'						=> date('Y-m-d H:i:s'),
				'created_at'					=> date('Y-m-d H:i:s'),
			);

			$this->SystemEndOfDays_model->updateSystemEndOfDaysDate($data, $end_of_days_id);
			
			$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.SystemEndOfDays.processCloseEndOfDays',$auth['user_id'],'Close Branch');
			$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
						Cabang Telah Ditutup !
					</div> ";

			$this->session->set_userdata('message',$msg);
			redirect('end-of-days/close-branch');
		}
		public function ProcessOpenBranch(){
			
			$auth = $this->session->userdata('auth');
			$end_of_days_id = $this->input->post('process_close_branch', true);
			$data = array(
				'end_of_days_status' 			=> 1,
				'debit_amount'					=> 0,
				'credit_amount'					=> 0,
				'open_at'						=> date('Y-m-d H:i:s'),
				'open_id'						=> $auth['user_id'],
				'created_at'					=> date('Y-m-d H:i:s'),
			);

			$this->SystemEndOfDays_model->insertSystemEndOfDaysDate($data);
			
			$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.SystemEndOfDays.processCloseEndOfDays',$auth['user_id'],'Close Branch');
			$msg = "<div class='alert alert-success alert-dismissable'>  
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
						Cabang Telah Dibuka, Semangat Bekerja !
					</div> ";

			$this->session->set_userdata('message',$msg);
			redirect('end-of-days/open-branch');
		}
    }
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsAccountDetail extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsAccountDetail_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
		}


		public function showdetail(){
			$sesi	= 	$this->session->userdata('filter-acctsavingsaccountdetail');
			if(!is_array($sesi)){
				$sesi['start_date']			= date('Y-m-d');
				$sesi['end_date']			= date('Y-m-d');
				$sesi['savings_account_id']	= '';
			}

			$savings_account_id = $this->uri->segment(3);
			if($savings_account_id == ''){
				$savings_account_id = $sesi['savings_account_id'];
			}

			$data['main_view']['acctsavingsaccount']			= $this->AcctSavingsAccountDetail_model->getAcctSavingsAccount_Detail($savings_account_id);
			$data['main_view']['acctsavingsaccountdetail']		= $this->AcctSavingsAccountDetail_model->getAcctSavingsAccountDetail($sesi['start_date'], $sesi['end_date'], $savings_account_id);	

			$data['main_view']['content']						= 'AcctSavingsAccountDetail/ListAcctSavingsAccountDetail_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 			=> tgltodb($this->input->post('start_date',true)),
				"end_date" 				=> tgltodb($this->input->post('end_date',true)),
				"savings_account_id" 	=> $this->input->post('savings_account_id',true),
			);

			$this->session->set_userdata('filter-acctsavingsaccountdetail',$data);
			redirect('savings-account-detail/show-detail/'.$data['savings_account_id']);
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctsavingsaccountdetail');
			redirect('savings-account-detail/show-detail');

		}

		public function getListAcctSavingsAccount(){
			$auth = $this->session->userdata('auth');
			$list = $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'savings-account-detail/show-detail/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}
	}
?>
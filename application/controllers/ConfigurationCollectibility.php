<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class ConfigurationCollectibility extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('ConfigurationCollectibility_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['collectibility']	= $this->ConfigurationCollectibility_model->getConfigurationCollectibility();
			$data['main_view']['content']			= 'ConfigurationCollectibility/ListConfigurationCollectibility_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddConfigurationCollectibility(){
			$auth = $this->session->userdata('auth');

			foreach($_POST as $key=>$val){
				if(is_numeric($key)){						
					$dataitem[$key] = array(
						'collectibility_id'			=> $_POST["collectibility_id_".$key],
						'collectibility_bottom'		=> $_POST["collectibility_bottom_".$key],
						'collectibility_top'		=> $_POST["collectibility_top_".$key],
						'collectibility_ppap'		=> $_POST["collectibility_ppap_".$key],
					);

					$dataArray= $dataitem;	
				}
			}


			
			foreach ($dataArray as $k => $v) {
				$datakonfigurasi = array (
					'collectibility_id'			=> $v['collectibility_id'],
					'collectibility_bottom'		=> $v['collectibility_bottom'],
					'collectibility_top'		=> $v['collectibility_top'],
					'collectibility_ppap'		=> $v['collectibility_ppap'],
				);
				


				if($this->ConfigurationCollectibility_model->updateConfigurationCollectibility($datakonfigurasi)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Edit Konfigurasi Kolektibilitas Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					continue;
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Edit Konfigurasi Kolektibilitas Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('configuration-collectibility');
					break;
				}
			}
			redirect('configuration-collectibility');
		}
	}
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class zone extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('zone_model');
			$this->load->helper('sistem');
			$this->load->library('fungsi');
			$this->load->library('configuration');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$page = $this->uri->segment(3);
			$sesi = $this->session->userdata('filter-zone');
		
			if(!is_array($sesi)){
				$sesi['zone_code']			='';
				$sesi['zone_name']			='';
			}
		
			// $config['base_url'] 		= base_url().'zone/index/';
			$data['main_view']['zone']	= $this->zone_model->get_list($sesi);
			$data['main_view']['content']	='zone/listzone_view';
			$this->load->view('mainpage_view',$data);
		}
		
		public function filter(){
			$data = array (
				'zone_name' 	=> $this->input->post('zone_name',true),
				'zone_code'	=> $this->input->post('zone_code',true)
			);
			//print_r($data);exit;
			$this->session->set_userdata('filter-zone',$data);
			redirect('zone/index');
		}
		
		public function reset_filter(){
			$this->session->unset_userdata('filter-zone');
			redirect('zone/index');
		}
		
		function add(){
			$data['main_view']['content']			= 'zone/formaddzone_view';
			$this->load->view('mainpage_view',$data);
		}
		
		function processaddzone(){
			$auth = $this->session->userdata('auth');
		
			$data = array(
				'zone_code' 				=> $this->input->post('zone_code',true),
				'zone_name' 				=> $this->input->post('zone_name',true),
				'zone_shipment_cost' 		=> $this->input->post('zone_shipment_cost',true),
				'zone_remark'				=> $this->input->post('zone_remark',true),
				'data_state'						=> '0'
			);
		
		
			$this->form_validation->set_rules('zone_code', 'Zone Code', 'required');
			$this->form_validation->set_rules('zone_name', 'Zone Name', 'required');
			$this->form_validation->set_rules('zone_shipment_cost', 'Shipment Cost', 'required|numeric');
		
			if($this->form_validation->run()==true){
				if($this->zone_model->insertzone($data)){
					$auth = $this->session->userdata('auth');
					//$this->fungsi->set_log($auth['username'],'1003','Application.currency.processaddcurrency',$auth['username'],'Add New Currency');
					$msg = "<div class='alert alert-success alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Add Data Zone Success
							</div> ";
					$this->session->set_userdata('message',$msg);
					$this->session->unset_userdata('addzone');
					redirect('zone/add');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>               
								Add Data Zone Fail
							</div> ";
					$this->session->set_userdata('message',$msg);
					$this->session->set_userdata('addzone',$data);
					redirect('zone/add');
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				$this->session->set_userdata('addzone',$data);
				redirect('zone/add');
			}
		}
		
		function edit(){
			$data['main_view']['result']		= $this->zone_model->getdetail($this->uri->segment(3));
			$data['main_view']['content']		= 'zone/formeditzone_view';		
			$this->load->view('mainpage_view',$data);
		}
		
		function processupdatezone(){
			$data = array(
				'zone_id'				=> $this->input->post('zone_id',true),
				'zone_code' 			=> $this->input->post('zone_code',true),
				'zone_name' 			=> $this->input->post('zone_name',true),
				'zone_shipment_cost' 		=> $this->input->post('zone_shipment_cost',true),
				'zone_remark'			=> $this->input->post('zone_remark',true),
				'data_state'				=> '0'
			);
		
			$this->session->set_userdata('edit',$data);
			$this->form_validation->set_rules('zone_code', 'Zone Code', 'required');
			$this->form_validation->set_rules('zone_name', 'Zone Name', 'required');
			$this->form_validation->set_rules('zone_shipment_cost', 'Shipment Cost', 'required|numeric');
		
			//print_r($data);exit;
		
			if($this->form_validation->run()==true){
				if($this->zone_model->updatezone($data)==true){
					$auth 	= $this->session->userdata('auth');
					//$this->fungsi->set_log($auth['username'],'1077','Application.chartofaccount.edit',$auth['username'],'Edit Chart of Account');
					//$this->fungsi->set_change_log($old_data,$data,$auth['username'],$data['account_id']);
					$msg = "<div class='alert alert-success alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Edit Zone Success
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('zone/edit/'.$data['zone_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>               
								Edit Zone Fail
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('zone/edit/'.$data['zone_id']);
				}
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('zone/edit/'.$data['zone_id']);
			}
		}
		
		function delete(){
			if($this->zone_model->deletezone($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				//$this->fungsi->set_log($auth['username'],'1005','Application.currency.delete',$auth['username'],'Delete Currency');
				$msg = "<div class='alert alert-success alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Delete Data Zone Success
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('zone');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>               
							Delete Data Zone Fail
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('zone');
			}
		}
		
	public function export(){
		$auth = $this->session->userdata('auth');
		$sesi = $this->session->userdata('filter-zone');
		
		if(!is_array($sesi)){
			$sesi['zone_name']	= '';
			$sesi['zone_code']	= '';
		}
		
		$item = $this->zone_model->getexport($sesi);
		
		$this->load->library('excel');
		$this->excel->getProperties()->setCreator("The Bongko's")
								 ->setLastModifiedBy("The Bongko's")
								 ->setTitle("Zone")
								 ->setSubject("")
								 ->setDescription("Zone")
								 ->setKeywords("Zone")
								 ->setCategory("Zone");
								 
		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);	
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);	
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);	
		$this->excel->getActiveSheet()->setCellValue('A1',"Zone Code");
		$this->excel->getActiveSheet()->setCellValue('B1',"Zone Name");
		$this->excel->getActiveSheet()->setCellValue('C1',"Zone Shipment Cost");
		$this->excel->getActiveSheet()->setCellValue('D1',"Zone Remark");
		
		$j=2;
		$no=0;
			
		foreach($item as $key=>$val){
			if(is_numeric($key)){
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getStyle('A'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->setCellValue('A'.$j, $val['zone_code']);		
				$this->excel->getActiveSheet()->setCellValue('B'.$j, $val['zone_name']);
				$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['zone_shipment_cost']);
				$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['zone_shipment_remark']);
			}else {continue;}
			$j++;
		}
		$filename='zone_export.xls';
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'"');
		header('Cache-Control: max-age=0');
		
		$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
		$objWriter->save('php://output');
	}
	
	function import(){
		$data['main_view']['content']	= 'zone/formimportzone_view';
		$this->load->view('mainpage_view',$data);
	}
	
	function processimportitemcategory(){
		$auth = $this->session->userdata('auth');
		$fileName 	= $_FILES['filexls']['name'];
		$fileSize 	= $_FILES['filexls']['size'];
		$fileError 	= $_FILES['filexls']['error'];
		$fileType 	= $_FILES['filexls']['type'];
		$config['upload_path'] = 'dataupload/';
        $config['file_name'] = $fileName;
        $config['allowed_types'] = 'xls|xlsx';
        $config['max_size']        = 100000;	
		$this->load->library('upload');
        $this->upload->initialize($config);
		if(! $this->upload->do_upload('filexls') ){
			$msg = "<div class='alert alert-success alert-dismissable'> 
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
					".$this->upload->display_errors('', '')."
					</div> ";
			$this->session->set_userdata('message',$msg);
			redirect('zone/index');
		}else{
			$media = $this->upload->data('filexls');
            $inputFileName = 'dataupload/'.$media['file_name'];
			try{
				$inputFileType = IOFactory::identify($inputFileName);
                $objReader = IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
			}catch(Exception $e){
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}
			$sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
			$sukses = 0;
			$gagal = 0;			
			$dataArrayBonus			= array();
			$dataArrayDouble		= array();
			for ($row = 2; $row <= $highestRow; $row++){
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
												
				$data = array(
					'zone_code'		=> $rowData[0][0],
					'zone_name'		=> $rowData[0][1],
					'zone_shipment_cost'		=> $rowData[0][2],
					'zone_remark'		=> $rowData[0][3],
				);
				
				$dataArray 	= $this->session->userdata('importzone');
				$this->session->set_userdata('importzone',$dataArray);
				if($data['zone_code'] != ''){	
					if($this->zone_model->insertzone($data)){
						$sukses++;
						continue;
					}else{
						$gagal++;
						break;
					}
				}else{
					continue;
				}
			}
			
			$auth = $this->session->userdata('auth');
			$msg = "<div class='alert alert-success alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Import Data Successfully
						</div> ";
			$this->session->set_userdata('message',$msg);
			redirect('zone');
		}
	}
}
?>
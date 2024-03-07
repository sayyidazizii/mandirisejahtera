<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreDusun extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreDusun_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-coredusun');
			if(!is_array($sesi)){
				$sesi['kelurahan_id']		= '';
			}

			$data['main_view']['corecity']			= create_double($this->CoreDusun_model->getCoreCity(),'city_id','city_name');		
			$data['main_view']['coredusun']			= $this->CoreDusun_model->getDataCoreDusun($sesi['kelurahan_id']);
			$data['main_view']['content']			= 'CoreDusun/ListCoreDusun_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"kelurahan_id" 	=> $this->input->post('kelurahan_id',true),
			);

			$this->session->set_userdata('filter-coredusun',$data);
			redirect('dusun');
		}

		
		public function addCoreDusun(){
			
			$data['main_view']['corecity']			= create_double($this->CoreDusun_model->getCoreCity(),'city_id','city_name');
			$data['main_view']['content']			= 'CoreDusun/FormAddCoreDusun_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getCoreKecamatan(){
			$city_id 		= $this->input->post('city_id', true);
			
			$item = $this->CoreDusun_model->getCoreKecamatan($city_id);
			$data .= "<option value=''>--Choose One--</option>";
			foreach ($item as $mp){
				$data .= "<option value='$mp[kecamatan_id]'>$mp[kecamatan_name]</option>\n";	
			}
			echo $data;
		}

		public function getCoreKelurahan(){
			$kecamatan_id 		= $this->input->post('kecamatan_id', true);
			
			$item = $this->CoreDusun_model->getCoreKelurahan($kecamatan_id);
			$data .= "<option value=''>--Choose One--</option>";
			foreach ($item as $mp){
				$data .= "<option value='$mp[kelurahan_id]'>$mp[kelurahan_name]</option>\n";	
			}
			echo $data;
		}
		
		public function processAddCoreDusun(){
			$data = array(
				'kelurahan_id'				=> $this->input->post('kelurahan_id', true),
				'dusun_name'				=> $this->input->post('dusun_name', true),
			);
			
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_name', 'Name', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreDusun_model->insertCoreDusun($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Dusun Sukses
							</div> ";
					$this->session->unset_userdata('addcoredusun');
					$this->session->set_userdata('message',$msg);
					redirect('dusun/add');
				}else{
					$this->session->set_userdata('addcoredusun',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Dusun Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('dusun/add');
				}
			}else{
				$this->session->set_userdata('addcoredusun',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('dusun/add');
			}
		}
		
		public function editCoreDusun(){
			$data['main_view']['corekelurahan']		= create_double($this->CoreDusun_model->getCoreKelurahan2(),'kelurahan_id','kelurahan_name');	
			$data['main_view']['coredusun']			= $this->CoreDusun_model->getCoreDusun_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'CoreDusun/FormEditCoreDusun_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditCoreDusun(){
			$data = array(
				'dusun_id'					=> $this->input->post('dusun_id', true),
				'kelurahan_id'				=> $this->input->post('kelurahan_id', true),
				'dusun_name'				=> $this->input->post('dusun_name', true),
			);
			
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_name', 'Name', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreDusun_model->updateCoreDusun($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Dusun Sukses
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('dusun/edit/'.$data['dusun_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Dusun Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('dusun/edit/'.$data['dusun_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('dusun/edit/'.$data['dusun_id']);
			}				
		}
		
		public function deleteCoreDusun(){
			if($this->CoreDusun_model->deleteCoreDusun($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Dusun Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('dusun');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Dusun Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('dusun');
			}
		}
		
		// public function export(){
		// 	$item = $this->CoreDusun_model->getexport();
			
		// 	if($item->num_rows()!=0){
		// 		$this->load->library('excel');
				
		// 		$this->excel->getProperties()->setCreator("IBS CJDW")
		// 							 ->setLastModifiedBy("IBS CJDW")
		// 							 ->setTitle("Branch Report")
		// 							 ->setSubject("")
		// 							 ->setDescription("Branch Report")
		// 							 ->setKeywords("Asset, Type, Report")
		// 							 ->setCategory("Branch Report");
									 
		// 		$this->excel->setActiveSheetIndex(0);
		// 		$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		// 		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
		// 		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
		// 		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		// 		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
		// 		$this->excel->getActiveSheet()->mergeCells("B1:E1");
		// 		$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
		// 		$this->excel->getActiveSheet()->getStyle('B3:E3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		// 		$this->excel->getActiveSheet()->getStyle('B3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 		$this->excel->getActiveSheet()->getStyle('B3:E3')->getFont()->setBold(true);
		// 		$this->excel->getActiveSheet()->setCellValue('B1',"Machine");
		// 		$this->excel->getActiveSheet()->setCellValue('B3',"No");
		// 		$this->excel->getActiveSheet()->setCellValue('C3',"Branch Code");
		// 		$this->excel->getActiveSheet()->setCellValue('D3',"Branch Name");
		// 		$this->excel->getActiveSheet()->setCellValue('E3',"Description");
				
		// 		$j=4;
		// 		$no=0;
		// 		foreach($item->result_array() as $key=>$val){
		// 			if(is_numeric($key)){
		// 				$no++;
		// 				$this->excel->setActiveSheetIndex(0);
		// 				$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		// 				$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 				$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		// 				$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						
		// 				$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
		// 				$this->excel->getActiveSheet()->setCellValue('C'.$j, $val[dusun_code]);
		// 				$this->excel->getActiveSheet()->setCellValue('D'.$j, $val[dusun_name]);
		// 				$this->excel->getActiveSheet()->setCellValue('E'.$j, $val[dusun_description]);
		// 			}else{
		// 				continue;
		// 			}
		// 			$j++;
		// 		}
		// 		$filename='AssetType.xls';
		// 		header('Content-Type: application/vnd.ms-excel');
		// 		header('Content-Disposition: attachment;filename="'.$filename.'"');
		// 		header('Cache-Control: max-age=0');
							 
		// 		$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
		// 		ob_end_clean();
		// 		$objWriter->save('php://output');
		// 	}else{
		// 		echo "No available data !";
		// 	}
		// }
		
		// function import(){
		// 	$data['main_view']['content']	= 'CoreDusun/formimportcoredusun_view';
		// 	$this->load->view('MainPage_view',$data);
		// }
		
		// function processimportmachine(){
		// 	$auth = $this->session->userdata('auth');
		// 	$fileName 	= $_FILES['filexls']['name'];
		// 	$fileSize 	= $_FILES['filexls']['size'];
		// 	$fileError 	= $_FILES['filexls']['error'];
		// 	$fileType 	= $_FILES['filexls']['type'];
		// 	$config['upload_path'] = 'dataupload/';
  //           $config['file_name'] = $fileName;
  //           $config['allowed_types'] = 'xls|xlsx';
  //           $config['max_size']        = 10000;
		// 	$this->load->library('upload');
  //           $this->upload->initialize($config);
		// 	if(! $this->upload->do_upload('filexls') ){
		// 		$msg = "<div class='alert alert-danger alert-dismissable'>                
		// 			".$this->upload->display_errors('', '')."
		// 			</div> ";
		// 		$this->session->set_userdata('message',$msg);
		// 		redirect('machine');
		// 	}else{
		// 		$media = $this->upload->data('filexls');
		// 		$inputFileName = 'dataupload/'.$media['file_name'];
		// 		try {
		// 			$inputFileType = IOFactory::identify($inputFileName);
		// 			$objReader = IOFactory::createReader($inputFileType);
		// 			$objPHPExcel = $objReader->load($inputFileName);
		// 		} catch(Exception $e) {
		// 			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		// 		}
		// 		$sheet = $objPHPExcel->getSheet(0);
		// 		$highestRow = $sheet->getHighestRow();
		// 		$highestColumn = $sheet->getHighestColumn();
		// 		$sukses = 0;
		// 		$gagal = 0;
		// 		for ($row = 2; $row <= $highestRow; $row++){ 
		// 			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
  //                                               NULL,
  //                                               TRUE,
  //                                               FALSE);
					
		// 			$data= array (
		// 				'dusun_code'		=> $rowData[0][0],
		// 				'dusun_name'		=> $rowData[0][1],
		// 				'dusun_description'	=> $rowData[0][2],
		// 			);
		// 			if($data['dusun_code'] != ''){
		// 				if($this->CoreDusun_model->cekcoredusuncode($data['dusun_code'])==0){
		// 					if($this->machine_model->insertmachine($data)){
		// 						$sukses++;
		// 						continue;
		// 					}else{
		// 						$dataArrayItem 	= $this->session->userdata('importcoredusun');
		// 						$dataArrayItem[$data['dusun_code']] = $data;
		// 						$this->session->set_userdata('importcoredusun',$dataArrayItem);
		// 						continue;
		// 					}
		// 				}else{
		// 					$dataArrayItem 	= $this->session->userdata('importcoredusun');
		// 					$dataArrayItem[$data['dusun_code']] = $data;
		// 					$this->session->set_userdata('importcoredusun',$dataArrayItem);
		// 				}
		// 			}
		// 		}
		// 		$auth = $this->session->userdata('auth');
		// 		// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processimportmachine',$auth['username'],'Import New machine');
		// 		$msg = "<div class='alert alert-success'>                
		// 					Import Data Branch Successfully ".$sukses." record
		// 				</div> ";
		// 		$this->session->set_userdata('message',$msg);
		// 		redirect('dusun');
		// 	}
		// }
	}
?>
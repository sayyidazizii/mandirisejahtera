<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavings extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavings_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['acctsavings']				= $this->AcctSavings_model->getDataAcctSavings();
			$data['main_view']['savingsprofitsharing']		= $this->configuration->SavingsProfitSharing();	
			$data['main_view']['content']			= 'AcctSavings/ListAcctSavings_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addAcctSavings(){
			$data['main_view']['savingsprofitsharing']		= $this->configuration->SavingsProfitSharing();
			$data['main_view']['accountstatus']				= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']			= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']				= create_double($this->AcctSavings_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['content']			= 'AcctSavings/FormAddAcctSavings_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavings-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavings-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavings-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavings-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavings-'.$unique['unique']);
			redirect('savings/add');
		}

		public function processAddAcctAccount(){
			$auth = $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'account_code'		=> $this->input->post('account_code', true),
				'account_name'		=> $this->input->post('account_name', true),
				'account_type_id'	=> $this->input->post('account_type_id', true),
				'account_group'		=> $this->input->post('account_group', true),
				'created_id'		=> $auth['user_id'],
				'created_on'		=> date('Y-m-d H:i:s'),
			);
			
			if($this->AcctSavings_model->insertAcctAccount($data)){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Perkiraan Sukses
						</div> ";

				$this->session->unset_userdata('addacctsavings-'.$unique['unique']);
				$this->session->set_userdata('message',$msg);
				$this->session->set_userdata('message',$msg);
				redirect('savings/add');
			}else{
				$this->session->set_userdata('addacctsavings',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Tambah Data Perkiraan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings/add');
			}
		}
		
		public function processAddAcctSavings(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'savings_code'				=> $this->input->post('savings_code', true),
				'savings_name'				=> $this->input->post('savings_name', true),
				'account_id'				=> $this->input->post('account_id', true),
				'account_basil_id'			=> $this->input->post('account_basil_id', true),
				'savings_profit_sharing'	=> $this->input->post('savings_profit_sharing', true),
				'savings_interest_rate'		=> $this->input->post('savings_interest_rate', true),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('savings_code', 'Kode Simpanan', 'required');
			$this->form_validation->set_rules('savings_name', 'Nama Simpanan', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan Simpanan', 'required');
			$this->form_validation->set_rules('account_basil_id', 'Nomor Perkiraan Bunga Simpanan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctSavings_model->insertAcctSavings($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Kode Simpanan Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctsavings-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('savings/add');
				}else{
					$this->session->set_userdata('addacctsavings',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Kode Simpanan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings/add');
				}
			}else{
				$this->session->set_userdata('addacctsavings',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings/add');
			}
		}
		
		public function editAcctSavings(){
			$data['main_view']['savingsprofitsharing']		= $this->configuration->SavingsProfitSharing();
			$data['main_view']['accountstatus']				= $this->configuration->AccountStatus();
			$data['main_view']['kelompokperkiraan']			= $this->configuration->KelompokPerkiraan();
			$data['main_view']['acctaccount']				= create_double($this->AcctSavings_model->getAcctAccount(),'account_id', 'account_code');
			$data['main_view']['acctsavings']		= $this->AcctSavings_model->getAcctSavings_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'AcctSavings/FormEditAcctSavings_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processEditAcctSavings(){
			$data = array(
				'savings_id'				=> $this->input->post('savings_id', true),
				'savings_code'				=> $this->input->post('savings_code', true),
				'savings_name'				=> $this->input->post('savings_name', true),
				'account_id'				=> $this->input->post('account_id', true),
				'account_basil_id'			=> $this->input->post('account_basil_id', true),
				'savings_profit_sharing'	=> $this->input->post('savings_profit_sharing', true),
				'savings_interest_rate'		=> $this->input->post('savings_interest_rate', true),
			);
			
			$this->form_validation->set_rules('savings_code', 'Kode Simpanan', 'required');
			$this->form_validation->set_rules('savings_name', 'Nama Simpanan', 'required');
			$this->form_validation->set_rules('account_id', 'Nomor Perkiraan', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctSavings_model->updateAcctSavings($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['user_id'],'1003','Application.machine.processMachinesupplier',$auth['username'],'edit machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Kode Simpanan Sukses
							</div> ";

					$this->session->set_userdata('message',$msg);
					redirect('savings/edit/'.$data['savings_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Kode Simpanan Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings/edit/'.$data['savings_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings/edit/'.$data['savings_id']);
			}				
		}
		
		public function deleteAcctSavings(){
			if($this->AcctSavings_model->deleteAcctSavings($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Kode Simpanan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Kode Simpanan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings');
			}
		}
		
		// public function export(){
		// 	$item = $this->AcctSavings_model->getexport();
			
		// 	if($item->num_rows()!=0){
		// 		$this->load->library('excel');
				
		// 		$this->excel->getProperties()->setCreator("IBS CJDW")
		// 							 ->setLastModifiedBy("IBS CJDW")
		// 							 ->setTitle("Member Report")
		// 							 ->setSubject("")
		// 							 ->setDescription("Member Report")
		// 							 ->setKeywords("Asset, Type, Report")
		// 							 ->setCategory("Member Report");
									 
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
		// 		$this->excel->getActiveSheet()->setCellValue('C3',"Member Code");
		// 		$this->excel->getActiveSheet()->setCellValue('D3',"Member Name");
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
		// 				$this->excel->getActiveSheet()->setCellValue('C'.$j, $val[member_code]);
		// 				$this->excel->getActiveSheet()->setCellValue('D'.$j, $val[member_name]);
		// 				$this->excel->getActiveSheet()->setCellValue('E'.$j, $val[member_description]);
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
		// 	$data['main_view']['content']	= 'AcctSavings/formimportacctsavings_view';
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
		// 				'member_code'		=> $rowData[0][0],
		// 				'member_name'		=> $rowData[0][1],
		// 				'member_description'	=> $rowData[0][2],
		// 			);
		// 			if($data['member_code'] != ''){
		// 				if($this->AcctSavings_model->cekacctsavingscode($data['member_code'])==0){
		// 					if($this->machine_model->insertmachine($data)){
		// 						$sukses++;
		// 						continue;
		// 					}else{
		// 						$dataArrayItem 	= $this->session->userdata('importacctsavings');
		// 						$dataArrayItem[$data['member_code']] = $data;
		// 						$this->session->set_userdata('importacctsavings',$dataArrayItem);
		// 						continue;
		// 					}
		// 				}else{
		// 					$dataArrayItem 	= $this->session->userdata('importacctsavings');
		// 					$dataArrayItem[$data['member_code']] = $data;
		// 					$this->session->set_userdata('importacctsavings',$dataArrayItem);
		// 				}
		// 			}
		// 		}
		// 		$auth = $this->session->userdata('auth');
		// 		// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processimportmachine',$auth['username'],'Import New machine');
		// 		$msg = "<div class='alert alert-success'>                
		// 					Import Data Member Successfully ".$sukses." record
		// 				</div> ";
		// 		$this->session->set_userdata('message',$msg);
		// 		redirect('savings');
		// 	}
		// }
	}
?>
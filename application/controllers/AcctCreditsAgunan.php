<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsAgunan extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsAgunan_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsAgunan_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']		= 'AcctCreditsAgunan/ListAcctCreditsAgunan_view';
			$this->load->view('MainPage_view', $data);
		}
		
		public function edit($credits_agunan_id){
			$data['main_view']['creditsagunan']	= $this->AcctCreditsAgunan_model->getAcctCreditAgunanDetail($credits_agunan_id);
			$data['main_view']['content']		= 'AcctCreditsAgunan/FormEditAcctCreditsAgunan_view';
			$this->load->view('MainPage_view', $data);
		}
		
		public function processEdit(){
			$data = array(
				'credits_agunan_id'				=> $this->input->post('credits_agunan_id', true),
				'credits_agunan_bpkb_no_rangka'	=> $this->input->post('credits_agunan_bpkb_no_rangka', true),
				'credits_agunan_bpkb_no_mesin'	=> $this->input->post('credits_agunan_bpkb_no_mesin', true),
			);
			
			$this->form_validation->set_rules('credits_agunan_bpkb_no_rangka', 'No. Rangka', 'required');
			$this->form_validation->set_rules('credits_agunan_bpkb_no_mesin', 'No. Mesin', 'required');
			
			if($this->form_validation->run()==true){
				if($this->AcctCreditsAgunan_model->updateAcctCreditsAgunan($data)){
					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Edit Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('credits-agunan');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Edit Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('credits-agunan/edit/'.$data['credits_agunan_id']);
				}
			}else{
				$this->session->set_userdata('addcorebranch',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credits-agunan/edit/'.$data['credits_agunan_id']);
			}
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctcreditsagunan',$data);
			redirect('credits-agunan');
		}

		public function reset_search(){
			$this->session->unset_userdata('filter-acctcreditsagunan');
			redirect('credits-agunan');
		}

		public function getAcctCreditsAgunanList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditsagunan');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$agunanstatus = $this->configuration->AgunanStatus();

			$list = $this->AcctCreditsAgunan_model->get_datatables($sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $agunan) {
				if($agunan->credits_agunan_type == 1){
					$credits_agunan_type	= 'BPKB';
					$credits_agunan_ket		= $agunan->credits_agunan_bpkb_keterangan;
				}else if($agunan->credits_agunan_type == 2) {
					$credits_agunan_type 	= 'Sertifikat';
					$credits_agunan_ket		= $agunan->credits_agunan_shm_keterangan;
				}else if($agunan->credits_agunan_type == 3){
					$credits_agunan_type 	='Bilyet Simpanan Berjangka';
					$credits_agunan_ket		= $agunan->credits_agunan_other_keterangan;
				}else if($agunan->credits_agunan_type == 4){
					$credits_agunan_type 	= 'Elektro';
					$credits_agunan_ket		= $agunan->credits_agunan_other_keterangan;
				}else if($agunan->credits_agunan_type == 5){
					$credits_agunan_type 	= 'Dana Keanggotaan';
					$credits_agunan_ket		= $agunan->credits_agunan_other_keterangan;
				}else if($agunan->credits_agunan_type == 6){
					$credits_agunan_type 	= 'Tabungan';
					$credits_agunan_ket		= $agunan->credits_agunan_other_keterangan;
				}else if($agunan->credits_agunan_type == 7){
					$credits_agunan_type 	= 'ATM / Jamsostek';
					$credits_agunan_ket		= $agunan->credits_agunan_atmjamsostek_keterangan;
				}
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $agunan->credits_account_serial;
	            $row[] = $this->AcctCreditsAgunan_model->getMemberName($agunan->member_id);
	            $row[] = $agunanstatus[$agunan->credits_agunan_status];
	            $row[] = $credits_agunan_type;
	            $row[] = $agunan->credits_agunan_shm_no_sertifikat;
	            $row[] = $agunan->credits_agunan_shm_luas;
	            $row[] = $agunan->credits_agunan_shm_atas_nama;
	            $row[] = $agunan->credits_agunan_shm_kedudukan;
	            $row[] = number_format($agunan->credits_agunan_shm_taksiran, 2);
	            $row[] = $agunan->credits_agunan_bpkb_nomor;
	            $row[] = $agunan->credits_agunan_bpkb_type;
	            $row[] = $agunan->credits_agunan_bpkb_nama;
	            $row[] = $agunan->credits_agunan_bpkb_address;
	            $row[] = $agunan->credits_agunan_bpkb_nopol;
	            $row[] = $agunan->credits_agunan_bpkb_no_rangka;
	            $row[] = $agunan->credits_agunan_bpkb_no_mesin;
	            $row[] = $agunan->credits_agunan_bpkb_dealer_name;
	            $row[] = $agunan->credits_agunan_bpkb_dealer_address;
	            $row[] = number_format($agunan->credits_agunan_bpkb_taksiran, 2);
	            $row[] = number_format($agunan->credits_agunan_bpkb_gross, 2);
	            $row[] = $agunan->credits_agunan_atmjamsostek_nomor;
	            $row[] = $agunan->credits_agunan_atmjamsostek_nama;
	            $row[] = $agunan->credits_agunan_atmjamsostek_bank;
	            $row[] = number_format($agunan->credits_agunan_atmjamsostek_taksiran, 2);
	            $row[] = $credits_agunan_ket;
	            if($agunan->credits_agunan_status == 0){
					if($agunan->credits_id == 13){
						$row[] = '
						<a href="'.base_url().'credits-agunan/edit/'.$agunan->credits_agunan_id.'" class="btn default btn-xs yellow-lemon" role="button"><i class="fa fa-edit"></i> Edit</a>
						<a href="'.base_url().'credits-agunan/update-status/'.$agunan->credits_agunan_id.'" onClick="javascript:return confirm(\'Yakin status agunan akan diupdate ?\')" class="btn default btn-xs purple" role="button"><i class="fa fa-edit"></i> Update</a>
						<a href="'.base_url().'credits-agunan/print-receipt/'.$agunan->credits_agunan_id.'" class="btn default btn-xs blue" role="button"><i class="fa fa-edit"></i> Tanda Terima</a>';
					}else{
						$row[] = '
						<a href="'.base_url().'credits-agunan/update-status/'.$agunan->credits_agunan_id.'" onClick="javascript:return confirm(\'Yakin status agunan akan diupdate ?\')" class="btn default btn-xs purple" role="button"><i class="fa fa-edit"></i> Update</a>
						<a href="'.base_url().'credits-agunan/print-receipt/'.$agunan->credits_agunan_id.'" class="btn default btn-xs blue" role="button"><i class="fa fa-edit"></i> Tanda Terima</a>';
					}
            	} else {
            		$row[] = '';
            	}
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditsAgunan_model->count_all($sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditsAgunan_model->count_filtered($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function updateAgunanStatus(){
			if($this->AcctCreditsAgunan_model->updateAgunanStatus($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Update Status Agunan Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('credits-agunan');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Update Status Agunan Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('credits-agunan');
			}
		}

		public function export(){	
			$auth = $this->session->userdata('auth');
			$agunanstatus = $this->configuration->AgunanStatus();

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-acctcreditsagunan');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$acctcreditsagunan	= $this->AcctCreditsAgunan_model->getExportAcctCreditsAgunan($sesi['branch_id']);

			
			if($acctcreditsagunan->num_rows()!=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									 ->setLastModifiedBy("SIS")
									 ->setTitle("Master Data Agunan")
									 ->setSubject("")
									 ->setDescription("Master Data Agunan")
									 ->setKeywords("Master, Data, Agunan")
									 ->setCategory("Master Data Agunan");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);		
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('U')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('Y')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('AA')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('AB')->setWidth(30);		
				$this->excel->getActiveSheet()->getColumnDimension('AC')->setWidth(30);		

				
				$this->excel->getActiveSheet()->mergeCells("B1:AC1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:AC3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:AC3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:AC3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Agunan");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Akad");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Sertifikat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Luas");
				$this->excel->getActiveSheet()->setCellValue('G3',"Atas Nama");
				$this->excel->getActiveSheet()->setCellValue('H3',"Kedudukan");
				$this->excel->getActiveSheet()->setCellValue('I3',"Taksiran");
				$this->excel->getActiveSheet()->setCellValue('J3',"BPKB");
				$this->excel->getActiveSheet()->setCellValue('K3',"Jenis");
				$this->excel->getActiveSheet()->setCellValue('L3',"Atas Nama");
				$this->excel->getActiveSheet()->setCellValue('M3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('N3',"No. Polisi");
				$this->excel->getActiveSheet()->setCellValue('O3',"No. Rangka");
				$this->excel->getActiveSheet()->setCellValue('P3',"No. Mesin");
				$this->excel->getActiveSheet()->setCellValue('Q3',"Nama Dealer");
				$this->excel->getActiveSheet()->setCellValue('R3',"Alamat Dealer");
				$this->excel->getActiveSheet()->setCellValue('S3',"Taksiran");
				$this->excel->getActiveSheet()->setCellValue('T3',"Uang Muka Gross");
				$this->excel->getActiveSheet()->setCellValue('U3',"Nomor (ATM / Jamsostek)");
				$this->excel->getActiveSheet()->setCellValue('V3',"Atas Nama (ATM / Jamsostek)");
				$this->excel->getActiveSheet()->setCellValue('W3',"Nama Bank (ATM / Jamsostek)");
				$this->excel->getActiveSheet()->setCellValue('X3',"Taksiran (ATM / Jamsostek)");
				$this->excel->getActiveSheet()->setCellValue('Y3',"Keterangan (ATM / Jamsostek)");
				$this->excel->getActiveSheet()->setCellValue('Z3',"Deskripsi Bilyet Simpanan Berjangka");
				$this->excel->getActiveSheet()->setCellValue('AA3',"Deskripsi Elektro");
				$this->excel->getActiveSheet()->setCellValue('AB3',"Deskripsi Dana Keanggotaan");
				$this->excel->getActiveSheet()->setCellValue('AC3',"Deskripsi Tabungan");
				$this->excel->getActiveSheet()->setCellValue('AD3',"Status");
				
				$j=4;
				$no=0;
				
				foreach($acctcreditsagunan->result_array() as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':AD'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('Q'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('R'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('S'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('T'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('U'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('V'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('W'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('X'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('Y'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('Z'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('AA'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('AB'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('AC'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('AD'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $this->AcctCreditsAgunan_model->getMemberName($val['member_id']));
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['credits_agunan_shm_no_sertifikat']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['credits_agunan_shm_luas']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['credits_agunan_shm_atas_nama']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['credits_agunan_shm_kedudukan']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_agunan_shm_taksiran'], 2));
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $val['credits_agunan_bpkb_nomor']);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_agunan_bpkb_type']);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['credits_agunan_bpkb_nama']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $val['credits_agunan_bpkb_address']);
						$this->excel->getActiveSheet()->setCellValue('N'.$j, $val['credits_agunan_bpkb_nopol']);
						$this->excel->getActiveSheet()->setCellValue('O'.$j, $val['credits_agunan_bpkb_no_rangka']);
						$this->excel->getActiveSheet()->setCellValue('P'.$j, $val['credits_agunan_bpkb_no_mesin']);
						$this->excel->getActiveSheet()->setCellValue('Q'.$j, $val['credits_agunan_bpkb_dealer_name']);
						$this->excel->getActiveSheet()->setCellValue('R'.$j, $val['credits_agunan_bpkb_dealer_address']);
						$this->excel->getActiveSheet()->setCellValue('S'.$j, number_format($val['credits_agunan_bpkb_taksiran'], 2));
						$this->excel->getActiveSheet()->setCellValue('T'.$j, number_format($val['credits_agunan_bpkb_gross'], 2));
						$this->excel->getActiveSheet()->setCellValue('U'.$j, $val['credits_agunan_atmjamsostek_nomor']);
						$this->excel->getActiveSheet()->setCellValue('V'.$j, $val['credits_agunan_atmjamsostek_nama']);
						$this->excel->getActiveSheet()->setCellValue('W'.$j, $val['credits_agunan_atmjamsostek_bank']);
						$this->excel->getActiveSheet()->setCellValue('X'.$j, number_format($val['credits_agunan_atmjamsostek_taksiran'], 2));
						$this->excel->getActiveSheet()->setCellValue('Y'.$j, $val['credits_agunan_atmjamsostek_keterangan']);
						if($val['credits_agunan_type'] == 3){
							$this->excel->getActiveSheet()->setCellValue('Z'.$j, $val['credits_agunan_other_keterangan']);	
						}	
						if($val['credits_agunan_type'] == 4){
							$this->excel->getActiveSheet()->setCellValue('AA'.$j, $val['credits_agunan_other_keterangan']);	
						}	
						if($val['credits_agunan_type'] == 5){
							$this->excel->getActiveSheet()->setCellValue('AB'.$j, $val['credits_agunan_other_keterangan']);	
						}	
						if($val['credits_agunan_type'] == 6){
							$this->excel->getActiveSheet()->setCellValue('AC'.$j, $val['credits_agunan_other_keterangan']);	
						}	
						$this->excel->getActiveSheet()->setCellValue('AD'.$j, $agunanstatus[$val['credits_agunan_status']]);	
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Agunan.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				ob_end_clean();
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}		

		public function printAgunanReceipt(){
			$auth 					= $this->session->userdata('auth');
			$credits_agunan_id 		= $this->uri->segment(3);
			// $preferencecompany 		= $this->AcctCreditsAgunan_model->getPreferenceCompany();
			$agunandetail		 	= $this->AcctCreditsAgunan_model->getAcctCreditAgunanDetail($credits_agunan_id);

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 
			
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			$pdf->SetFont('helvetica', 'B', 20);

			$pdf->AddPage();


			$pdf->SetFont('helvetica', '', 12);

			$base_url = base_url();

			$tbl1 = "
			<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			   <tr>
				   <td style=\"text-align:center;\" width=\"100%\">
					   <div style=\"font-size:14px; font-weight:bold\">TANDA TERIMA JAMINAN</div>
				   </td>			
				</tr>
			</table>
			<br>
			<br>
			<br>
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"100%\"><div style=\"text-align: left; font-size:12px;\">Telah Diterima barang jaminan dari :</div></td>
			    </tr>
			    <tr>
					<td style=\"text-align:left;\" width=\"5%\"></td>	
					<td style=\"text-align:left;\" width=\"15%\">
						<div style=\"font-size:12px;\">Nama</div>
					</td>
					<td style=\"text-align:left;\" width=\"2%\">
						<div style=\"font-size:12px;\">:</div>
					</td>
					<td style=\"text-align:left;\" width=\"80%\">
						<div style=\"font-size:12px;\">".$agunandetail['member_name']."</div>
					</td>	
			    </tr>	
			    <tr>
					<td style=\"text-align:left;\" width=\"5%\"></td>	
					<td style=\"text-align:left;\" width=\"15%\">
						<div style=\"font-size:12px;\">No. KTP</div>
					</td>
					<td style=\"text-align:left;\" width=\"2%\">
						<div style=\"font-size:12px;\">:</div>
					</td>
					<td style=\"text-align:left;\" width=\"80%\">
						<div style=\"font-size:12px;\">".$agunandetail['member_identity_no']."</div>
					</td>	
			    </tr>	
			    <tr>
					<td style=\"text-align:left;\" width=\"5%\"></td>	
					<td style=\"text-align:left;\" width=\"15%\">
						<div style=\"font-size:12px;\">Pekerjaan</div>
					</td>
					<td style=\"text-align:left;\" width=\"2%\">
						<div style=\"font-size:12px;\">:</div>
					</td>
					<td style=\"text-align:left;\" width=\"80%\">
						<div style=\"font-size:12px;\">".$agunandetail['member_company_job_title']."</div>
					</td>	
			    </tr>	
			    <tr>
					<td style=\"text-align:left;\" width=\"5%\"></td>	
					<td style=\"text-align:left;\" width=\"15%\">
						<div style=\"font-size:12px;\">Alamat</div>
					</td>
					<td style=\"text-align:left;\" width=\"2%\">
						<div style=\"font-size:12px;\">:</div>
					</td>
					<td style=\"text-align:left;\" width=\"80%\">
						<div style=\"font-size:12px;\">".$agunandetail['member_address']."</div>
					</td>	
			    </tr>	
			    <tr>
					<td style=\"text-align:left;\" width=\"5%\"></td>	
					<td style=\"text-align:left;\" width=\"15%\">
						<div style=\"font-size:12px;\">No. Telepon</div>
					</td>
					<td style=\"text-align:left;\" width=\"2%\">
						<div style=\"font-size:12px;\">:</div>
					</td>
					<td style=\"text-align:left;\" width=\"80%\">
						<div style=\"font-size:12px;\">".$agunandetail['member_phone']."</div>
					</td>	
			    </tr>			
			</table>
			<br>";
			
			if($agunandetail['credits_id'] == 17){
				$tbl1 .="
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
						<tr>
							<td width=\"100%\"><div style=\"text-align: left; font-size:12px;\">Jaminan Berupa ATM Asli dan Buku Tabungan dengan data sebagai berikut :</div></td>
						</tr>
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px;\">Nomor ATM</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_atmjamsostek_nomor']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px;\">No. Rekening Tabungan</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_atmjamsostek_keterangan']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px;\">Nama Bank</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_atmjamsostek_bank']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"20%\">
								<div style=\"font-size:12px;\">Atas Nama</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_atmjamsostek_nama']."</div>
							</td>	
						</tr>	
					</table>
					<br>
				";
			}else{
				$tbl1 .="
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
						<tr>
							<td width=\"100%\"><div style=\"text-align: left; font-size:12px;\">Jaminan BPKB dengan data sebagai berikut :</div></td>
						</tr>
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">No. BPKB</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_nomor']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">No. Polisi</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_nopol']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">Nomor Rangka</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_no_rangka']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">Nomor Mesin</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_no_mesin']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">Merk / Type</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_type']."</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">Tahun / Warna</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_keterangan']."</div>
							</td>	
						</tr>	
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">A/N Nama</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_nama']."</div>
							</td>	
						</tr>
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"15%\">
								<div style=\"font-size:12px;\">Alamat</div>
							</td>
							<td style=\"text-align:left;\" width=\"2%\">
								<div style=\"font-size:12px;\">:</div>
							</td>
							<td style=\"text-align:left;\" width=\"80%\">
								<div style=\"font-size:12px;\">".$agunandetail['credits_agunan_bpkb_address']."</div>
							</td>	
						</tr>";
					if($agunandetail['credits_id'] == 13){
					$tbl1 .="
						<tr>
							<td style=\"text-align:right;\" width=\"5%\">-</td>	
							<td style=\"text-align:left;\" width=\"95%\">
								<div style=\"font-size:12px;\"><b>BPKB Baru dalam Proses Pembuatan Dealer ......................, dan setelah selesai akan diberikan ke pihak KSU \"Mandiri Sejahtera\"</b></div>
							</td>
						</tr>	
						";
					}
					$tbl1 .="		
					</table>
					<br>";
			}
			$tbl1 .="
				<div style=\"font-size:12px;\"><b>Dan akan dikembalikan setelah pinjaman lunas.</b><div>
				<div style=\"font-size:12px;\">Karanganyar, ".$agunandetail['credits_account_date']."<div>
				
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
				<tr>	
				<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
					<div style=\"font-size:12px;\">
						Yang Menyerahkan</div>
				</td>
				<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
					<div style=\"font-size:12px;\">
						Yang Menerima</div>
				</td>			
				</tr>
				<tr>	
					<td style=\"text-align:center;\" width=\"50%\">
						<div style=\"font-size:12px;\">
							".$agunandetail['member_name']."</div>
					</td>
				<td style=\"text-align:center;\" width=\"50%\">
					<div style=\"font-size:12px;\">Melyda Nur Malita</div>
				</td>			
				</tr>
				<tr>	
					<td style=\"text-align:center;\" width=\"25%\">
					</td>
				<td style=\"text-align:center;\" width=\"50%\" height=\"100px\">
					<div style=\"font-size:12px;\">Mengetahui</div>
				</td>	
				<td style=\"text-align:center;\" width=\"25%\">
				</td>		
				</tr>
				<tr>	
					<td style=\"text-align:center;\" width=\"25%\">
					</td>
				<td style=\"text-align:center;\" width=\"50%\">
					<u>Herry Warsilo</u><br>
					Pimpinan Cabang
				</td>	
				<td style=\"text-align:center;\" width=\"25%\">
				</td>		
				</tr>
			</table>
			
			";

			$pdf->writeHTML($tbl1, true, false, false, false, '');

			ob_clean();

			
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

		}
	}
?>
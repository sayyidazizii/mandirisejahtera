<?php ob_start(); ?>
<?php
	ini_set('memory_limit', '512M');
	defined('BASEPATH') OR exit('No direct script access allowed');


	Class NewMemberReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('NewMemberReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->NewMemberReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['acctsavings']				= create_double($this->NewMemberReport_model->getAcctSavings(),'savings_id','savings_name');
			$data['main_view']['content']					= 'NewMemberReport/ListNewMemberReport_view';
			$this->load->view('MainPage_view',$data);
		}
 
		public function viewreport(){
			$sesi = array (
				"branch_id"					=> $this->input->post('branch_id', true),
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				"view"						=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 				=	$this->session->userdata('auth'); 
			$preferencecompany 	= $this->NewMemberReport_model->getPreferenceCompany();
			
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}


			$kelompoklaporansimpanan		= $this->configuration->KelompokLaporanSimpanan1();

			$coremember 		= $this->NewMemberReport_model->getNewMemberReport($sesi['start_date'], $sesi['end_date']);
			$totaltax = 0;
			
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl0 = "
			<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"10%\">" .$img."</td>
			    </tr>
			    <tr>
			    </tr>
			</table>
			<br/>
			<br/>
			<br/>
			<br/>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"100%\"><div style=\"text-align: left; font-size:14px; font-weight:bold\">DAFTAR ANGGOTA BARU ".date('d-m-Y', strtotime($sesi['start_date']))." s/d ".date('d-m-Y', strtotime($sesi['end_date']))."</div></td>
			    </tr>
			</table>
			<br>";

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
					<td width=\"4%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No</div></td>
					<td width=\"16%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Anggota</div></td>
			        <td width=\"30%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"30%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"20%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
			    </tr>				
			</table>";

			if(count($coremember) > 0){
				$no = 1;
				foreach($coremember as $key => $val){
					$tbl1 .= "
					<tr>
						<td width=\"4%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">".$no.".</div></td>
						<td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">".$val['member_no']."</div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".$val['member_name']."</div></td>
						<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".$val['member_address']."</div></td>
						<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">".date('d-m-Y', strtotime($val['member_register_date']))."</div></td>
					</tr>";

					$no++;
				}
			}else{
				$tbl1 .= "
				<tr>
					<td width=\"100%\" colspan =\"3\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Data Kosong</div></td>
				</tr>";
			}

			$pdf->writeHTML($tbl0.$tbl1, true, false, false, '');

			ob_clean();
			$filename = 'Laporan Anggota Baru '.$kelompoklaporansimpanan.'.pdf';
			$pdf->Output($filename, 'I');
		}

		public function export($sesi){	
			$auth 				= $this->session->userdata('auth'); 
			
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$coremember 		= $this->NewMemberReport_model->getNewMemberReport($sesi['start_date'], $sesi['end_date']);
			
			if(count($coremember) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Anggota Baru")
									 ->setSubject("")
									 ->setDescription("Laporan Anggota Baru")
									 ->setKeywords("Laporan Anggota Baru")
									 ->setCategory("Laporan Anggota Baru");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				
				$this->excel->getActiveSheet()->mergeCells("B1:F1");
				$this->excel->getActiveSheet()->mergeCells("B2:F2");
				$this->excel->getActiveSheet()->getStyle('B1:B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Anggota Baru");
				$this->excel->getActiveSheet()->setCellValue('B2', date('d-m-Y', strtotime($sesi['start_date'])).' s/d '.date('d-m-Y', strtotime($sesi['end_date'])));
				
				$this->excel->getActiveSheet()->setCellValue('B3', "No");
				$this->excel->getActiveSheet()->setCellValue('C3', "No Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3', "Nama");
				$this->excel->getActiveSheet()->setCellValue('E3', "Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3', "Tanggal");
				
				$no		= 1;
				$row	= 4;
				foreach($coremember as $key => $val){
					$this->excel->getActiveSheet()->getStyle('B'.$row.':F'.$row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('D'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('E'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('F'.$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					
					$this->excel->getActiveSheet()->setCellValue('B'.($row), $no);								
					$this->excel->getActiveSheet()->setCellValue('C'.($row), $val['member_no']);
					$this->excel->getActiveSheet()->setCellValue('D'.($row), $val['member_name']);
					$this->excel->getActiveSheet()->setCellValue('E'.($row), $val['member_address']);
					$this->excel->getActiveSheet()->setCellValue('F'.($row), date('d-m-Y', strtotime($val['member_register_date'])));

					$no++;
					$row++;
				}

				$filename='Laporan Anggota Baru.xls';
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

	}
?>
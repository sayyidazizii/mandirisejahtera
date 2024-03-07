<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreMemberReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMemberReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('Configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth 	= $this->session->userdata('auth');

			if($auth['branch_status'] == 1){ 
				$sesi	= 	$this->session->userdata('filter-CoreMemberReport');
				if(!is_array($sesi)){
					$sesi['branch_id']		= $auth['branch_id'];
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}
			$data['main_view']['corebranch']		= create_double($this->CoreMemberReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['content'] 			= 'CoreMemberReport/FormFilterCoreMemberReport_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filter(){
			$data = array (
				"branch_id"					=> $this->input->post('branch_id',true),
				"member_character"	=> $this->input->post('member_character',true),
			);

			$this->session->set_userdata('filter-CoreMemberReport',$data);
			redirect('member-report');
		}

		public function viewreport(){
			$sesi = array (
				"branch_id"			=> $this->input->post('branch_id',true),
				"member_character"	=> $this->input->post('member_character',true),
				"view"				=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			$preferencecompany	= $this->CoreMemberReport_model->getPreferenceCompany();

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			
			$coremember 		= $this->CoreMemberReport_model->getCoreMember($sesi['member_character'], $branch_id);
			if($member_character == 9){
				$membercharacter = 'ANGGOTA BIASA DAN ANGGOTA';
			} else if($member_character == 0){
				$membercharacter = 'ANGGOTA BIASA';
			} else if($member_character == 1){
				$membercharacter = 'ANGGOTA LUAR BIASA';
			} else if($member_character == 2){
				$membercharacter = 'PENDIRI';
			}


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('tcpdf, PDF, example, test, guide');*/

			// set default header data
			/*$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE);
			$pdf->SetSubHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_STRING);*/

			// set header and footer fonts
			/*$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

			// set default monospaced font
			/*$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);*/

			// set margins
			/*$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);*/

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); // put space of 10 on top
			/*$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);*/
			/*$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);*/

			// set auto page breaks
			/*$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);*/

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set some language-dependent strings (optional)
			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// ---------------------------------------------------------

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
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
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12; font-weight:bold\">DAFTAR REGISTER : ".$membercharacter."</div></td>			       
			    </tr>						
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Anggota</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Simp Pokok</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Simp KHS</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Simp WJB</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($coremember as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['member_no']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['member_principal_savings_last_balance'], 2)."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['member_special_savings_last_balance'], 2)."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['member_mandatory_savings_last_balance'], 2)."</div></td>
				    </tr>
				";

				$simp_pokok += $val['member_principal_savings_last_balance'];

				$simp_khs 	+= $val['member_special_savings_last_balance'];

				$simp_wjb 	+= $val['member_mandatory_savings_last_balance'];

				$no++;
			}

			$tbl4 = "
					<tr>
						<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($simp_pokok, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($simp_khs, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($simp_wjb, 2)."</div></td>
					</tr>";
			

			$tbl5 = "							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4.$tbl5, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Data Normatif Anggota.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export($sesi){
			$member_character	= $this->input->post('member_character', true);

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			
			
			$coremember 		= $this->CoreMemberReport_model->getExportCoreMember($sesi['member_character'], $branch_id);
			//$coremember 		= $this->CoreMemberReport_model->getExportCoreMember($member_character);
			if($member_character == 9){
				$membercharacter = 'ANGGOTA BIASA DAN ANGGOTA';
			} else if($member_character == 0){
				$membercharacter = 'ANGGOTA BIASA';
			} else if($member_character == 1){
				$membercharacter = 'ANGGOTA LUAR BIASA';
			} else if($member_character == 2){
				$membercharacter = 'PENDIRI';
			}

			$this->load->library('Excel');
				
			$this->excel->getProperties()->setCreator("KSU MANDIRI")
								 ->setLastModifiedBy("KSU MANDIRI")
								 ->setTitle("Laporan Simpanan Anggota")
								 ->setSubject("")
								 ->setDescription("Laporan Simpanan Anggota")
								 ->setKeywords("Laporan, Anggota, Simpanan")
								 ->setCategory("Laporan Simpanan Anggota");
								 
			$this->excel->setActiveSheetIndex(0);
			$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
			$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
			$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
			$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
			$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);		

			
			$this->excel->getActiveSheet()->mergeCells("B1:H1");
			$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
			$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);
			
			$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR REGISTER ".$membercharacter);
			
			$this->excel->getActiveSheet()->setCellValue('B3',"No");
			$this->excel->getActiveSheet()->setCellValue('C3',"No. Anggota");
			$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
			$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
			$this->excel->getActiveSheet()->setCellValue('F3',"Simpanan Pokok");
			$this->excel->getActiveSheet()->setCellValue('G3',"Simpanan Wajib");
			$this->excel->getActiveSheet()->setCellValue('H3',"Simpanan Khusus");
			
			$j=3;
			$no=0;
				
			foreach($coremember as $key=>$val){
				if(is_numeric($key)){
					$no++;
					$j++;
					
					$this->excel->setActiveSheetIndex(0);
					$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					
					$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
					$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j,$val['member_no'],PHPExcel_Cell_DataType::TYPE_STRING);
					$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
					$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
					$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['member_principal_savings_last_balance'],2));
					$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['member_special_savings_last_balance'],2));
					$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['member_mandatory_savings_last_balance'],2));
					
					 }else{
					 	continue;
					 }
			}
				$filename='Laporan Data Normatif Anggota.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				ob_end_clean();
				$objWriter->save('php://output');
			
		
		}
	}
?>
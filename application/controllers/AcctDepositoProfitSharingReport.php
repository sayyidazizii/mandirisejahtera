<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctDepositoProfitSharingReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoProfitSharingReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$data['main_view']['corebranch']		= create_double($this->AcctDepositoProfitSharingReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content'] = 'AcctDepositoProfitSharingReport/FormFilterAcctDepositoProfitSharingReport_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 		=> tgltodb($this->input->post('start_date',true)),
				"end_date"			=> tgltodb($this->input->post('end_date', true)),
				"branch_id"			=> $this->input->post('branch_id',true),
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

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			
			$acctdepositoprofitsharing 	= $this->AcctDepositoProfitSharingReport_model->getAcctDepositoProfitSharing($sesi['start_date'], $sesi['end_date'], $branch_id);
			$preference					= $this->AcctDepositoProfitSharingReport_model->getPreferenceCompany();


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
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preference['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

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
			        <td><div style=\"text-align: left;font-size:12;\">DAFTAR BUNGA SIMP BERJANGKA BULAN INI</div></td>			       
			    </tr>						
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Jatuh Tempo</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Dep</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">BG Hasil</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Saldo</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Transfer</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalnominal = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctdepositoprofitsharing as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".tgltoview($val['deposito_profit_sharing_due_date'])."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['deposito_profit_sharing_amount'], 2)."</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_last_balance'], 2)."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".$val['savings_account_no']."</div></td>
				    </tr>
				";

				$totalnominal 	+= $val['deposito_account_last_balance'];

				$no++;
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoProfitSharingReport_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\"></div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR BUNGA SIMP BERJANGKA BULAN INI.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export($sesi){	
			$auth = $this->session->userdata('auth');


			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			
			$acctdepositoprofitsharing 	= $this->AcctDepositoProfitSharingReport_model->getAcctDepositoProfitSharing($sesi['start_date'], $sesi['end_date'], $branch_id);
			$preference					= $this->AcctDepositoProfitSharingReport_model->getPreferenceCompany();

			
			if(count($acctdepositoprofitsharing) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR BUNGA SIMP BERJANGKA BULAN INI")
									 ->setSubject("")
									 ->setDescription("DAFTAR BUNGA SIMP BERJANGKA BULAN INI")
									 ->setKeywords("DAFTAR, BUNGA, SIMP BERJANGKA")
									 ->setCategory("DAFTAR BUNGA SIMP BERJANGKA BULAN INI");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);			

				
				$this->excel->getActiveSheet()->mergeCells("B1:H1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR BUNGA SIMP BERJANGKA BULAN INI");

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('D3',"No. Simpanan Berjangka");
				$this->excel->getActiveSheet()->setCellValue('E3',"Nama");
				$this->excel->getActiveSheet()->setCellValue('F3',"Bagi Hasil");
				$this->excel->getActiveSheet()->setCellValue('G3',"Saldo");
				$this->excel->getActiveSheet()->setCellValue('H3',"Transfer");
				
				
				$no=0;
				$totalnominal = 0;
				$j=4;
				foreach($acctdepositoprofitsharing as $key=>$val){
					if(is_numeric($key)){
						$no++;
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
						$this->excel->getActiveSheet()->setCellValue('C'.$j, tgltoview($val['deposito_profit_sharing_due_date']));
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['deposito_account_no']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['deposito_profit_sharing_amount']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['deposito_account_last_balance']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['savings_account_no']);
			
						$totalnominal 	+= $val['deposito_account_last_balance'];
						
					}else{
						continue;
					}
					$j++;
				}

				$i = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, 'Total');
				$this->excel->getActiveSheet()->setCellValue('H'.$i, $totalnominal);
				
				$filename='DAFTAR BUNGA SIMP BERJANGKA BULAN INI.xls';
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
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsDailyMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsDailyMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$this->load->view('MainPage_view');
		}

		public function addCreditsAccount(){
			$data['main_view']['corebranch']		= create_double($this->AcctCreditsDailyMutation_model->getCoreBranch(),'branch_id','branch_name');

			$data['main_view']['content'] = 'AcctCreditsDailyMutation/FormFilterCreditsAccount_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport_account(){
			$sesi = array (
				'branch_id'		=> $this->input->post('branch_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				"view"			=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrintingCreditsAccount($sesi);
			} else {
				$this->exportCreditsAccount($sesi);
			}
		}

		public function processPrintingCreditsAccount($sesi){
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
			


			$acctcreditsaccount	= $this->AcctCreditsDailyMutation_model->getAcctCreditsAccount($sesi['start_date'], $sesi['end_date'], $branch_id);


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
			

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">DAFTAR PENCAIRAN PEMBIAYAAN TGL : &nbsp; ".tgltoview($sesi['start_date'])." - ".tgltoview($sesi['end_date'])."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. KREDIT</div></td>
			        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">ALAMAT</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">POKOK</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JK WAKTU</div></td>
			        <td width=\"12%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT TEMPO</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctcreditsaccount as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"12%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"25%\"><div style=\"text-align: center;\">".$val['member_address']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['credits_account_amount'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: center;\">".$val['credits_account_period']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".tgltoview($val['credits_account_due_date'])."</div></td>
				    </tr>
				";

				$totalsaldo 	+= $val['credits_account_amount'];

				$no++;
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsDailyMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"></td>
						<td style=\"border-top: 1px solid black\"></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan_Pencairan_Pembiyaan.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		public function exportCreditsAccount($sesi){	
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
			
			$acctcreditsaccount	= $this->AcctCreditsDailyMutation_model->getAcctCreditsAccount($sesi['start_date'], $sesi['end_date'], $branch_id);

			
			if(count($acctcreditsaccount) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("MUTASI PENCAIRAN PEMBIAYAAN")
									 ->setSubject("")
									 ->setDescription("MUTASI PENCAIRAN PEMBIAYAAN")
									 ->setKeywords("MUTASI, SETORAN, BERJANGKA")
									 ->setCategory("MUTASI PENCAIRAN PEMBIAYAAN");
									 
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
				
				$this->excel->getActiveSheet()->mergeCells("B2:H2");
				
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

				$this->excel->getActiveSheet()->getStyle('B4:H4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B4:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B4:H4')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI PENCAIRAN PEMBIAYAAN");
				
				$this->excel->getActiveSheet()->setCellValue('B2',"per Tanggal : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B4',"No");				
				$this->excel->getActiveSheet()->setCellValue('C4',"No Rek");
				$this->excel->getActiveSheet()->setCellValue('D4',"Nama ");	
				$this->excel->getActiveSheet()->setCellValue('E4',"Alamat");	
				$this->excel->getActiveSheet()->setCellValue('F4',"Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('G4',"Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('H4',"Pokok");
								
				$no=0;
				$totalnominal	= 0;
				$totalsaldo		= 0;
					$i=4;
					// foreach ($acctdeposito as $k => $v) {
					// $acctdepositocashdeposit	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashDeposit($sesi['start_date'], $sesi['end_date'], $v['deposito_id']);

					// 	if(!empty($acctdepositocashdeposit)){
						
					// 		$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
					// 		$this->excel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// 		$this->excel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
					// 		$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['deposito_name']);

						$nov= 0;
						$j=$i+1;
							foreach($acctcreditsaccount as $key=>$val){
								if(is_numeric($key)){
									$nov++;
									
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									
										$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
										$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
										$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
										$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
										$this->excel->getActiveSheet()->setCellValue('F'.$j,$val['credits_account_period']);
										$this->excel->getActiveSheet()->setCellValue('G'.$j,tgltoview($val['credits_account_due_date']));
										$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_amount'],2));
									
								}else{
									continue;
								}
								$j++;
						//	$totalnominal 	+= $val['deposito_cash_mutation_amount'];
							$totalsaldo 	+= $val['credits_account_amount'];
							}
						
					//}
					
					//$grandtotalnominal 	+= $totalnominal;
					$grandtotalsaldo	+= $totalsaldo;

				

				$n = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':H'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':H'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':G'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				//$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalnominal,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$n, number_format($grandtotalsaldo,2));
				
				$filename='Laporan_Pencairan_Pembiyaan.xls';
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

		public function addCreditsPayment(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsDailyMutation_model->getCoreBranch(),'branch_id','branch_name');

			$data['main_view']['coreoffice']	= create_double($this->AcctCreditsDailyMutation_model->getCoreOffice(),'office_id','office_name');

			$data['main_view']['content'] 		= 'AcctCreditsDailyMutation/FormFilterCreditsPayment_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport_payment(){
			$sesi = array (
				'branch_id'		=> $this->input->post('branch_id', true),
				'office_id'		=> $this->input->post('office_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				"view"			=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrintingCreditPayment($sesi);
			} else {
				$this->exportCreditsPayment($sesi);
			}
		}

		public function processPrintingCreditPayment($sesi){
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

			$acctcreditspayment	= $this->AcctCreditsDailyMutation_model->getAcctCreditsPayment($sesi['start_date'], $sesi['end_date'], $branch_id, $sesi['office_id']);


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
			

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">DAFTAR ANGSURAN PEMBIAYAAN TGL : &nbsp; ".tgltoview($sesi['start_date'])." - ".tgltoview($sesi['end_date'])."</div></td>		
			       	       
			    </tr>					
			</table>
			<br><br>";

			if(!empty($sesi['office_id'])){
				$tbl1 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">

				    <tr>
				        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">AO:  ".$this->AcctCreditsDailyMutation_model->getOfficeName($sesi['office_id'])."</div></td>	       
				    </tr>					
				</table>";

			} else {
				$tbl1 = "
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">

				    <tr>
				        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">GLOBAL</div></td>	       
				    </tr>					
				</table>";
			}

			$pdf->writeHTML($tbl.$tbl1, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. KREDIT</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">ALAMAT</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">ANGS POKOK</div></td>
			        <td width=\"13%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">ANGS BUNGA</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">DENDA</div></td>
			         <td width=\"15%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">TOTAL</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalpokok = 0;
			$totalmargin = 0;
			$totaltotal = 0;
			$totaldenda = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctcreditspayment as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"13%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['credits_payment_principal'])."</div></td>
				        <td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['credits_payment_interest'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_accumulated_fines'])."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['credits_account_payment_amount'])."</div></td>
				    </tr>
				";

				$totalpokok 	+= $val['credits_payment_principal'];
				$totalmargin 	+= $val['credits_payment_interest'];
				$totaltotal		+= $val['credits_account_payment_amount'];
				$totaldenda		+= $val['credits_account_accumulated_fines'];

				$no++;
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"3\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsDailyMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalpokok, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalmargin, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totaldenda, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totaltotal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan_Mutasi_Angsuran_Pembiayaan.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		public function exportCreditsPayment($sesi){	
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
			
			$acctcreditspayment	= $this->AcctCreditsDailyMutation_model->getAcctCreditsPayment($sesi['start_date'], $sesi['end_date'], $branch_id);

			
			if(count($acctcreditspayment) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("MUTASI ANGSURAN PEMBIAYAAN")
									 ->setSubject("")
									 ->setDescription("MUTASI ANGSURAN PEMBIAYAAN")
									 ->setKeywords("MUTASI, SETORAN, BERJANGKA")
									 ->setCategory("MUTASI ANGSURAN PEMBIAYAAN");
									 
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
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);	

				
				$this->excel->getActiveSheet()->mergeCells("B1:I1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				
				$this->excel->getActiveSheet()->mergeCells("B2:I2");
				
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

				$this->excel->getActiveSheet()->mergeCells("B4:I4");
				
				$this->excel->getActiveSheet()->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true)->setSize(11);

				$this->excel->getActiveSheet()->getStyle('B5:I5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B5:I5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B5:I5')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI ANGSURAN PEMBIAYAAN");
				
				$this->excel->getActiveSheet()->setCellValue('B2',"per Tanggal : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

				if(!empty($sesi['office_id'])){
					$this->excel->getActiveSheet()->setCellValue('B4',"AO : ".$this->AcctCreditsDailyMutation_model->getOfficeName($sesi['office_id']));
				} else {
					$this->excel->getActiveSheet()->setCellValue('B4',"GLOBAL");
				}

				


				
				$this->excel->getActiveSheet()->setCellValue('B5',"No");				
				$this->excel->getActiveSheet()->setCellValue('C5',"No Rek");
				$this->excel->getActiveSheet()->setCellValue('D5',"Nama ");	
				$this->excel->getActiveSheet()->setCellValue('E5',"Alamat");	
				$this->excel->getActiveSheet()->setCellValue('F5',"Angsuran Pokok");
				$this->excel->getActiveSheet()->setCellValue('G5',"Angsuran Bunga");
				$this->excel->getActiveSheet()->setCellValue('H5',"Denda");
				$this->excel->getActiveSheet()->setCellValue('I5',"Total");
								
				$no=0;
				$totalpokok = 0;
				$totalmargin = 0;
				$totaltotal = 0;
				$totaldenda = 0;
					$i=5;
					// foreach ($acctdeposito as $k => $v) {
					// $acctdepositocashdeposit	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashDeposit($sesi['start_date'], $sesi['end_date'], $v['deposito_id']);

					// 	if(!empty($acctdepositocashdeposit)){
						
					// 		$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
					// 		$this->excel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					// 		$this->excel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
					// 		$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['deposito_name']);

						$nov= 0;
						$j=$i+1;
							foreach($acctcreditspayment as $key=>$val){
								if(is_numeric($key)){
									$nov++;
									
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									
										$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
										$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
										$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
										$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
										$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_payment_principal'],2));
										$this->excel->getActiveSheet()->setCellValue('G'.$j,number_format($val['credits_payment_interest'],2));
										$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_accumulated_fines'],2));
										$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_account_payment_amount'],2));
									
								}else{
									continue;
								}
								$j++;
													
							$totalpokok 	+= $val['credits_payment_principal'];
							$totalmargin 	+= $val['credits_payment_interest'];
							$totaltotal		+= $val['credits_account_payment_amount'];
							$totaldenda		+= $val['credits_account_accumulated_fines'];
						}
						
					//}
					

				$n = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':I'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':I'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');
				$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totalpokok,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($totalmargin,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$n, number_format($totaldenda,2));
				$this->excel->getActiveSheet()->setCellValue('I'.$n, number_format($totaltotal,2));
				
				$filename='Laporan_Mutasi_Angsuran_Pembiayaan.xls';
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
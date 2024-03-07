<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctDepositoDailyCashMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoDailyCashMutation_model');
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

		public function addCashDeposit(){
			$corebranch 						= create_double_branch($this->AcctDepositoDailyCashMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctDepositoDailyCashMutation/FormAddAcctDepositoCashDeposit_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport_deposit(){
			$sesi = array (
				//'office_id'		=> $this->input->post('office_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				"view"			=> $this->input->post('view',true),
				"branch_id"		=> $this->input->post('branch_id',true),
			);
			
			if($sesi['view'] == 'pdf'){
				$this->processPrintingCashDeposit($sesi);
			} else {
				$this->exportCashDeposit($sesi);
			}
		}

		public function processPrintingCashDeposit($sesi){
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
			
			$preference		= $this->AcctDepositoDailyCashMutation_model->getPreferenceCompany();
			$acctdeposito 	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito();

			
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
			        <td><div style=\"text-align: left;font-size:12;\">".$preference['company_name']."</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI SETORAN BERJANGKA TGL : &nbsp; ".tgltoview($sesi['start_date'])." - ".tgltoview($sesi['end_date'])."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK WAKTU</div></td>
			        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT TEMPO</div></td>
			        <td width=\"18%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">SALDO</div></td>
			       
			    </tr>				
			</table>";


			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctdeposito as $kD => $vD) {
				$acctdepositocashdeposit	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashDeposit($sesi['start_date'], $sesi['end_date'], $vD['deposito_id'], $branch_id);
				if(!empty($acctdepositocashdeposit)){
					$tbl3 .= "
						<br>
						<tr>
							<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
						</tr>
					";

					$no = 1;
					$totalsaldo = 0;
					foreach ($acctdepositocashdeposit as $key => $val) {
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"16%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"12%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
						        <td width=\"17%\"><div style=\"text-align: left;\">".tgltoview($val['deposito_account_due_date'])."</div></td>
						        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
						    </tr>
						";

						$totalsaldo 	+= $val['deposito_account_amount'];

						$no++;
					}
					$tbl3 .= "
						<tr>
							<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
						</tr>";

					$grandtotalsaldo += $totalsaldo;
				}
			}

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoDailyCashMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Mutasi Setoran Simpanan Berjangka.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function exportCashDeposit($sesi){	
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
			$acctdeposito 			= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito();		
			$preferencecompany 		= $this->AcctDepositoDailyCashMutation_model->getPreferenceCompany();

			
			
			if(count($acctdeposito) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("MUTASI SETORAN BERJANGKA")
									 ->setSubject("")
									 ->setDescription("MUTASI SETORAN SIMPANAN BERJANGKA")
									 ->setKeywords("MUTASI, SETORAN,SIMPANAN, BERJANGKA")
									 ->setCategory("MUTASI SETORAN SIMPANAN BERJANGKA");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);	

				
				$this->excel->getActiveSheet()->mergeCells("B1:G1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				
				$this->excel->getActiveSheet()->mergeCells("B2:G2");
				
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

				$this->excel->getActiveSheet()->getStyle('B3:G3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:G3')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI SETORAN SIMPANAN BERJANGKA");
				
				$this->excel->getActiveSheet()->setCellValue('B2',"per Tanggal : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");				
				$this->excel->getActiveSheet()->setCellValue('C3',"No Rek");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama ");	
				$this->excel->getActiveSheet()->setCellValue('E3',"Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('F3',"Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('G3',"Saldo");
								
				$no=0;
				$totalnominal	= 0;
				$totalsaldo		= 0;
					$i=4;
					foreach ($acctdeposito as $k => $v) {
					$acctdepositocashdeposit	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashDeposit($sesi['start_date'], $sesi['end_date'], $v['deposito_id'], $branch_id);

						if(!empty($acctdepositocashdeposit)){
						
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['deposito_name']);

						$nov= 0;
						$j=$i+1;
							$totalsaldo = 0;
							foreach($acctdepositocashdeposit as $key=>$val){
								if(is_numeric($key)){
									$nov++;
									
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									
										$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
										$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['deposito_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
										$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
										$this->excel->getActiveSheet()->setCellValueExplicit('E'.$j,$val['deposito_account_period']);
										$this->excel->getActiveSheet()->setCellValue('F'.$j,tgltoview($val['deposito_account_due_date']));
										$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['deposito_account_amount'],2));
									
								}else{
									continue;
								}
								$j++;
						//	$totalnominal 	+= $val['deposito_cash_mutation_amount'];
							$totalsaldo 	+= $val['deposito_account_amount'];
							}


						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':G'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':G'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						//$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalnominal,2));
						$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalsaldo,2));

						$i = $m + 1;
						$grandtotalsaldo	+= $totalsaldo;
						}


					}
					
					//$grandtotalnominal 	+= $totalnominal;
					
				

				$n = $i;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':G'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':G'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':F'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				//$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalnominal,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalsaldo,2));
				
				$filename='Laporan Mutasi Setoran Simpanan Berjangka.xls';
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

		public function addCashWithdrawal(){
			$corebranch 						= create_double_branch($this->AcctDepositoDailyCashMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctDepositoDailyCashMutation/FormAddAcctDepositoCashWithdrawal_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport_withdrawl(){
			$sesi = array (
				//'office_id'		=> $this->input->post('office_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				"branch_id"		=> $this->input->post('branch_id',true),
				"view"			=> $this->input->post('view',true),
			);
			
			if($sesi['view'] == 'pdf'){
				$this->processPrintingCashWithdrawal($sesi);
			} else {
				$this->exportCashWithDrawl($sesi);
			}
		}

		public function processPrintingCashWithdrawal($sesi){
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


			
			$preference		= $this->AcctDepositoDailyCashMutation_model->getPreferenceCompany();
			$acctdeposito 	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito();



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
			        <td><div style=\"text-align: left;font-size:12;\">".$preference['company_name']."</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI PENARIKAN BERJANGKA TGL : &nbsp; ".tgltoview($sesi['start_date'])." - ".tgltoview($sesi['end_date'])."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK WAKTU</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">JT TEMPO</div></td>
			        <td width=\"18%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">SALDO</div></td>
			       
			    </tr>				
			</table>";

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($acctdeposito as $kD => $vD) {
				$acctdepositocashwithdrawal	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashWithdrawal($sesi['start_date'], $sesi['end_date'], $vD['deposito_id'], $branch_id);
				if(!empty($acctdepositocashwithdrawal)){
					$tbl3 .= "
						<br>
						<tr>
							<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
						</tr>
					";

					$no = 1;
					$totalsaldo = 0;
					foreach ($acctdepositocashwithdrawal as $key => $val) {
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"16%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"12%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
						         <td width=\"16%\"><div style=\"text-align: left;\">".tgltoview($val['deposito_account_due_date'])."</div></td>
						        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
						    </tr>
						";

						$totalsaldo 	+= $val['deposito_account_amount'];

						$no++;
					}
					$tbl3 .= "
						<tr>
							<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
							<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
						</tr>";

					$grandtotalsaldo += $totalsaldo;
				}
			}
			

			$tbl4 = "
				<br>
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoDailyCashMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Mutasi Penarikan Simpanan Berjangka.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function exportCashWithDrawl($sesi){	
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
			$acctdeposito 			= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito();		
			$preferencecompany 		= $this->AcctDepositoDailyCashMutation_model->getPreferenceCompany();

			
			
			if(count($acctdeposito) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("MUTASI PENARIKAN SIMPANAN BERJANGKA")
									 ->setSubject("")
									 ->setDescription("MUTASI PENARIKAN SIMPANAN BERJANGKA")
									 ->setKeywords("MUTASI, PENARIKAN, SIMPANAN, BERJANGKA")
									 ->setCategory("MUTASI PENARIKAN SIMPANAN BERJANGKA");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);	

				
				$this->excel->getActiveSheet()->mergeCells("B1:G1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				
				$this->excel->getActiveSheet()->mergeCells("B2:G2");
				
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

				$this->excel->getActiveSheet()->getStyle('B3:G3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:G3')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI PENARIKAN SIMPANAN BERJANGKA");
				
				$this->excel->getActiveSheet()->setCellValue('B2',"per Tanggal : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");				
				$this->excel->getActiveSheet()->setCellValue('C3',"No Rek");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama ");	
				$this->excel->getActiveSheet()->setCellValue('E3',"Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('F3',"Jatuh Tempo");
				$this->excel->getActiveSheet()->setCellValue('G3',"Saldo");
								
				$no=0;
				$totalnominal	= 0;
				$totalsaldo		= 0;
					$i=4;
					foreach ($acctdeposito as $k => $v) {
						$acctdepositocashwithdrawal	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashWithdrawal($sesi['start_date'], $sesi['end_date'], $v['deposito_id'], $branch_id);
						//$acctdepositocashdeposit	= $this->AcctDepositoDailyCashMutation_model->getAcctDeposito_CashWithdrawal($sesi['start_date'], $sesi['end_date'], $v['deposito_id'], $branch_id);

						if(!empty($acctdepositocashwithdrawal)){
						
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['deposito_name']);

						$nov= 0;
						$j=$i+1;
						$totalsaldo = 0;
							foreach($acctdepositocashwithdrawal as $key=>$val){
								if(is_numeric($key)){
									$nov++;
									
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':G'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									
										$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
										$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['deposito_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
										$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
										$this->excel->getActiveSheet()->setCellValueExplicit('E'.$j,$val['deposito_account_period']);
										$this->excel->getActiveSheet()->setCellValue('F'.$j,tgltoview($val['deposito_account_due_date']));
										$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['deposito_account_amount'],2));
									
								}else{
									continue;
								}
								$j++;
						//	$totalnominal 	+= $val['deposito_cash_mutation_amount'];
							$totalsaldo 	+= $val['deposito_account_amount'];
							}


						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':G'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':G'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						//$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalnominal,2));
						$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalsaldo,2));

						$i = $m + 1;
						$grandtotalsaldo	+= $totalsaldo;

						}
					}
					
					//$grandtotalnominal 	+= $totalnominal;

				

				$n = $i;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':G'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':G'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':F'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				//$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalnominal,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalsaldo,2));
				
				$filename='Laporan Mutasi Simpanan Berjangka.xls';
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
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsDailyCashMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsDailyCashMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			
		}

		public function addCashDeposit(){
			$corebranch 						= create_double_branch($this->AcctSavingsDailyCashMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] = 'AcctSavingsDailyCashMutation/FormAddAcctSavingsCashDeposit_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport_cashdeposit(){
			$sesi = array (
				//'office_id'		=> $this->input->post('office_id', true),
				'branch_id' 	=> $this->input->post('branch_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				"view"			=> $this->input->post('view',true),
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

			
			$preference		= $this->AcctSavingsDailyCashMutation_model->getPreferenceCompany();
			$acctsavings 	= $this->AcctSavingsDailyCashMutation_model->getAcctSavings();

			


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
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI SETORAN TUNAI TGL : &nbsp;&nbsp; ".tgltoview($sesi['start_date'])."&nbsp;&nbsp; S.D &nbsp;&nbsp;".tgltoview($sesi['end_date'])."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"11%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">TANGGAL</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">SANDI</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">NOMINAL</div></td>
			        <td width=\"17%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Saldo</div></td>
			       
			    </tr>				
			</table>";

			

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			foreach ($acctsavings as $kS => $vS) {
				$acctsavingscashdeposit	= $this->AcctSavingsDailyCashMutation_model->getAcctSavings_CashDeposit($sesi['start_date'], $sesi['end_date'], $preference['cash_deposit_id'], $vS['savings_id'], $branch_id);

				if(!empty($acctsavingscashdeposit)){
					$tbl3 .= "
						<br>
						<tr>
							<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vS['savings_name']."</div></td>
						</tr>
					";

					$no = 1;
					$totalnominal 	= 0;
					$totalsaldo		= 0;
					$grandtotalnominal	=0;
					$grandtotalsaldo	=0;
					foreach ($acctsavingscashdeposit as $key => $val) {
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_cash_mutation_date'])."</div></td>
						        <td width=\"16%\"><div style=\"text-align: left;\">".$val['savings_account_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctSavingsDailyCashMutation_model->getMutationCode($preference['cash_deposit_id'])."</div></td>
						        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['savings_cash_mutation_amount'], 2)."</div></td>
						        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['savings_cash_mutation_last_balance'], 2)."</div></td>
						    </tr>
						";

						$totalnominal 	+= $val['savings_cash_mutation_amount'];
						$totalsaldo 	+= $val['savings_cash_mutation_last_balance'];

						$no++;
					}

					$tbl3 .= "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
					</tr>";

					$grandtotalnominal 	+= $totalnominal;
					$grandtotalsaldo	+= $totalsaldo;
				}
			}
			
			$tbl4 = "
				<br>
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctSavingsDailyCashMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Mutasi Setoran Simpanan.pdf';
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
			$acctsavings 			= $this->AcctSavingsDailyCashMutation_model->getAcctSavings();			
			$preferencecompany 		= $this->AcctSavingsDailyCashMutation_model->getPreferenceCompany();

			
			
			if(count($acctsavings) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("MUTASI SETORAN SIMPANAN")
									 ->setSubject("")
									 ->setDescription("MUTASI SETORAN SIMPANAN")
									 ->setKeywords("MUTASI, SETORAN, SIMPANAN")
									 ->setCategory("MUTASI SETORAN SIMPANAN");
									 
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

				
				$this->excel->getActiveSheet()->mergeCells("B1:H1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				
				$this->excel->getActiveSheet()->mergeCells("B2:H2");
				
				$this->excel->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B2')->getFont()->setSize(11);

				$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI SETORAN SIMPANAN");
				
				$this->excel->getActiveSheet()->setCellValue('B2',"per Tanggal : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"Tanggal");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"No Rek");	
				$this->excel->getActiveSheet()->setCellValue('F3',"Sandi");
				$this->excel->getActiveSheet()->setCellValue('G3',"Nominal");
				$this->excel->getActiveSheet()->setCellValue('H3',"Saldo");
								
				$no=0;
				$totalnominal	= 0;
				$totalsaldo		= 0;
					$i=4;
					foreach ($acctsavings as $k => $v) {
					$acctsavingscashdeposit	= $this->AcctSavingsDailyCashMutation_model->getAcctSavings_CashDeposit($sesi['start_date'], $sesi['end_date'], $preferencecompany['cash_deposit_id'], $v['savings_id'], $branch_id);

						if(!empty($acctsavingscashdeposit)){
						
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':H'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['savings_name']);

						$nov= 0;
						$j=$i+1;
						foreach($acctsavingscashdeposit as $key=>$val){
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
									$this->excel->getActiveSheet()->setCellValue('C'.$j, tgltoview($val['savings_cash_mutation_date']));
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValueExplicit('E'.$j, $val['savings_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValueExplicit('F'.$j, $this->AcctSavingsDailyCashMutation_model->getMutationCode($preferencecompany['cash_deposit_id'],PHPExcel_Cell_DataType::TYPE_STRING));
									$this->excel->getActiveSheet()->setCellValue('G'.$j,number_format($val['savings_cash_mutation_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['savings_cash_mutation_last_balance'],2));
								
							}else{
								continue;
							}
							$j++;
						$totalnominal 	+= $val['savings_cash_mutation_amount'];
						$totalsaldo 	+= $val['savings_cash_mutation_last_balance'];
						}

						

						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalnominal,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($totalsaldo,2));

						$i = $m + 1;
						}
					}
					
					$grandtotalnominal 	+= $totalnominal;
					$grandtotalsaldo	+= $totalsaldo;

				

				$n = $i;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':H'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':H'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':F'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalnominal,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$n, number_format($grandtotalsaldo,2));
				
				$filename='Laporan Mutasi Setoran Simpanan.xls';
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
			$corebranch 						= create_double_branch($this->AcctSavingsDailyCashMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctSavingsDailyCashMutation/FormAddAcctSavingsCashWithdrawal_view';
			$this->load->view('MainPage_view', $data);
			
		}

		public function viewreport_cashwithdrawl(){
			$sesi = array (
				//'office_id'		=> $this->input->post('office_id', true),
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'view'			=> $this->input->post('view',true),
				'branch_id' 	=> $this->input->post('branch_id', true),

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


			
			$preference		= $this->AcctSavingsDailyCashMutation_model->getPreferenceCompany();
			$acctsavings 	= $this->AcctSavingsDailyCashMutation_model->getAcctSavings();



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
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI PENARIKAN SIMPANAN TGL : &nbsp;&nbsp; ".tgltoview($sesi['start_date'])."&nbsp;&nbsp; S.D &nbsp;&nbsp;".tgltoview($sesi['end_date'])."</div></td>		
			       	       
			    </tr>					
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">NO.</div></td>
			        <td width=\"11%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">TANGGAL</div></td>
			        <td width=\"16%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NO. REK</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">NAMA</div></td>
			        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">SANDI</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">NOMINAL</div></td>
			        <td width=\"17%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Saldo</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalnominal 	= 0;
			$totalsaldo		= 0;
			$grandtotalnominal 	= 0;
			$grandtotalsaldo	= 0;
			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($acctsavings as $kS => $vS) {
				$acctsavingscashwithdrawal	= $this->AcctSavingsDailyCashMutation_model->getAcctSavings_CashWithdrawal($sesi['start_date'], $sesi['end_date'], $preference['cash_withdrawal_id'], $vS['savings_id'], $branch_id);

				if(!empty($acctsavingscashwithdrawal)){
					$tbl3 .= "
						<br>
						<tr>
							<td colspan =\"6\" style=\"border-bottom: 1px solid black;\"><div style=\"font-size:10\">".$vS['savings_name']."</div></td>
						</tr>
					";

					$no = 1;
					foreach ($acctsavingscashwithdrawal as $key => $val) {
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_cash_mutation_date'])."</div></td>
						        <td width=\"16%\"><div style=\"text-align: left;\">".$val['savings_account_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctSavingsDailyCashMutation_model->getMutationCode($preference['cash_withdrawal_id'])."</div></td>
						        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['savings_cash_mutation_amount'], 2)."</div></td>
						        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($val['savings_cash_mutation_last_balance'], 2)."</div></td>
						    </tr>
						";

						$totalnominal 	+= $val['savings_cash_mutation_amount'];
						$totalsaldo 	+= $val['savings_cash_mutation_last_balance'];

						$no++;
					}

					$tbl3 .= "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalsaldo, 2)."</div></td>
					</tr>";

					$grandtotalnominal 	+= $totalnominal;
					$grandtotalsaldo	+= $totalsaldo;
				}
			}
			

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctSavingsDailyCashMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Mutasi Penarikan Simpanan.pdf';
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
			$acctsavings 			= $this->AcctSavingsDailyCashMutation_model->getAcctSavings();			
			$preferencecompany 		= $this->AcctSavingsDailyCashMutation_model->getPreferenceCompany();

			
			
			if(count($acctsavings) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("MUTASI PENARIKAN SIMPANAN")
									 ->setSubject("")
									 ->setDescription("MUTASI PENARIKAN SIMPANAN")
									 ->setKeywords("MUTASI, PENARIKAN, SIMPANAN")
									 ->setCategory("MUTASI PENARIKAN SIMPANAN");
									 
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

				$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);
				
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI PENARIKAN SIMPANAN");
				
				$this->excel->getActiveSheet()->setCellValue('B2',"per Tanggal : ".tgltoview($sesi['start_date'])." S.D ".tgltoview($sesi['end_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"Tanggal");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"No Rek");	
				$this->excel->getActiveSheet()->setCellValue('F3',"Sandi");
				$this->excel->getActiveSheet()->setCellValue('G3',"Nominal");
				$this->excel->getActiveSheet()->setCellValue('H3',"Saldo");
								
				$no=0;
				$totalnominal	= 0;
				$totalsaldo		= 0;
					$i=4;
					foreach ($acctsavings as $k => $v) {

					$acctsavingscashwithdrawal	= $this->AcctSavingsDailyCashMutation_model->getAcctSavings_CashWithdrawal($sesi['start_date'], $sesi['end_date'], $preferencecompany['cash_withdrawal_id'], $v['savings_id'], $branch_id);

						if(!empty($acctsavingscashwithdrawal)){
						
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':H'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['savings_name']);

						$nov= 0;
						$j=$i+1;
						foreach($acctsavingscashwithdrawal as $key=>$val){
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
									$this->excel->getActiveSheet()->setCellValue('C'.$j, tgltoview($val['savings_cash_mutation_date']));
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValueExplicit('E'.$j, $val['savings_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValueExplicit('F'.$j, $this->AcctSavingsDailyCashMutation_model->getMutationCode($preferencecompany['cash_withdrawal_id'],PHPExcel_Cell_DataType::TYPE_STRING));
									$this->excel->getActiveSheet()->setCellValue('G'.$j,number_format($val['savings_cash_mutation_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['savings_cash_mutation_last_balance'],2));
								
							}else{
								continue;
							}
							$j++;
						$totalnominal 	+= $val['savings_cash_mutation_amount'];
						$totalsaldo 	+= $val['savings_cash_mutation_last_balance'];
						}

						

						$m = $j;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalnominal,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($totalsaldo,2));

						$i = $m + 1;
						}
					}
					
					$grandtotalnominal 	+= $totalnominal;
					$grandtotalsaldo	+= $totalsaldo;

				

				$n = $i;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':H'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':H'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':F'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($grandtotalnominal,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$n, number_format($grandtotalsaldo,2));
				
				$filename='Laporan Mutasi Penarikan Simpanan.xls';
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
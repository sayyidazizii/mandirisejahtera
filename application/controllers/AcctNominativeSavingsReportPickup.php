<?php ob_start(); ?>
<?php
	ini_set('memory_limit', '512M');
	defined('BASEPATH') OR exit('No direct script access allowed');


	Class AcctNominativeSavingsReportPickup extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctNominativeSavingsReportPickup_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->AcctNominativeSavingsReportPickup_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['acctsavings']				= create_double($this->AcctNominativeSavingsReportPickup_model->getAcctSavings(),'savings_id','savings_name');
			$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanSimpanan1();	
			$data['main_view']['content']					= 'AcctNominativeSavingsReportPickup/ListAcctNominativeSavingsReportPickup_view';
			$this->load->view('MainPage_view',$data);
		}
 
		public function viewreport(){
			$sesi = array (
				"branch_id"					=> $this->input->post('branch_id', true),
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"kelompok_laporan_simpanan"	=> $this->input->post('kelompok_laporan_simpanan',true),
				"view"						=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			$preferencecompany = $this->AcctNominativeSavingsReportPickup_model->getPreferenceCompany();
			
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
			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup1($sesi['start_date']);

			$data[] = array ();

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['savings'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['savings_cash_mutation_date'],
					'transaction_amount'	=> $val['savings_cash_mutation_amount'],
					'transaction_code'		=> $val['mutation_name'],
					'transaction_remark'	=> $val['savings_cash_mutation_remark'],
				);
			}

			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup($sesi['start_date']);

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['mandatory'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['savings_cash_mutation_date'],
					'transaction_amount'	=> $val['savings_cash_mutation_amount'],
					'transaction_code'		=> $val['mutation_name'],
					'transaction_remark'	=> $val['savings_cash_mutation_remark'],
				);
			}

			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup2($sesi['start_date']);

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['tariktunai'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['savings_cash_mutation_date'],
					'transaction_amount'	=> $val['savings_cash_mutation_amount'],
					'transaction_code'		=> $val['mutation_name'],
					'transaction_remark'	=> $val['savings_cash_mutation_remark'],
				);
			}

			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup3($sesi['start_date']);

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['angs'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['credits_payment_date'],
					'transaction_amount'	=> $val['credits_payment_principal'],
				);
			}

			//print_r($data['savings']);exit;


		// 	$mutation	 					= $this->AcctNominativeSavingsReportPickup_model->getPickupMutation();


		// 	if($sesi['kelompok_laporan_simpanan'] == 0){


		// 	foreach ($acctsavingsaccount_pickup as $key => $val) {

		// 		$mutation4	 					= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup1();

		// 		foreach ($mutation4 as $k => $v) {

		// 		$data_acctsavingsaccount_pickup[] = array (
		// 				'operated_name'					=> $val['operated_name'],
		// 				'savings_cash_mutation_date'	=> $val['savings_cash_mutation_date'],
		// 				'savings_cash_mutation_amount'	=> $val['savings_cash_mutation_amount'],
		// 				'savings_cash_mutation_amount1'	=> $v['savings_cash_mutation_amount'],
						
		// 			);

		// 		}
		// 	}
			
		// 	} else {

			
		// 	foreach ($mutation as $key => $vS) {

		// 		$acctsavingsaccountpickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNomintiveSavingsReport_Pickup($vS['mutation_id']);

		// 		foreach ($acctsavingsaccountpickup as $key => $val) {

		// 		$data_acctsavingsaccount_pickup[$vS['mutation_id']][] = array (
		// 				'operated_name'					=> $val['operated_name'],
		// 				'savings_cash_mutation_date'	=> $val['savings_cash_mutation_date'],
		// 				'mutation_name'					=> $val['mutation_name'],
		// 				'savings_cash_mutation_remark'	=> $val['savings_cash_mutation_remark'],
		// 				'savings_cash_mutation_amount'	=> $val['savings_cash_mutation_amount'],
						
		// 			);

		// 		}

		// 	}
		// }
		
			
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
			<br/>";

			if($sesi['kelompok_laporan_simpanan'] == 0){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">LAPORAN PICKUP</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])."</div></td>
					    </tr>
					</table>";
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">LAPORAN PICKUP PER JENIS TRANSAKSI</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])."</div></td>
					    </tr>
					</table>";

			}
		
			$pdf->writeHTML($tbl0.$tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Tanggal</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Nama Operator</div></td>
			         <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Transaksi</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Jumlah</div></td>
			         <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Keterangan</div></td>
			         
			       
			       
			    </tr>				
			</table>";

			$no=1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			$totalsaldo = 0;
			$subtotalsaldo = 0;
			$subtotalsaldo1 = 0;
			$subtotalsaldo2 = 0;
			$subtotalsaldo3 = 0;

						$tbllabel .= "
							<br>
							<tr>
								<td colspan =\"7\" width=\"100%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">SETORAN TUNAI</div></td>
							</tr>
							<br>
						";

						$no = 1;
						foreach ($data['savings'] as $key => $val) {
						
							$tblsavings .= "
								<tr>
							  
					       		 <td width=\"15%\"><div style=\"text-align: left;\">".$val['transaction_date']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val['operated_name']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val['transaction_code']."</div></td>
					         	<td width=\"15%\"><div style=\"text-align: left;\">".number_format($val['transaction_amount'], 2)."</div></td>
					        	<td width=\"20%\"><div style=\"text-align: left;\">".$val['transaction_remark']."</div></td>
							    </tr>

							";

							$subtotalsaldo += $val['transaction_amount'];

							$no++;
						}

						$tblsubtotal1 .= "
							<br>
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalsaldo, 2)."</div></td>
							</tr>
							<br>
						";

						$totalsaldo += $subtotalsaldo;
					

					$tbllabel2 .= "
							<br>
							<tr>
								<td colspan =\"7\" width=\"100%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">TARIK TUNAI</div></td>
							</tr>
							<br>
						";

						foreach ($data['tariktunai'] as $key2 => $val2) {
						
							$tbltt .= "
								<tr>
							   
					       		 <td width=\"15%\"><div style=\"text-align: left;\">".$val2['transaction_date']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val2['operated_name']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val2['transaction_code']."</div></td>
					         	<td width=\"15%\"><div style=\"text-align: left;\">".number_format($val2['transaction_amount'], 2)."</div></td>
					        	<td width=\"20%\"><div style=\"text-align: left;\">".$val2['transaction_remark']."</div></td>
							    </tr>

							";

							$subtotalsaldo2 += $val2['transaction_amount'];

						}

						$tblsubtotal2 .= "
							<br>
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalsaldo2, 2)."</div></td>
							</tr>
							<br>
						";

						$totalsaldo += $subtotalsaldo2;
					

					$tbllabel3 .= "
							<br>
							<tr>
								<td colspan =\"7\" width=\"100%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">ANGSURAN</div></td>
							</tr>
							<br>
						";

						$no = 1;
						foreach ($data['angs'] as $key3 => $val3) {
						
							$tblangs .= "
								<tr>
							   
					       		 <td width=\"15%\"><div style=\"text-align: left;\">".$val3['transaction_date']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val3['operated_name']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">Angsuran</div></td>
					         	<td width=\"15%\"><div style=\"text-align: left;\">".number_format($val3['transaction_amount'], 2)."</div></td>
					        	<td width=\"20%\"><div style=\"text-align: left;\">Angsuran</div></td>
							    </tr>

							";

							$subtotalsaldo3 += $val3['transaction_amount'];

							$no++;
						}

						$tblsubtotal3 .= "
							<br>
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalsaldo3, 2)."</div></td>
							</tr>
							<br>
						";

						$totalsaldo += $subtotalsaldo3;
					

					$tbllabel4 .= "
							<br>
							<tr>
								<td colspan =\"7\" width=\"100%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">SIMPANAN WAJIB</div></td>
							</tr>
							<br>
						";

						$no = 1;
						foreach ($data['mandatory'] as $key4 => $val4) {
						
							$tblmandatory .= "
								<tr>
							   
					       		 <td width=\"15%\"><div style=\"text-align: left;\">".$val4['transaction_date']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val4['operated_name']."</div></td>
					       		 <td width=\"20%\"><div style=\"text-align: left;\">".$val4['transaction_code']."</div></td>
					         	<td width=\"15%\"><div style=\"text-align: left;\">".number_format($val4['transaction_amount'], 2)."</div></td>
					        	<td width=\"20%\"><div style=\"text-align: left;\">".$val4['transaction_remark']."</div></td>
							    </tr>

							";

							$subtotalsaldo4 += $val4['transaction_amount'];

							$no++;
						}

						$tblsubtotal4 .= "
							<br>
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($subtotalsaldo4, 2)."</div></td>
							</tr>
							<br>
						";

						$totalsaldo += $subtotalsaldo4;
					
				


			$tbl4 .= "
				<br>
				<tr>
					<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:center\">".number_format($totalsaldo, 2)."</div></td>
				</tr>
				</table>
				<br>
			";

			$tbltot .= "
				<br>
				<tr>
					<td colspan =\"4\"><div style=\"font-size:10;text-align:left;font-style:italic\"></div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:center\">".number_format($totalsaldo, 2)."</div></td>
				</tr>
				</table>
				<br>
			";
				
		if($sesi['kelompok_laporan_simpanan'] == 0){
			$pdf->writeHTML($tbl1.$tbl2.$tblsavings.$tbltt.$tblangs.$tblmandatory.$tbl4, true, false, false, false, false, false, false, '');

		}else{

			$pdf->writeHTML($tbl1.$tbl2.$tbllabel.$tblsavings.$tblsubtotal1.$tbllabel2.$tbltt.$tblsubtotal2.$tbllabel3.$tblangs.$tblsubtotal3.$tbllabel4.$tblmandatory.$tblsubtotal4.$tbltot, true, false, false, false, false, false, false, false, false, false, false, false, false, false, false, '');
			
		}

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Nominatif Simpanan '.$kelompoklaporansimpanan.'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export($sesi){	
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


			$kelompoklaporansimpanan		= $this->configuration->KelompokLaporanSimpanan1();
			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup1($sesi['start_date']);

			$data[] = array ();

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['savings'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['savings_cash_mutation_date'],
					'transaction_amount'	=> $val['savings_cash_mutation_amount'],
					'transaction_code'		=> $val['mutation_name'],
					'transaction_remark'	=> $val['savings_cash_mutation_remark'],
				);
			}

			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup($sesi['start_date']);

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['mandatory'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['savings_cash_mutation_date'],
					'transaction_amount'	=> $val['savings_cash_mutation_amount'],
					'transaction_code'		=> $val['mutation_name'],
					'transaction_remark'	=> $val['savings_cash_mutation_remark'],
				);
			}

			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup2($sesi['start_date']);

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['tariktunai'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['savings_cash_mutation_date'],
					'transaction_amount'	=> $val['savings_cash_mutation_amount'],
					'transaction_code'		=> $val['mutation_name'],
					'transaction_remark'	=> $val['savings_cash_mutation_remark'],
				);
			}

			$acctsavingsaccount_pickup 		= $this->AcctNominativeSavingsReportPickup_model->getAcctNominativeSavingsReportPickup3($sesi['start_date']);

			foreach ($acctsavingsaccount_pickup as $key => $val) {
				$data['angs'][$key] = array (
					'operated_name'			=> $val['operated_name'],
					'transaction_date'		=> $val['credits_payment_date'],
					'transaction_amount'	=> $val['credits_payment_principal'],
				);
			}


		//print_r($data['savings']);exit;

			
			if(count($acctsavingsaccount_pickup) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Data Pickup")
									 ->setSubject("")
									 ->setDescription("Laporan Data Pickup")
									 ->setKeywords("Laporan Data Pickup")
									 ->setCategory("Laporan Data Pickup");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
						

				
				$this->excel->getActiveSheet()->mergeCells("B1:F1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);
				if($sesi['kelompok_laporan_simpanan'] == 0){
					$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Data Pickup");
				} else {
					$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Data Pickup");
				}
					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"Tanggal");
				$this->excel->getActiveSheet()->setCellValue('C3',"Nama Operator");
				$this->excel->getActiveSheet()->setCellValue('D3',"Transaksi");
				$this->excel->getActiveSheet()->setCellValue('E3',"Jumlah");
				$this->excel->getActiveSheet()->setCellValue('F3',"Keterangan");
				
			
				
				$no=0;
				$totalbasil = 0;
				$totalsaldo = 0;
				$saldo = 0;
				if($sesi['kelompok_laporan_simpanan'] == 0){
					$j=4;
					foreach($data['savings'] as $key=>$val){
						if(is_numeric($key)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['transaction_code']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['transaction_remark']);
							}
								
							$saldo += $val['transaction_amount'];
						}
						$j++;
						$j=5;
						foreach($data['tariktunai'] as $key1=>$val1){
						if(is_numeric($key1)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val1['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val1['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, $val1['transaction_code']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val1['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $val1['transaction_remark']);
							}
								
							$saldo += $val1['transaction_amount'];
						}
						$j++;
						$j=6;
						foreach($data['angs'] as $key2=>$val2){
						if(is_numeric($key2)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val2['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val2['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, "Angsuran");
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val2['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, "Angsuran");
							}
								
							$saldo += $val2['transaction_amount'];
						}
						$j++;

						$j=7;
						foreach($data['mandatory'] as $key3=>$val3){
						if(is_numeric($key3)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val3['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val3['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, $val3['transaction_code']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val3['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $val3['transaction_remark']);
							}
								
							$saldo += $val3['transaction_amount'];
						}

						$j++;
					
					
				} else {
					$i=4;
					
						$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
						$this->excel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
						$this->excel->getActiveSheet()->setCellValue('B'.$i, "SETOR TUNAI");

						$nov= 0;
						$j=$i+1;
							$subtotalbasil = 0;
							$subtotalsaldo = 0;
						foreach($data['savings'] as $key=>$val){
						if(is_numeric($key)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['transaction_code']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['transaction_remark']);
							}
								
							$saldo += $val['transaction_amount'];
						}

						$k=6;
					
						$this->excel->getActiveSheet()->getStyle('B'.$k)->getFont()->setBold(true)->setSize(14);
						$this->excel->getActiveSheet()->getStyle('B'.$k.':F'.$k)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$k.':F'.$k);
						$this->excel->getActiveSheet()->setCellValue('B'.$k, "TARIK TUNAI");

						$nov= 0;
						$j=$k+1;
							$subtotalbasil = 0;
							$subtotalsaldo = 0;
						foreach($data['tariktunai'] as $key1=>$val1){
						if(is_numeric($key)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val1['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val1['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, $val1['transaction_code']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val1['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $val1['transaction_remark']);
							}
								
							$saldo += $val1['transaction_amount'];
						}

						$l=8;
					
						$this->excel->getActiveSheet()->getStyle('B'.$l)->getFont()->setBold(true)->setSize(14);
						$this->excel->getActiveSheet()->getStyle('B'.$l.':F'.$l)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$l.':F'.$l);
						$this->excel->getActiveSheet()->setCellValue('B'.$l, "ANGSURAN");

						$nov= 0;
						$j=$l+1;
							$subtotalbasil = 0;
							$subtotalsaldo = 0;
						foreach($data['angs'] as $key2=>$val2){
						if(is_numeric($key2)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val2['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val2['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, "Angsuran");
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val2['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, "Angsuran");
							}
								
							$saldo += $val2['transaction_amount'];
						}

						$m=10;
					
						$this->excel->getActiveSheet()->getStyle('B'.$m)->getFont()->setBold(true)->setSize(14);
						$this->excel->getActiveSheet()->getStyle('B'.$m.':F'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, "SIMPANAN WAJIB");

						$nov= 0;
						$j=$m+1;
							$subtotalbasil = 0;
							$subtotalsaldo = 0;
						foreach($data['mandatory'] as $key3=>$val3){
						if(is_numeric($key2)){
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							

								$this->excel->getActiveSheet()->setCellValueExplicit('B'.$j, $val3['transaction_date'],PHPExcel_Cell_DataType::TYPE_STRING);								
								$this->excel->getActiveSheet()->setCellValue('C'.$j, $val3['operated_name']);
								$this->excel->getActiveSheet()->setCellValue('D'.$j, $val3['transaction_code']);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val3['transaction_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $val3['transaction_remark']);
							}
								
							$saldo += $val3['transaction_amount'];
						}

						

						$m = 13;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':G'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($subtotalbasil,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($subtotalsaldo,2));

						$i = $m + 1;
					

					$totalsaldo += $saldo;

				}

				if($sesi['kelompok_laporan_simpanan'] == 0){

				$n = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');
				$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totalsaldo,2));

			}else{
				$n = 12;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');
				$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totalsaldo,2));
			}
				
				$filename='Laporan Data Pickup.xls';
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
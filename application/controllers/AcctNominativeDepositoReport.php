<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctNominativeDepositoReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctNominativeDepositoReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->AcctNominativeDepositoReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['kelompoklaporansimpananberjangka']	= $this->configuration->KelompokLaporanSimpananBerjangka();
			$data['main_view']['content']			= 'AcctNominativeDepositoReport/ListAcctNominativeDepositoReport_view';
			$this->load->view('MainPage_view',$data);
		}
		public function viewreport(){
			$sesi = array (
				"start_date" 							=> tgltodb($this->input->post('start_date',true)),
				"branch_id"								=> $this->input->post('branch_id',true),
				"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
				"view"									=> $this->input->post('view',true),
			);
			
			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}
		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			$preferencecompany = $this->AcctNominativeDepositoReport_model->getPreferenceCompany();
			// $sesi = array (
			// 	"start_date" 							=> tgltodb($this->input->post('start_date',true)),
			// 	"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
			// 	"branch_id"								=> $this->input->post('branch_id',true),
			// );

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctdepositoaccount	= $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport($sesi['start_date'], $branch_id);
			$acctdeposito 			= $this->AcctNominativeDepositoReport_model->getAcctDeposito();

			//$depositointerestrate = $acctdepositoaccount['deposito_interest_rate'];

			//print_r($acctdepositoaccount);exit;

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7);

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

			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SIMPANAN BERJANGKA</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])."</div></td>
					    </tr>
					</table>";
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR NOMINATIF SIMPANAN BERJANGKA PER JENIS</div></td>
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
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Rek</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">Bunga</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Nominal</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal Mulai</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			$totalglobal = 0;
			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				foreach ($acctdepositoaccount as $key => $val) {
				$depositointerestrate = $val['deposito_interest_rate']/12;

				//print_r($depositointerestrate);exit;
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
					        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
					        <td width=\"5%\"><div style=\"text-align: left;\">".$depositointerestrate."%</div></td>
					        <td width=\"15%\"><div style=\"text-align: right;\">".$val['deposito_account_amount']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_date'], 2)."</div></td>
					        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_due_date'], 2)."</div></td>
					    </tr>
					";

					$totalglobal += $val['deposito_account_amount'];

					$no++;
				}
			} else {
				foreach ($acctdeposito as $kSavings => $vSavings) {					
					$acctdepositoaccount_deposito = $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport_Deposito($sesi['start_date'], $vSavings['deposito_id'], $branch_id);
					
					if(!empty($acctdepositoaccount_deposito)){
						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"6\" width=\"95%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">".$vSavings['deposito_name']."</div></td>
							</tr>
							<br>
						";
						$nov = 1;
						$totalperjenis = 0;
						foreach ($acctdepositoaccount_deposito as $k => $v) {

							$depositointerestrate1 			= $v['deposito_interest_rate']/12;
							$depositointerestrate_total 	= round($depositointerestrate1,2);

							//print_r($acctdepositoaccount_deposito);exit;
							$tbl3 .= "
								<tr>
							    	<td width=\"5%\"><div style=\"text-align: left;\">".$nov."</div></td>
							        <td width=\"10%\"><div style=\"text-align: left;\">".$v['deposito_account_no']."</div></td>
							        <td width=\"15%\"><div style=\"text-align: left;\">".$v['member_name']."</div></td>
							        <td width=\"20%\"><div style=\"text-align: left;\">".$v['member_address']."</div></td>
							        <td width=\"5%\"><div style=\"text-align: left;\">".$depositointerestrate_total."%</div></td>
							        <td width=\"15%\"><div style=\"text-align: right;\">".$v['deposito_account_amount']."</div></td>
							        <td width=\"10%\"><div style=\"text-align: center;\">".$v['deposito_account_period']."</div></td>
							        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($v['deposito_account_date'], 2)."</div></td>
							        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($v['deposito_account_due_date'], 2)."</div></td>
							    </tr>

							";

							$totalperjenis += $v['deposito_account_amount'];

							$nov++;
						}

						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"3\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".$totalperjenis."</div></td>
							</tr>
							<br>
						";

						$totalglobal += $totalperjenis;
					}

					
				}
			}

			$tbl4 = "
					<tr>
						<td colspan =\"4\"><div style=\"font-size:10;font-style:italic;text-align:left\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctNominativeDepositoReport_model->getUserName($auth['user_id'])."</div></td>
						<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Total</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".$totalglobal."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			if (ob_get_length() > 0){
				ob_clean();
			}
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Nominatif Simpanan Berjangka.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		public function export($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			// $sesi = array (
			// 	"start_date" 							=> tgltodb($this->input->post('start_date',true)),
			// 	"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
			// 	//"branch_id"								=> $this->input->post('branch_id',true),
			// );

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}


			$acctdepositoaccount	= $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport($sesi['start_date'], $branch_id);
			$acctdeposito 			= $this->AcctNominativeDepositoReport_model->getAcctDeposito();

			if(count($acctdepositoaccount) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Nominatif Simpanan")
									 ->setSubject("")
									 ->setDescription("Laporan Nominatif Simpanan")
									 ->setKeywords("Laporan, Nominatif, Simpanan")
									 ->setCategory("Laporan Nominatif Simpanan");
									 
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
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getFont()->setBold(true);
			
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NOMINATIF SIMPANAN BERJANGKA");
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Rek");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Nominal");
				$this->excel->getActiveSheet()->setCellValue('G3',"Jangka Waktu");
				$this->excel->getActiveSheet()->setCellValue('H3',"Tanggal Mulai");
				$this->excel->getActiveSheet()->setCellValue('I3',"Tanggal Jatuh Tempo");
				
				$j=4;
				$no=0;
				$totalbasil = 0;
				$totalsaldo = 0;
				$subtotalnominal = 0;
				if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
					foreach($acctdepositoaccount as $key=>$val){
						if(is_numeric($key)){
							$no++;
							$this->excel->setActiveSheetIndex(0);
							$this->excel->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
							


							$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
							$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['deposito_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
							$this->excel->getActiveSheet()->setCellValue('F'.$j,$val['deposito_account_amount']);
							$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['deposito_account_period']);
							$this->excel->getActiveSheet()->setCellValue('H'.$j, tgltoview($val['deposito_account_date']));
							$this->excel->getActiveSheet()->setCellValue('I'.$j, tgltoview($val['deposito_account_due_date']));
								
						$grandtotal += $val['deposito_account_amount'];
						$subtotalnominal += $val['deposito_account_amount'];				
						//	$totalbasil += $val['savings_profit_sharing_amount'];
						//	$totalsaldo += $val['savings_account_last_balance'];
							
						}else{
							continue;
						}
						$j++;
						//$no++;
					}
				} else {
					$i=4;
					$grandtotal =0;

					foreach ($acctdeposito as $k => $v) {
						$acctdepositoaccount_deposito = $this->AcctNominativeDepositoReport_model->getAcctNomintiveDepositoReport_Deposito($sesi['start_date'], $v['deposito_id'], $branch_id);
					
						if(!empty($acctdepositoaccount_deposito)){
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':I'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':I'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['deposito_name']);

							$nov = 0;
							$j = $i+1;
						
							$subtotalnominal = 0;
							foreach($acctdepositoaccount_deposito as $key=>$val){
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
									$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									
									$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['deposito_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['deposito_account_amount']);
									$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['deposito_account_period']);
									$this->excel->getActiveSheet()->setCellValue('H'.$j, tgltoview($val['deposito_account_date']));
									$this->excel->getActiveSheet()->setCellValue('I'.$j, tgltoview($val['deposito_account_due_date']));
									
								}else{
									continue;
								}
									$j++;
							
								$subtotalnominal += $val['deposito_account_amount'];
								//$subtotalsaldo += $val['savings_account_last_balance'];
							}
						}
							$m = $j;

							// $this->excel->getActiveSheet()->getStyle('B'.$m.':I'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
							// $this->excel->getActiveSheet()->getStyle('B'.$m.':I'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							// $this->excel->getActiveSheet()->mergeCells('B'.$m.':E'.$m);
							// $this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

							// $this->excel->getActiveSheet()->setCellValue('F'.$m, $subtotalnominal);
				
						$i = $m+1;
					}

					$grandtotal += $subtotalnominal;
				//	$totalsaldo += $subtotalsaldo;
				 }

				$n = $j;
				//$grandtotal += $subtotalnominal;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':I'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':I'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F'.$n, $grandtotal);
				//$this->excel->getActiveSheet()->setCellValue('H'.$j, $totalsaldo);
				
				$filename='Laporan Nominatif Simpanan Berjangka.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							 
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				if (ob_get_length() > 0){
				ob_end_clean();
				}
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		 }

	}
?>
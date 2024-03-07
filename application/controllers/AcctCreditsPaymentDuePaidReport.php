<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsPaymentDuePaidReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctCreditsPaymentDuePaidReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsPaymentDuePaidReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']		= 'AcctCreditsPaymentDuePaidReport/ListAcctCreditsPaymentDuePaidReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$sesi = array (
				"end_date" 								=> tgltodb($this->input->post('end_date',true)),
				"branch_id"								=> $this->input->post('branch_id',true),
				"view"									=> $this->input->post('view',true),
			);

			// print_r($sesi); 
			// exit;
			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			$preferencecompany = $this->AcctCreditsPaymentDuePaidReport_model->getPreferenceCompany();
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			
			// $coremember 		= $this->AcctCreditsPaymentDuePaidReport_model->getCoreMemberDetail($sesi['start_date'],$sesi['end_date'], $branch_id);

			$acctcreditsaccount	= $this->AcctCreditsPaymentDuePaidReport_model->getCreditsAccount($sesi['end_date'], $branch_id);
// 			print_r($acctcreditsaccount);exit;
			
			$acctcredits 		= $this->AcctCreditsPaymentDuePaidReport_model->getAcctCredits();

			// $acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($acctcreditsaccount['credits_account_id']);

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'F4', true, 'UTF-8', false);

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

			$pdf->SetFont('helvetica', '', 8);

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
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR NASABAH JATUH TEMPO ANGSUR TANGGAL ".tgltoview($sesi['end_date'])."</div></td>
				    </tr>
				   
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
            
			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">No.</div></td>
			        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">No. Perjanjian</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Nama</div></td>
			        <td width=\"12%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Alamat</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Plafon</div></td>
			        <td width=\"8%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Angs Pokok</div></td>
			        <td width=\"8%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Angs Bunga</div></td>
			        <td width=\"8%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Total Angsuran</div></td>
			        <td width=\"8%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">SLD Pokok (outstanding)</div></td>
			        <td width=\"7%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Jumlah Denda</div></td>
			        <td width=\"7%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Tgl Angsur</div></td>
			         <td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Tenor</div></td>
			        <td width=\"8%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Keterlambatan</div></td>
			       
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalplafon = 0;
			$totalangspokok = 0;
			$totalangsmargin = 0;
			$totalangs = 0;
			$totalsisa = 0;
			$totaldenda = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($acctcreditsaccount as $key => $val) {
				$acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($val['credits_account_id']);	
				// print_r($acctcredits_fine);exit;

				$day 		= date('d-m-Y');
				$start_ 	= new DateTime($day);
				$end_ 		= new DateTime($val['credits_account_payment_date']);
				$status 	= $val['credits_account_status'];

				if($end_ >= $start_){
					$Keterlambatan 	= '0';
					
				}else{
					$interval 		= $start_->diff($end_);
					$Keterlambatan 	= $interval->days;
				}

				$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $acctcredits_fine['credits_fine']) / 100 ) * $Keterlambatan;
				$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;

				$credits_account_payment_to = ($val['credits_account_payment_to'] + 1); 

				//print_r($credits_account_accumulated_fines);exit;
// 			if(($Keterlambatan >= 1)){
					$tbl3 .= "
						<tr>
					    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"8%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"12%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_amount'], 2)."</div></td>
					        <td width=\"8%\"><div style=\"text-align: right;\">".number_format($val['credits_account_principal_amount'], 2)."</div></td>
					       	<td width=\"8%\"><div style=\"text-align: right;\">".number_format($val['credits_account_interest_amount'], 2)."</div></td>
					       	<td width=\"8%\"><div style=\"text-align: right;\">".number_format($val['credits_account_payment_amount'], 2)."</div></td>
					       	<td width=\"8%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance'], 2)."</div></td>
					       	<td width=\"7%\"><div style=\"text-align: right;\">".number_format($credits_account_accumulated_fines, 2)."</div></td>
					        <td width=\"7%\"><div style=\"text-align: right;\">".tgltoview($val['credits_account_payment_date'])."</div></td>
					        <td width=\"5%\"><div style=\"text-align: right;\">".$credits_account_payment_to." / ".$val['credits_account_period']."</div></td>
					        <td width=\"8%\"><div style=\"text-align: center;\">".$Keterlambatan." Hari</div></td>
					     	
					    </tr>
					";
				// }else{
				// 	$tbl3="";
				// }

				$totalplafon	 	+= $val['credits_account_amount'];
				$totalangspokok 	+= $val['credits_account_principal_amount'];
				$totalangsmargin 	+= $val['credits_account_interest_amount'];
				$totalangs 			+= $val['credits_account_payment_amount'];
				$totalsisa 			+= $val['credits_account_last_balance'];
				$totaldenda			+= $val['credits_account_accumulated_fines'];

				$no++;
// 			}
		}

			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"font-size:8;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsPaymentDuePaidReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalplafon, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalangspokok, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalangsmargin, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalangs, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totalsisa, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:8;text-align:right\">".number_format($totaldenda, 2)."</div></td>
				</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR NASABAH JATUH TEMPO ANGSUR.pdf';
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

			$acctcreditsaccount	= $this->AcctCreditsPaymentDuePaidReport_model->getCreditsAccount($sesi['end_date'] ,$branch_id);
			$acctcredits 		= $this->AcctCreditsPaymentDuePaidReport_model->getAcctCredits();

			
			if(count($acctcreditsaccount) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR NASABAH JATUH TEMPO ANGSUR")
									 ->setSubject("")
									 ->setDescription("DAFTAR NASABAH JATUH TEMPO ANGSUR")
									 ->setKeywords("DAFTAR, NASABAH, JATUH, TEMPO, ANGSUR")
									 ->setCategory("DAFTAR NASABAH JATUH TEMPO ANGSUR");
									 
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
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(20);		

				
				$this->excel->getActiveSheet()->mergeCells("B1:M1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:M3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:M3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR NASABAH JATUH TEMPO ANGSUR TANGGAL ".tgltoview($sesi['end_date']));					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Perjanjian");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Plafon");
				$this->excel->getActiveSheet()->setCellValue('G3',"Angs Pokok");
				$this->excel->getActiveSheet()->setCellValue('H3',"Angs Bunga");
				$this->excel->getActiveSheet()->setCellValue('I3',"Total Angsuran");
				$this->excel->getActiveSheet()->setCellValue('J3',"Saldo Pokok (Outstanding)");
				$this->excel->getActiveSheet()->setCellValue('K3',"Jumlah Denda");
				$this->excel->getActiveSheet()->setCellValue('L3',"Tanggal Angsuran");
				$this->excel->getActiveSheet()->setCellValue('M3',"Keterlambatan");
				
				
				$no=0;
				$totalplafon = 0;
				$totalangspokok = 0;
				$totalangsmargin = 0;
				$totalangs = 0;
				$totalsisa = 0;
				$totaldenda = 0;
				$j=4;

				foreach($acctcreditsaccount as $key=>$val){
					$acctcredits_fine	= $this->AcctCreditAccount_model->getDetailByID($val['credits_account_id']);

					$day 		= date('d-m-Y');
					$start_ 	= new DateTime($day);
					$end_ 		= new DateTime($val['credits_account_payment_date']);

					if($end_ >= $start_){
						$Keterlambatan 	= '0';
						// $interval 	= $start_->diff($end_);
					}else{
						$interval 		= $start_->diff($end_);
						$Keterlambatan 	= $interval->days;
					}

					$credits_payment_fine_amount 		= (($val['credits_account_payment_amount'] * $acctcredits_fine['credits_fine']) / 100 ) * $Keterlambatan;
					$credits_account_accumulated_fines 	= $val['credits_account_accumulated_fines'] + $credits_payment_fine_amount;

					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':M'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);						


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_principal_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_interest_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_account_payment_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_account_last_balance'],2));
						$this->excel->getActiveSheet()->setCellValue('K'.$j, number_format($credits_account_accumulated_fines,2));
						$this->excel->getActiveSheet()->setCellValue('L'.$j, tgltoview($val['credits_account_payment_date']));
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $Keterlambatan.' Hari');
			
						$totalplafon += $val['credits_account_amount'];
						$totalangspokok += $val['credits_account_principal_amount'];
						$totalangsmargin += $val['credits_account_interest_amount'];
						$totalangs += $val['credits_account_payment_amount'];
						$totalsisa += $val['credits_account_last_balance'];
						$totaldenda += $val['credits_account_accumulated_fines'];
						
					}else{
						continue;
					}
					$j++;
				}

				$i = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$i.':M'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$i.':M'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F'.$i, number_format($totalplafon,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($totalangspokok,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$i, number_format($totalangsmargin,2));
				$this->excel->getActiveSheet()->setCellValue('I'.$i, number_format($totalangs,2));
				$this->excel->getActiveSheet()->setCellValue('J'.$i, number_format($totalsisa,2));
				$this->excel->getActiveSheet()->setCellValue('K'.$i, number_format($totaldenda,2));
				
				$filename='DAFTAR NASABAH JATUH TEMPO ANGSUR.xls';
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
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsPaymentDailyReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsPaymentDailyReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsPaymentDailyReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['coreoffice']	= create_double($this->AcctCreditsPaymentDailyReport_model->getCoreOffice(),'office_id','office_name');
			$data['main_view']['content']		= 'AcctCreditsPaymentDailyReport/ListAcctCreditsPaymentDailyReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 							=> tgltodb($this->input->post('start_date',true)),
				"office_id"								=> $this->input->post('office_id',true),
				"branch_id"								=> $this->input->post('branch_id',true),
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
			$preferencecompany = $this->AcctCreditsPaymentDailyReport_model->getPreferenceCompany();
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctcreditspayment	= $this->AcctCreditsPaymentDailyReport_model->getCreditsPayment($sesi['start_date'], $branch_id);
			$acctcredits 		= $this->AcctCreditsPaymentDailyReport_model->getAcctCredits();

			// print_r($acctcreditspayment);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 
			
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
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR ANGSURAN PINJAMAN</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">".tgltoview($sesi['start_date'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">No.</div></td>
			        <td width=\"7%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">No. Kredit</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Nama</div></td>
					<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Sisa Pokok Awal</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Angs Pokok</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Angs Bunga</div></td>
			         <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Total Angs</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Denda</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Sisa Pokok Akhir</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Angsuran ke</div></td>
			    </tr>				
			</table>";

			$no 				= 1;
			$totaldenda 		= 0;
			$totalpokokakhir 	= 0;
			$totalangspokok 	= 0;
			$totalangsmargin 	= 0;
			$totaltotal 		= 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			if(!empty($acctcreditspayment)){
				foreach ($acctcreditspayment as $key => $val) {
					$tbl3 .= "
						<tr>
					    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"7%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".number_format($val['credits_principal_opening_balance'], 2)."</div></td>
					        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_payment_principal'], 2)."</div></td>
					       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_payment_interest'], 2)."</div></td>
					       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_payment_amount'], 2)."</div></td>
					       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_payment_fine'], 2)."</div></td>
					       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_principal_last_balance'], 2)."</div></td>
					       	<td width=\"10%\"><div style=\"text-align: right;\">".$val['credits_payment_to']."</div></td>
					    </tr>
					";

					$totaldenda 		+= $val['credits_payment_fine'];
					$totalangspokok 	+= $val['credits_payment_principal'];
					$totalangsmargin 	+= $val['credits_payment_interest'];
					$totalpokokakhir 	+= $val['credits_principal_last_balance'];
					$totaltotal			+= $val['credits_payment_amount'];

					$no++;
				}
			} else {
				$tbl3 .= "";
			}
			

			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsPaymentDailyReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"font-weight:bold;border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align:right\">".number_format($totalangspokok, 2)."</div></td>
					<td style=\"font-weight:bold;border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align:right\">".number_format($totalangsmargin, 2)."</div></td>
					<td style=\"font-weight:bold;border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align:right\">".number_format($totaltotal, 2)."</div></td>
					<td style=\"font-weight:bold;border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align:right\">".number_format($totaldenda, 2)."</div></td>
					<td style=\"font-weight:bold;border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align:right\">".number_format($totalpokokakhir, 2)."</div></td>
					<td style=\"font-weight:bold;border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align:right\"></div></td>
					
				</tr>
							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR TAGIHAN ANGSURAN PINJAMAN.pdf';
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
			$acctcreditspayment	= $this->AcctCreditsPaymentDailyReport_model->getCreditsPayment($sesi['start_date'], $branch_id);
			//$acctcredits 		= $this->AcctCreditsPaymentDailyReport_model->getAcctCredits();

			
			if(count($acctcreditspayment) !=''){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR ANGSURAN PINJAMAN HARIAN")
									 ->setSubject("")
									 ->setDescription("DAFTAR ANGSURAN PINJAMAN HARIAN")
									 ->setKeywords("DAFTAR, ANGSURAN, PINJAMAN, HARIAN")
									 ->setCategory("DAFTAR ANGSURAN PINJAMAN HARIAN");
									 
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

				
				$this->excel->getActiveSheet()->mergeCells("B1:K1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:K3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:K3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:K3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR ANGSURAN PINJAMAN ".$sesi['start_date']);

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Sisa Pokok Awal");
				$this->excel->getActiveSheet()->setCellValue('F3',"Angsuran Pokok");
				$this->excel->getActiveSheet()->setCellValue('G3',"Angsuran Bunga");
				$this->excel->getActiveSheet()->setCellValue('H3',"Total Angsuran");
				$this->excel->getActiveSheet()->setCellValue('I3',"Denda");
				$this->excel->getActiveSheet()->setCellValue('J3',"Sisa Pokok Akhir");
				$this->excel->getActiveSheet()->setCellValue('K3',"Angsuran ke");
				
				
				$no 				= 0;
				$totaldenda 		= 0;
				$totalpokokakhir 	= 0;
				$totalangspokok 	= 0;
				$totalangsmargin 	= 0;
				$totaltotal 		= 0;
				$j=4;
				foreach($acctcreditspayment as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, number_format($val['credits_principal_opening_balance'],2));
						$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_payment_principal'],2));
						$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_payment_interest'],2));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_payment_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_payment_fine'],2));
						$this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_principal_last_balance'],2));
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_payment_to']);
			
						$totaldenda 		+= $val['credits_payment_fine'];
						$totalangspokok 	+= $val['credits_payment_principal'];
						$totalangsmargin 	+= $val['credits_payment_interest'];
						$totalpokokakhir 	+= $val['credits_principal_last_balance'];
						$totaltotal			+= $val['credits_payment_amount'];
						
					}else{
						continue;
					}
					$j++;
				}

				$i = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('F'.$j.':J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$this->excel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F'.$i, number_format($totalangspokok,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($totalangsmargin,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$i, number_format($totaltotal,2));
				$this->excel->getActiveSheet()->setCellValue('I'.$i, number_format($totaldenda,2));
				$this->excel->getActiveSheet()->setCellValue('J'.$i, number_format($totalpokokakhir,2));
				
				$filename='DAFTAR ANGSURAN PINJAMAN'.$sesi['start_date'].'.xls';
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
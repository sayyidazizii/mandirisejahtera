<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsPaymentDueReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsPaymentDueReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsPaymentDueReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']		= 'AcctCreditsPaymentDueReport/ListAcctCreditsPaymentDueReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 							=> tgltodb($this->input->post('start_date',true)),
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
			$preferencecompany	= $this->AcctCreditsPaymentDueReport_model->getPreferenceCompany();
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			$acctcreditsaccount	= $this->AcctCreditsPaymentDueReport_model->getCreditsAccount($sesi['start_date'], $branch_id);
			$acctcredits 		= $this->AcctCreditsPaymentDueReport_model->getAcctCredits();

			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

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
				<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				    <tr>
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR PINJAMAN JATUH TEMPO TGL ".tgltoview($sesi['start_date'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Akad</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Plafon</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Angs Pokok</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Angs Margin</div></td>
			        <td width=\"10%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">SLD Pokok (outstanding)</div></td>
			        <td width=\"5%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tenor</div></td>
			         <td width=\"8%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tgl Terakhir Angsur</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalplafon = 0;
			$totalangspokok = 0;
			$totalangsmargin = 0;
			$totalsisa = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($acctcreditsaccount as $key => $val) {

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
				        <td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['credits_account_amount'], 2)."</div></td>
				        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_principal_amount'], 2)."</div></td>
				       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_interest_amount'], 2)."</div></td>
				       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance'], 2)."</div></td>
				       	<td width=\"5%\"><div style=\"text-align: right;\">".$val['credits_account_payment_to']." / ".$val['credits_account_period']."</div></td>
				         <td width=\"8%\"><div style=\"text-align: right;\">".tgltoview($val['credits_account_last_payment_date'])."</div></td>
				    </tr>
				";

				$totalplafon += $val['credits_account_amount'];
				$totalangspokok += $val['credits_account_principal_amount'];
				$totalangsmargin += $val['credits_account_interest_amount'];
				$totalsisa += $val['credits_account_last_balance'];

				$no++;
			}

			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsPaymentDueReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalplafon, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangspokok, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangsmargin, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsisa, 2)."</div></td>
				</tr>
							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR PINJAMAN JATUH TEMPO.pdf';
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

			$acctcreditsaccount	= $this->AcctCreditsPaymentDueReport_model->getCreditsAccount($sesi['start_date'], $branch_id);
			$acctcredits 		= $this->AcctCreditsPaymentDueReport_model->getAcctCredits();

			
			if(count($acctcreditsaccount) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR PINJAMAN JATUH TEMPO")
									 ->setSubject("")
									 ->setDescription("DAFTAR PINJAMAN JATUH TEMPO")
									 ->setKeywords("DAFTAR, PINJAMAN, JATUH TEMPO")
									 ->setCategory("DAFTAR PINJAMAN JATUH TEMPO");
									 
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
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR PINJAMAN JATUH TEMPO S.D ".tgltoview($sesi['start_date']));

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Plafon");
				$this->excel->getActiveSheet()->setCellValue('G3',"Angs Pokok");
				$this->excel->getActiveSheet()->setCellValue('H3',"Angs Bunga");
				$this->excel->getActiveSheet()->setCellValue('I3',"Total Angsuran");
				$this->excel->getActiveSheet()->setCellValue('J3',"Saldo Pokok (Outstanding)");
				$this->excel->getActiveSheet()->setCellValue('K3',"Tanggal Terakhir Angsuran");
				
				
				$no=0;
				$totalplafon = 0;
				$totalangspokok = 0;
				$totalangsmargin = 0;
				$totalangs = 0;
				$totalsisa = 0;
				$j=4;
				foreach($acctcreditsaccount as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_principal_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_interest_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_account_payment_amount'],2));
						$this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_account_last_balance'],2));
						$this->excel->getActiveSheet()->setCellValue('K'.$j, tgltoview($val['credits_account_last_payment_date']));
			
						$totalplafon += $val['credits_account_amount'];
						$totalangspokok += $val['credits_account_principal_amount'];
						$totalangsmargin += $val['credits_account_interest_amount'];
						$totalangs += $val['credits_account_payment_amount'];
						$totalsisa += $val['credits_account_last_balance'];
						
					}else{
						continue;
					}
					$j++;
				}

				$i = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
				$this->excel->getActiveSheet()->setCellValue('B'.$i, 'Total');

				$this->excel->getActiveSheet()->setCellValue('F'.$i, number_format($totalplafon,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($totalangspokok,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$i, number_format($totalangsmargin,2));
				$this->excel->getActiveSheet()->setCellValue('I'.$i, number_format($totalangs,2));
				$this->excel->getActiveSheet()->setCellValue('J'.$i, number_format($totalsisa,2));
				
				$filename='DAFTAR PINJAMAN JATUH TEMPO.xls';
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
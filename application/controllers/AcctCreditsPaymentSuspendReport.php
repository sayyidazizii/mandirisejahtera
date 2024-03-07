<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsPaymentSuspendReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsPaymentSuspendReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']	= create_double($this->AcctCreditsPaymentSuspendReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctcredits']	= create_double($this->AcctCreditsPaymentSuspendReport_model->getAcctCredits(),'credits_id', 'credits_name');
			//$data['main_view']['coreoffice']	= create_double($this->AcctCreditsPaymentSuspendReport_model->getCoreOffice(),'office_id','office_name');
			$data['main_view']['content']		= 'AcctCreditsPaymentSuspendReport/ListAcctCreditsPaymentSuspendReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 							=> tgltodb($this->input->post('start_date',true)),
				"end_date" 								=> tgltodb($this->input->post('end_date',true)),
				"credits_id"								=> $this->input->post('credits_id',true),
				"branch_id"								=> $this->input->post('branch_id',true),
				"view"									=> $this->input->post('view',true),
			);

			//print_r($sesi);exit;
			
			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
			}
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctcreditspaymentsuspend	= $this->AcctCreditsPaymentSuspendReport_model->AcctCreditsPaymentSuspend($sesi['start_date'], $sesi['end_date'], $sesi['credits_id'], $branch_id);
			$preferencecompany	= $this->AcctCreditsPaymentSuspendReport_model->getPreferenceCompany();

			$creditspaymentperiod = $this->configuration->CreditsPaymentPeriod();

			//print_r($acctcreditspaymentsuspend);exit;


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
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR TAGIHAN PENUNDAAN ANGSURAN</div></td>
				    </tr>
				    <tr>
				        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
				    </tr>
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"3%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">No.</div></td>
			        <td width=\"17%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">No. Perjanjian Kredit</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Nama Anggota</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Jenis Pinjaman</div></td>
			         <td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Angsuran</div></td>
					<td width=\"10%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Penundaan Angsuran</div></td>
					<td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Tanggal Angsuran Lama</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:9;\">Tanggal Angsuran Baru</div></td>
			       
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalplafon = 0;
			$totalangspokok = 0;
			$totalangsmargin = 0;
			$totaltotal = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			if(!empty($acctcreditspaymentsuspend)){
				foreach ($acctcreditspaymentsuspend as $key => $val) {
					$tbl3 .= "
						<tr>
					    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"17%\"><div style=\"text-align: center;\">".$val['credits_account_serial']."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: center;\">".$val['credits_name']."</div></td>
					      	<td width=\"10%\"><div style=\"text-align: center;\">".$creditspaymentperiod[$val['credits_payment_period']]."</div></td>
					      	<td width=\"10%\"><div style=\"text-align: center;\">".$val['credits_grace_period']."</div></td>
					        <td width=\"15%\"><div style=\"text-align: center;\">".tgltoview($val['credits_payment_date_old'])."</div></td>
					        <td width=\"15%\"><div style=\"text-align: center;\">".tgltoview($val['credits_payment_date_new'])."</div></td>
					    </tr>
					";


					$no++;
				}
			} else {
				$tbl3 .= "";
			}
			

			$tbl4 = "
				<tr>
					<td colspan =\"4\"><div style=\"font-size:9;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsPaymentSuspendReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalplafon, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalsisa, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangspokok, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totalangsmargin, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;text-align:right\">".number_format($totaltotal, 2)."</div></td>
					
				</tr>
							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR PENUNDAAN ANGSURAN.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function export($sesi){	
			$auth = $this->session->userdata('auth');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
			}
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctcreditspaymentsuspend	= $this->AcctCreditsPaymentSuspendReport_model->AcctCreditsPaymentSuspend($sesi['start_date'], $sesi['end_date'], $sesi['credits_id'], $branch_id);

			$creditspaymentperiod = $this->configuration->CreditsPaymentPeriod();

			
			if(count($acctcreditspaymentsuspend) !=''){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR PENUNDAAN ANGSURAN")
									 ->setSubject("")
									 ->setDescription("DAFTAR PENUNDAAN ANGSURAN")
									 ->setKeywords("DAFTAR, TAGIHAN, ANGSURAN, PINJAMAN")
									 ->setCategory("DAFTAR PENUNDAAN ANGSURAN");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(30);	
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(30);	
						

				
				$this->excel->getActiveSheet()->mergeCells("B1:I1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:I3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR PENUNDAAN ANGSURAN");

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Perjanjian Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Jenis Pinjamna");
				$this->excel->getActiveSheet()->setCellValue('F3',"Angsuran");
				$this->excel->getActiveSheet()->setCellValue('G3',"Penundaan Angsuran");
				$this->excel->getActiveSheet()->setCellValue('H3',"Tanggal Angsuran Lama");
				$this->excel->getActiveSheet()->setCellValue('I3',"Tanggal Angsuran Baru");
				
				
				
				$no=0;
				$j=4;
				foreach($acctcreditspaymentsuspend as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						
						


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['credits_name']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $creditspaymentperiod[$val['credits_payment_period']]);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['credits_grace_period']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, tgltoview($val['credits_payment_date_old']));
						$this->excel->getActiveSheet()->setCellValue('I'.$j, tgltoview($val['credits_payment_date_new']));
			
						
						
					}else{
						continue;
					}
					$j++;
				}

				$i = $j;

				
				$filename='DAFTAR PENUNDAAN ANGSURAN.xls';
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
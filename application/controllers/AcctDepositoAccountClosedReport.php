<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

	Class AcctDepositoAccountClosedReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctDepositoAccountClosedReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$data['main_view']['corebranch']						= create_double($this->AcctDepositoAccountClosedReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['kelompoklaporansimpananberjangka']	= $this->configuration->KelompokLaporanSimpananBerjangka();
			$data['main_view']['content']				= 'AcctDepositoAccountClosedReport/FormFilterAcctDepositoAccountClosedReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 							=> tgltodb($this->input->post('start_date',true)),
				"end_date" 								=> tgltodb($this->input->post('end_date',true)),
				"kelompok_laporan_simpanan_berjangka"	=> $this->input->post('kelompok_laporan_simpanan_berjangka',true),
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
			$kelompoklaporansimpanan	= $this->configuration->KelompokLaporanSimpanan();	
			$acctdeposito 				= $this->AcctDepositoAccountClosedReport_model->getAcctDeposito();
			$preferencecompany			= $this->AcctDepositoAccountClosedReport_model->getPreferenceCompany();
			$period 					= date('mY', strtotime($sesi['start_date']));

			$namaKelompoklaporansimpanan = $kelompoklaporansimpanan[$sesi['kelompok_laporan_simpanan_berjangka']];

			

			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				$acctdepositoaccount	= $this->AcctDepositoAccountClosedReport_model->getAcctDepositoAccountClosed($sesi['start_date'], $sesi['end_date'], $branch_id);
			

				foreach ($acctdepositoaccount as $key => $val) {
					$data_acctdepositoaccount[] = array (
						'deposito_account_no'			=> $val['deposito_account_no'],
						'member_name'					=> $val['member_name'],
						'office_id'						=> $val['office_id'],
						'member_address'				=> $val['member_address'],
						'deposito_account_amount'		=> $val['deposito_account_amount'],
						'deposito_account_period'		=> $val['deposito_account_period'],
						'deposito_account_date'			=> $val['deposito_account_date'],
						'deposito_account_due_date'		=> $val['deposito_account_due_date'],
						'deposito_account_closed_date'	=> $val['deposito_account_closed_date'],
					);
				}
			} else {

				foreach ($acctdeposito as $key => $vD) {
					$acctdepositoaccount_deposito = $this->AcctDepositoAccountClosedReport_model->getAcctDepositoAccountClosed_Deposito($sesi['start_date'], $sesi['end_date'], $vD['deposito_id'], $branch_id);

					foreach ($acctdepositoaccount_deposito as $key => $val) {
						$data_acctdepositoaccount[$vD['deposito_id']][] = array (
							'deposito_account_no'			=> $val['deposito_account_no'],
							'member_name'					=> $val['member_name'],
							'office_id'						=> $val['office_id'],
							'member_address'				=> $val['member_address'],
							'deposito_account_amount'		=> $val['deposito_account_amount'],
							'deposito_account_period'		=> $val['deposito_account_period'],
							'deposito_account_date'			=> $val['deposito_account_date'],
							'deposito_account_due_date'		=> $val['deposito_account_due_date'],
							'deposito_account_closed_date'	=> $val['deposito_account_closed_date'],
						);
					}
				}
			}


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
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR SIMPANAN BERJANGKA DITUTUP </div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					    </tr>
					</table>";
			} else {
				$tbl = "
					<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
					    <tr>
					        <td><div style=\"text-align: center; font-size:14px;font-weight:bold\">DAFTAR SIMPANAN BERJANGKA DITUTUP PER JENIS</div></td>
					    </tr>
					    <tr>
					        <td><div style=\"text-align: center; font-size:10px\">Periode ".tgltoview($sesi['start_date'])." S.D. ".tgltoview($sesi['end_date'])."</div></td>
					    </tr>
					</table>";
			}
			

			$pdf->writeHTML($tbl0.$tbl, true, false, false, false, '');
			

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">No.</div></td>
			        <td width=\"9%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">No. Rek</div></td>
			        <td width=\"13%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">AO</div></td>
			        <td width=\"18%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"15%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">Nominal</div></td>
			        <td width=\"9%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"9%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal Mulai</div></td>
			        <td width=\"9%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			        <td width=\"9%\"style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">TGL Tutup</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			$totalglobal = 0;
			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				foreach ($data_acctdepositoaccount as $key => $val) {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"9%\"><div style=\"text-align: left;\">".$val['deposito_account_no']."</div></td>
					        <td width=\"13%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"5%\"><div style=\"text-align: left;\">".$this->AcctDepositoAccountClosedReport_model->getOfficeCode($val['office_id'])."</div></td>
					        <td width=\"18%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
					        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['deposito_account_amount'], 2)."</div></td>
					        <td width=\"9%\"><div style=\"text-align: center;\">".$val['deposito_account_period']."</div></td>
					        <td width=\"9%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_date'], 2)."</div></td>
					        <td width=\"9%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_due_date'], 2)."</div></td>
					        <td width=\"9%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_closed_date'], 2)."</div></td>
					    </tr>
					";

					$totalglobal += $val['deposito_account_amount'];

					$no++;
				}
			} else {
				foreach ($acctdeposito as $kD => $vD) {
					
					
					if(!empty($data_acctdepositoaccount[$vD['deposito_id']])){
						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"6\" width=\"95%\" style=\"border-bottom: 1px solid black;font-weight:bold\"><div style=\"font-size:10\">".$vD['deposito_name']."</div></td>
							</tr>
							<br>
						";
						$nov = 1;
						$totalperjenis = 0;
						foreach ($data_acctdepositoaccount[$vD['deposito_id']] as $k => $v) {
							$tbl3 .= "
								<tr>
							    	<td width=\"5%\"><div style=\"text-align: left;\">".$nov."</div></td>
							        <td width=\"9%\"><div style=\"text-align: left;\">".$v['deposito_account_no']."</div></td>
							        <td width=\"13%\"><div style=\"text-align: left;\">".$v['member_name']."</div></td>
							        <td width=\"5%\"><div style=\"text-align: left;\">".$this->AcctDepositoAccountClosedReport_model->getOfficeCode($val['office_id'])."</div></td>
							        <td width=\"18%\"><div style=\"text-align: left;\">".$v['member_address']."</div></td>
							        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($v['deposito_account_amount'], 2)."</div></td>
							        <td width=\"9%\"><div style=\"text-align: center;\">".$v['deposito_account_period']."</div></td>
							        <td width=\"9%\"><div style=\"text-align: center;\">".tgltoview($v['deposito_account_date'], 2)."</div></td>
							        <td width=\"9%\"><div style=\"text-align: center;\">".tgltoview($v['deposito_account_due_date'], 2)."</div></td>
							        <td width=\"9%\"><div style=\"text-align: center;\">".tgltoview($val['deposito_account_closed_date'], 2)."</div></td>
							    </tr>

							";

							$totalperjenis += $v['deposito_account_amount'];

							$nov++;
						}

						$tbl3 .= "
							<br>
							<tr>
								<td colspan =\"4\"><div style=\"font-size:10;font-style:italic;text-align:right\"></div></td>
								<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Subtotal</div></td>
								<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalperjenis, 2)."</div></td>
							</tr>
							<br>
						";

						$totalglobal += $totalperjenis;
					}

					
				}
			}

			$tbl4 = "
					<tr>
						<td colspan =\"4\"><div style=\"font-size:10;font-style:italic;text-align:left\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctDepositoAccountClosedReport_model->getUserName($auth['user_id'])."</div></td>
						<td><div style=\"font-size:10;font-weight:bold;text-align:center\">Total</div></td>
						<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($totalglobal, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR SIMPANAN BERJANGKA DITUTUP.pdf';
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
			$kelompoklaporansimpanan	= $this->configuration->KelompokLaporanSimpanan();	
			$acctdeposito 				= $this->AcctDepositoAccountClosedReport_model->getAcctDeposito();
			$period 					= date('mY', strtotime($sesi['start_date']));

			$namaKelompoklaporansimpanan = $kelompoklaporansimpanan[$sesi['kelompok_laporan_simpanan_berjangka']];

			

			if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
				$acctdepositoaccount	= $this->AcctDepositoAccountClosedReport_model->getAcctDepositoAccountClosed($sesi['start_date'], $sesi['end_date'], $branch_id);

				foreach ($acctdepositoaccount as $key => $val) {
					$data_acctdepositoaccount[] = array (
						'deposito_account_no'			=> $val['deposito_account_no'],
						'member_name'					=> $val['member_name'],
						'office_id'						=> $val['office_id'],
						'member_address'				=> $val['member_address'],
						'deposito_account_amount'		=> $val['deposito_account_amount'],
						'deposito_account_period'		=> $val['deposito_account_period'],
						'deposito_account_date'			=> $val['deposito_account_date'],
						'deposito_account_due_date'		=> $val['deposito_account_due_date'],
						'deposito_account_closed_date'	=> $val['deposito_account_closed_date'],
					);
				}
			} else {

				foreach ($acctdeposito as $key => $vD) {
					$acctdepositoaccount_deposito = $this->AcctDepositoAccountClosedReport_model->getAcctDepositoAccountClosed_Deposito($sesi['start_date'], $sesi['end_date'], $vD['deposito_id'], $branch_id);

					foreach ($acctdepositoaccount_deposito as $key => $val) {
						$data_acctdepositoaccount[$vD['deposito_id']][] = array (
							'deposito_account_no'			=> $val['deposito_account_no'],
							'member_name'					=> $val['member_name'],
							'office_id'						=> $val['office_id'],
							'member_address'				=> $val['member_address'],
							'deposito_account_amount'		=> $val['deposito_account_amount'],
							'deposito_account_period'		=> $val['deposito_account_period'],
							'deposito_account_date'			=> $val['deposito_account_date'],
							'deposito_account_due_date'		=> $val['deposito_account_due_date'],
							'deposito_account_closed_date'	=> $val['deposito_account_closed_date'],
						);
					}
				}
			}

			
			if(count($data_acctdepositoaccount) != ''){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR SIMPANAN BERJANGKA DITUTUP")
									 ->setSubject("")
									 ->setDescription("DAFTAR SIMPANAN BERJANGKA DITUTUP")
									 ->setKeywords("DAFTAR, SIMPANAN BERJANGKA, DITUTUP")
									 ->setCategory("DAFTAR SIMPANAN BERJANGKA DITUTUP");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
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
				if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR SIMPANAN BERJANGKA DITUTUP");
				} else {
					$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR SIMPANAN BERJANGKA DITUTUP PER JENIS");
				}
					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Rek");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"AO");
				$this->excel->getActiveSheet()->setCellValue('F3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('G3',"Nominal");
				$this->excel->getActiveSheet()->setCellValue('H3',"JK Waktu");
				$this->excel->getActiveSheet()->setCellValue('I3',"Tanggal Mulai");
				$this->excel->getActiveSheet()->setCellValue('J3',"JT Tempo");
				$this->excel->getActiveSheet()->setCellValue('K3',"Tanggal Tutup");
				
				
				$no=0;
				$totalglobal = 0;
				if($sesi['kelompok_laporan_simpanan_berjangka'] == 0){
					$j=4;
					foreach($data_acctdepositoaccount as $key=>$val){
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
							$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							


							$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
							$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['deposito_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, $this->AcctDepositoAccountClosedReport_model->getOfficeCode($val['office_id']));
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_address']);
							$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['deposito_account_amount'],2));
							$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['deposito_account_period']);
							$this->excel->getActiveSheet()->setCellValue('I'.$j, tgltoview($val['deposito_account_date']));
							$this->excel->getActiveSheet()->setCellValue('J'.$j, tgltoview($val['deposito_account_due_date']));
							$this->excel->getActiveSheet()->setCellValue('K'.$j, tgltoview($val['deposito_account_closed_date']));
				
							$totalglobal += $val['deposito_account_amount'];
							
						}else{
							continue;
						}
						$j++;
					}

					$i = $j;
				} else {
					$i=4;
					
					$totalperjenis = 0;
					foreach ($acctdeposito as $k => $v) {
						if(!empty($data_acctdepositoaccount[$v['deposito_id']])){
							$this->excel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true)->setSize(14);
							$this->excel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$i.':K'.$i);
							$this->excel->getActiveSheet()->setCellValue('B'.$i, $v['deposito_name']);

							$nov= 0;
							$j=$i+1;
							foreach($data_acctdepositoaccount[$v['deposito_id']] as $key=>$val){
								if(is_numeric($key)){
									$nov++;
									$this->excel->setActiveSheetIndex(0);
									$this->excel->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
									$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
									$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
									$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
									


									$this->excel->getActiveSheet()->setCellValue('B'.$j, $nov);
									$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['deposito_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
									$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
									$this->excel->getActiveSheet()->setCellValue('E'.$j, $this->AcctDepositoAccountClosedReport_model->getOfficeCode($val['office_id']));
									$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_address']);
									$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['deposito_account_amount'],2));
									$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['deposito_account_period']);
									$this->excel->getActiveSheet()->setCellValue('I'.$j, tgltoview($val['deposito_account_date']));
									$this->excel->getActiveSheet()->setCellValue('J'.$j, tgltoview($val['deposito_account_due_date']));
									$this->excel->getActiveSheet()->setCellValue('K'.$j, tgltoview($val['deposito_account_closed_date']));
						
									$totalperjenis += $val['deposito_account_amount'];
						
									
								}else{
									continue;
								}
								$j++;
							}

							$m = $j;

							$this->excel->getActiveSheet()->getStyle('B'.$m.':K'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
							$this->excel->getActiveSheet()->getStyle('B'.$m.':K'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
							$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
							$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

							$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($totalperjenis,2));

							$i = $m + 1;
						}
					}

					$totalglobal += $totalperjenis;

				}

				$n = $i;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':K'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':K'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':F'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');

				$this->excel->getActiveSheet()->setCellValue('G'.$n, number_format($totalglobal,2));
				
				$filename='DAFTAR SIMPANAN BERJANGKA DITUTUP.xls';
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
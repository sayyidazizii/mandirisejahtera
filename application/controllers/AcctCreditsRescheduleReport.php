<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsRescheduleReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsRescheduleReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 						= create_double_branch($this->AcctCreditsRescheduleReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content']		= 'AcctCreditsRescheduleReport/ListAcctCreditsRescheduleReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id"		=> $this->input->post('branch_id',true),
				"view"			=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 	=	$this->session->userdata('auth'); 
			$preferencecompany	= $this->AcctCreditsRescheduleReport_model->getPreferenceCompany();

			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}

			$acctcreditsaccountreschedule	= $this->AcctCreditsRescheduleReport_model->getCreditsAccountReschedule($sesi['start_date'], $sesi['end_date'], $branch_id);

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
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR RESCHEDULLING PINJAMAN</div></td>
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
			        <td width=\"3%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\" >No.</div></td>
			        <td width=\"10%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\" >No. Kredit</div></td>
			        <td width=\"12%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Nama</div></td>
			        <td width=\"15%\" rowspan=\"2\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Alamat</div></td>
			        <td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;\"><div style=\"text-align: center;font-size:10;\" colspan=\"4\">PINJAMAN Lama</div></td>
			        <td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\" colspan=\"4\">PINJAMAN Baru</div></td>
			    </tr>
			    <tr>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-left: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Pokok</div></td>
			      <td width=\"10%\" style=\"border-bottom: 1px solid black;border-left: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Bunga</div></td>
			        <td width=\"4%\"style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"6%\"style=\"border-bottom: 1px solid black;border-right: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Pokok</div></td>
			        <td width=\"10%\" style=\"border-bottom: 1px solid black;border-left: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">Bunga</div></td>
			        <td width=\"4%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JK Waktu</div></td>
			        <td width=\"6%\" style=\"border-bottom: 1px solid black;\"><div style=\"text-align: center;font-size:10;\">JT Tempo</div></td>
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalpokokold = 0;
			$totalinterestold = 0;
			$totalpokoknew = 0;
			$totalinterestnew = 0;
			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";

			if(!empty($acctcreditsaccountreschedule)){
				foreach ($acctcreditsaccountreschedule as $key => $val) {
					// print_r($acctcreditspayment);exit;

					$tbl3 .= "
						<tr>
					    	<td width=\"3%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"10%\"><div style=\"text-align: left;\">".$val['credits_account_serial']."</div></td>
					        <td width=\"12%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
					        <td width=\"15%\"><div style=\"text-align: left;\">".$val['member_address']."</div></td>
					        <td width=\"10%\" style=\"border-left: 1px solid black;\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_old'], 2)."</div></td>
					        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_interest_old'], 2)."</div></td>
					       	<td width=\"4%\"><div style=\"text-align: center;\">".$val['credits_account_period_old']."</div></td>
					       	<td width=\"6%\" style=\"border-right: 1px solid black;\"><div style=\"text-align: center;\">".tgltoview($val['credits_account_due_date_old'])."</div></td>
					       	<td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_last_balance_new'], 2)."</div></td>
					        <td width=\"10%\"><div style=\"text-align: right;\">".number_format($val['credits_account_interest_new'], 2)."</div></td>
					       	<td width=\"4%\"><div style=\"text-align: center;\">".$val['credits_account_period_new']."</div></td>
					       	<td width=\"6%\"><div style=\"text-align: center;\">".tgltoview($val['credits_account_due_date_new'])."</div></td>
					    </tr>
					";
					$totalpokokold 	+= $val['credits_account_last_balance_old'];
					$totalinterestold	+= $val['credits_account_interest_old'];
					$totalpokoknew 	+= $val['credits_account_last_balance_new'];
					$totalinterestnew	+= $val['credits_account_interest_new'];

					$no++;
				}
			} else {
				$tbl3 = "";
			}
		
			

			$tbl4 = "
				<tr>
					<td colspan =\"3\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctCreditsRescheduleReport_model->getUserName($auth['user_id'])."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-right: 1px solid black;\"><div style=\"font-size:9;font-weight:bold;text-align:center\">Total </div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\">".number_format($totalpokokold, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\"></div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black;border-left: 1px solid black;\"><div style=\"font-size:9;font-weight:bold;text-align:right\">".number_format($totalpokoknew, 2)."</div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"font-size:9;font-weight:bold;text-align:right\"></div></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					<td style=\"border-bottom: 1px solid black;border-top: 1px solid black\"></td>
					
					
				</tr>
							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR RESCHEDULLING PINJAMAN.pdf';
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

			$acctcreditsaccountreschedule	= $this->AcctCreditsRescheduleReport_model->getCreditsAccountReschedule($sesi['start_date'], $sesi['end_date'], $branch_id);

			
			if(count($acctcreditsaccountreschedule) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR RESCHEDULLING PINJAMAN")
									 ->setSubject("")
									 ->setDescription("DAFTAR RESCHEDULLING PINJAMAN")
									 ->setKeywords("DAFTAR, RESCHEDULLING, PINJAMAN")
									 ->setCategory("DAFTAR RESCHEDULLING PINJAMAN");
									 
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
				$this->excel->getActiveSheet()->mergeCells("F3:I3");
				$this->excel->getActiveSheet()->mergeCells("J3:M3");
				$this->excel->getActiveSheet()->mergeCells("B3:B4");
				$this->excel->getActiveSheet()->mergeCells("C3:C4");
				$this->excel->getActiveSheet()->mergeCells("D3:D4");
				$this->excel->getActiveSheet()->mergeCells("E3:E4");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:M3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:M3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->getStyle('B4:M4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B4:M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B4:M4')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR RESCHEDULLING PINJAMAN");

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Kredit");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Pinjaman Lama");
				$this->excel->getActiveSheet()->setCellValue('G3',"");
				$this->excel->getActiveSheet()->setCellValue('H3',"");
				$this->excel->getActiveSheet()->setCellValue('I3',"");
				$this->excel->getActiveSheet()->setCellValue('J3',"Pinjaman Baru");
				$this->excel->getActiveSheet()->setCellValue('K3',"");
				$this->excel->getActiveSheet()->setCellValue('L3',"");
				$this->excel->getActiveSheet()->setCellValue('M3',"");

				$this->excel->getActiveSheet()->setCellValue('B4',"");
				$this->excel->getActiveSheet()->setCellValue('C4',"");
				$this->excel->getActiveSheet()->setCellValue('D4',"");
				$this->excel->getActiveSheet()->setCellValue('E4',"");
				$this->excel->getActiveSheet()->setCellValue('F4',"Pokok");
				$this->excel->getActiveSheet()->setCellValue('G4',"Bunga");
				$this->excel->getActiveSheet()->setCellValue('H4',"JK Waktu");
				$this->excel->getActiveSheet()->setCellValue('I4',"JT Tempo");
				$this->excel->getActiveSheet()->setCellValue('J4',"Pokok");
				$this->excel->getActiveSheet()->setCellValue('K4',"Bunga");
				$this->excel->getActiveSheet()->setCellValue('L4',"JK Waktu");
				$this->excel->getActiveSheet()->setCellValue('M4',"JT Tempo");
				
				
				$no=0;
				$totalinterestnew = 0;
				$totalinterestold = 0;
				$totalpokoknew 	= 0;
				$totalpokokold 	= 0;
				$j=5;
				foreach($acctcreditsaccountreschedule as $key=>$val){
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
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['credits_account_serial'],PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, number_format($val['credits_account_last_balance_old'],2));
						$this->excel->getActiveSheet()->setCellValue('G'.$j, $val['credits_account_interest_old']);
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $val['credits_account_period_old']);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, tgltoview($val['credits_account_due_date_old']));
						$this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_account_last_balance_new'],2));
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_account_interest_new']);
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $val['credits_account_period_new']);
						$this->excel->getActiveSheet()->setCellValue('M'.$j, tgltoview($val['credits_account_due_date_new']));
			
						$totalpokokold 	+= $val['credits_account_last_balance_old'];
						$totalinterestold	+= $val['credits_account_interest_old'];
						$totalpokoknew 	+= $val['credits_account_last_balance_new'];
						$totalinterestnew	+= $val['credits_account_interest_new'];
						
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

				$this->excel->getActiveSheet()->setCellValue('F'.$i, number_format($totalpokokold,2));
				$this->excel->getActiveSheet()->setCellValue('G'.$i, "");
				$this->excel->getActiveSheet()->setCellValue('H'.$i, "");
				$this->excel->getActiveSheet()->setCellValue('I'.$i, "");
				$this->excel->getActiveSheet()->setCellValue('J'.$i, number_format($totalpokoknew,2));
				
				$filename='DAFTAR RESCHEDULLING PINJAMAN.xls';
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
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsMandatoryHasntPaidReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsMandatoryHasntPaidReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			
			$data['main_view']['corebranch']	= create_double($this->AcctSavingsMandatoryHasntPaidReport_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['monthlist']		= $this->configuration->Month();
			$data['main_view']['content']		= 'AcctSavingsMandatoryHasntPaidReport/ListAcctSavingsMandatoryHasntPaidReport_view';
			$this->load->view('MainPage_view',$data);
		}

		public function viewreport(){
			$month_now 	= date('m');
			$year_now 	= date('Y');
			$sesi = array (
				"month_period" 					=> $month_now,
				"year_period" 					=> $year_now,
				"start_date" 					=> tgltodb($this->input->post('start_date',true)),
				"end_date" 						=> tgltodb($this->input->post('end_date',true)),
				"branch_id"						=> $this->input->post('branch_id',true),
				"view"							=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
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

			$coremember 		= $this->AcctSavingsMandatoryHasntPaidReport_model->getCoreMemberDetail($sesi['start_date'],$sesi['end_date'], $branch_id);
			$acctcredits 		= $this->AcctSavingsMandatoryHasntPaidReport_model->getAcctCredits();
			$preferencecompany 		= $this->AcctSavingsMandatoryHasntPaidReport_model->getPreferenceCompany();

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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
				        <td><div style=\"text-align: center; font-size:14px\">DAFTAR TUNGGAKAN SIMPANAN WAJIB</div></td>
				    </tr>
				   
				</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"5%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">No.</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">No Anggota</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">Nama</div></td>
			        <td width=\"25%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">Terakhir Setor</div></td>
			        <td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:9;\">Tunggakan</div></td>		       
			       
			    </tr>				
			</table>";

			$no = 1;
			$totalplafon = 0;
			$totalangspokok = 0;
			$totalangsmargin = 0;
			$totalangs = 0;
			$totalsisa = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
		
			foreach ($coremember as $key => $val) {
				$mandatorysavings 	= $this->AcctSavingsMandatoryHasntPaidReport_model->getSavingsMandatoryDetail( $sesi['month_period'], $sesi['year_period'],$val['member_id']);
				
				$date_now 	= date("Y-m-d");
				//$date_now 	= date("2020-01-29");
				$month_now 	= date("m");

				if ($mandatorysavings->num_rows()== 0) {
					$last_month 	= $this->AcctSavingsMandatoryHasntPaidReport_model->getLastDate($val['member_id']);
					$now 			= new DateTime($date_now);
					$tunggakaan		= new DateTime($last_month);
					$Keterlambatan 	= $tunggakaan->diff($now);
					$bulan 			= $Keterlambatan->m;
					if($bulan >= 1){
						$tbl3 .= "
							<tr>
						    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_no']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".$val['member_name']."</div></td>
						        <td width=\"25%\"><div style=\"text-align: left;\">".tgltoview($last_month)."</div></td>
						        <td width=\"20%\"><div style=\"text-align: center;\">".$Keterlambatan->m."</div></td>

						    </tr>
						";
						$no++;
					}else{
						$tbl3 .= "
						
					    ";
					}
						
				
				}else{
					$tbl3 = "";
				}
				
			}
			

			$tbl4 = "
				
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'DAFTAR TUNGGAKAN SIMPANAN WAJIB.pdf';
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

			// $coremember	= $this->AcctSavingsMandatoryHasntPaidReport_model->getCreditsAccount($sesi['start_date'], $sesi['end_date'] ,$branch_id);
			// $acctcredits 		= $this->AcctSavingsMandatoryHasntPaidReport_model->getAcctCredits();
			$coremember 		= $this->AcctSavingsMandatoryHasntPaidReport_model->getCoreMemberDetail($sesi['start_date'],$sesi['end_date'], $branch_id);
			$acctcredits 		= $this->AcctSavingsMandatoryHasntPaidReport_model->getAcctCredits();
			
			if(count($coremember) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("DAFTAR TUNGGAKAN SIMPANAN WAJIB")
									 ->setSubject("")
									 ->setDescription("DAFTAR TUNGGAKAN SIMPANAN WAJIB")
									 ->setKeywords("DAFTAR, TUNGGAKAN ,SIMPANAN WAJIB")
									 ->setCategory("DAFTAR TUNGGAKAN SIMPANAN WAJIB");
									 
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

				
				$this->excel->getActiveSheet()->mergeCells("B1:F1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"DAFTAR TUNGGAKAN SIMPANAN WAJIB ");

					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No. Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Terakhir Angsur");
				$this->excel->getActiveSheet()->setCellValue('F3',"Tunggakan");
				// $this->excel->getActiveSheet()->setCellValue('G3',"Angs Pokok");
				// $this->excel->getActiveSheet()->setCellValue('H3',"Angs Bunga");
				// $this->excel->getActiveSheet()->setCellValue('I3',"Total Angsuran");
				// $this->excel->getActiveSheet()->setCellValue('J3',"Saldo Pokok (Outstanding)");
				// $this->excel->getActiveSheet()->setCellValue('K3',"Jumlah Denda");
				// $this->excel->getActiveSheet()->setCellValue('L3',"Tanggal Angsuran");
				// $this->excel->getActiveSheet()->setCellValue('M3',"Keterlambatan");
				
				
				$no=0;
				$totalplafon = 0;
				$totalangspokok = 0;
				$totalangsmargin = 0;
				$totalangs = 0;
				$totalsisa = 0;
				$j=4;

				foreach($coremember as $key=>$val){
					$mandatorysavings 	= $this->AcctSavingsMandatoryHasntPaidReport_model->getSavingsMandatoryDetail( $sesi['month_period'], $sesi['year_period'],$val['member_id']);
					
					if ($mandatorysavings->num_rows()== 0) {
						$last_month 	= $this->AcctSavingsMandatoryHasntPaidReport_model->getLastDate($val['member_id']);
						$now 			= new DateTime($date_now);
						$tunggakaan		= new DateTime($last_month);
						$Keterlambatan 	= $tunggakaan->diff($now);
						$bulan 			= $Keterlambatan->m;
						
						// if(is_numeric($key)){
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						

						if($bulan >= 1){
						$no++;

							$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
							$this->excel->getActiveSheet()->setCellValueExplicit('C'.$j, $val['member_no']);
							$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
							$this->excel->getActiveSheet()->setCellValue('E'.$j, tgltoview($last_month));
							$this->excel->getActiveSheet()->setCellValue('F'.$j, $Keterlambatan->m);
							// $this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['credits_account_principal_amount'],2));
							// $this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['credits_account_interest_amount'],2));
							// $this->excel->getActiveSheet()->setCellValue('I'.$j, number_format($val['credits_account_payment_amount'],2));
							// $this->excel->getActiveSheet()->setCellValue('J'.$j, number_format($val['credits_account_last_balance'],2));
							// $this->excel->getActiveSheet()->setCellValue('K'.$j, $val['credits_account_accumulated_fines']);
							// $this->excel->getActiveSheet()->setCellValue('L'.$j, tgltoview($val['credits_account_payment_date']));
							// $this->excel->getActiveSheet()->setCellValue('M'.$j, $interval->days.' Hari');
																				
						}else{
							continue;
						}
						
						// }else{
						// 	continue;
						// }
					}
					$j++;
				}

				$i = $j;
				
				$filename='DAFTAR TUNGGAKAN SIMPANAN WAJIB.xls';
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
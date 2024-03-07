<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsDailyTransferMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsDailyTransferMutation_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$corebranch 						= create_double_branch($this->AcctSavingsDailyTransferMutation_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 						= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']	= $corebranch;
			$data['main_view']['content'] 		= 'AcctSavingsDailyTransferMutation/FormFilterAcctSavingsDailyTransferMutation_view';
			$this->load->view('MainPage_view', $data);
		}

		public function viewreport(){
			$sesi = array (
				"start_date" 		=> tgltodb($this->input->post('start_date',true)),
				"end_date"			=> tgltodb($this->input->post('end_date', true)),
				"branch_id"			=> $this->input->post('branch_id',true),
				"view"				=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 			= $this->session->userdata('auth');
			if($auth['branch_status'] == 1){
				if($sesi['branch_id'] == '' || $sesi['branch_id'] == 0){
					$branch_id = '';
				} else {
					$branch_id = $sesi['branch_id'];
				}
			} else {
				$branch_id = $auth['branch_id'];
			}
			
			$preference		= $this->AcctSavingsDailyTransferMutation_model->getPreferenceCompany();

			$acctsavingstransfermutation	= $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutation($sesi['start_date'], $sesi['end_date'], $branch_id);

			// foreach ($acctsavingstransfermutation as $key => $val) {
			// 	$datatranfser[$val['savings_transfer_mutation_id']][] = array (
			// 	)
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
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preference['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

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
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td><div style=\"text-align: left;font-size:12;\">".$preference['company_name']."</div></td>			       
			    </tr>	

			    <tr>
			        <td><div style=\"text-align: left;font-size:12;font-weight:bold\">MUTASI SIMPANAN NON TUNAI TGL : &nbsp;&nbsp; ".tgltoview($sesi['start_date'])."&nbsp;&nbsp; S.D &nbsp;&nbsp;".tgltoview($sesi['end_date'])."</div></td>		
			       	       
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

			$totalnominalfrom = 0;
			$totalsaldofrom = 0;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			foreach ($acctsavingstransfermutation as $key => $val) {
				$acctsavingstransfermutationfrom = $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutationFrom($val['savings_transfer_mutation_id']);

				foreach ($acctsavingstransfermutationfrom as $kFrom => $vFrom) {
					$tbl3 .= "
						<tr>
					    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
					        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_transfer_mutation_date'])."</div></td>
					        <td width=\"16%\"><div style=\"text-align: left;\">".$vFrom['savings_account_no']."</div></td>
					        <td width=\"25%\"><div style=\"text-align: left;\">".$vFrom['member_name']."</div></td>
					        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctSavingsDailyTransferMutation_model->getMutationCode($vFrom['mutation_id'])."</div></td>
					        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($vFrom['savings_transfer_mutation_from_amount'], 2)."</div></td>
					        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($vFrom['savings_account_last_balance'], 2)."</div></td>
					    </tr>
					";

					$totalnominalfrom 	+= $vFrom['savings_transfer_mutation_from_amount'];
					$totalsaldofrom 	+= $vFrom['savings_account_last_balance'];

					$no++;
				}

				$acctsavingstransfermutationto = $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutationTo($val['savings_transfer_mutation_id']);

				foreach ($acctsavingstransfermutationto as $kTo => $vTo) {
				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">".$no."</div></td>
				        <td width=\"11%\"><div style=\"text-align: left;\">".tgltoview($val['savings_transfer_mutation_date'])."</div></td>
				        <td width=\"16%\"><div style=\"text-align: left;\">".$vTo['savings_account_no']."</div></td>
				        <td width=\"25%\"><div style=\"text-align: left;\">".$vTo['member_name']."</div></td>
				        <td width=\"8%\"><div style=\"text-align: center;\">".$this->AcctSavingsDailyTransferMutation_model->getMutationCode($vTo['mutation_id'])."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($vTo['savings_transfer_mutation_to_amount'], 2)."</div></td>
				        <td width=\"17%\"><div style=\"text-align: right;\">".number_format($vTo['savings_account_last_balance'], 2)."</div></td>
				    </tr>
				";

				$totalnominalto 	+= $vTo['savings_transfer_mutation_to_amount'];
				$totalsaldoto 		+= $vTo['savings_account_last_balance'];

				$no++;
			}
			}

			$grandtotalnominal 	= $totalnominalfrom + $totalnominalto;
			$grandtotalsaldo	= $totalsaldoto + $totalsaldofrom;
			

			$tbl4 = "
					<tr>
						<td colspan =\"4\" style=\"border-top: 1px solid black;\"><div style=\"font-size:10;text-align:left;font-style:italic\">Printed : ".date('d-m-Y H:i:s')."  ".$this->AcctSavingsDailyTransferMutation_model->getUserName($auth['user_id'])."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;font-weight:bold;text-align:center\">Jumlah </div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalnominal, 2)."</div></td>
						<td style=\"border-top: 1px solid black\"><div style=\"font-size:10;text-align:right\">".number_format($grandtotalsaldo, 2)."</div></td>
					</tr>
							
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Laporan Mutasi Harian Non Tunai Simpanan.pdf';
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

			$preference						= $this->AcctSavingsDailyTransferMutation_model->getPreferenceCompany();

			$acctsavingstransfermutation	= $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutation($sesi['start_date'], $sesi['end_date'], $branch_id);
			
			if(count($acctsavingstransfermutation) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Mutasi Harian Non Tunai Simpanan")
									 ->setSubject("")
									 ->setDescription("Laporan Mutasi Harian Non Tunai Simpanan")
									 ->setKeywords("Laporan, Mutasi, Harian, Non Tunai Simpanan")
									 ->setCategory("Laporan Mutasi Harian Non Tunai Simpanan");
									 
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
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);
				$this->excel->getActiveSheet()->setCellValue('B1',"MUTASI SIMPANAN NON TUNAI TGL : ".$sesi['start_date']." S.D ".$sesi['end_date']."");
									
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"Tanggal");
				$this->excel->getActiveSheet()->setCellValue('D3',"No. Rek");
				$this->excel->getActiveSheet()->setCellValue('E3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('F3',"Sandi");
				$this->excel->getActiveSheet()->setCellValue('G3',"Nominal");
				$this->excel->getActiveSheet()->setCellValue('H3',"Saldo");
				
				
				$a = 4;
				$no=0;
				foreach($acctsavingstransfermutation as $key=>$val){
					
					// $no++;
					$j=$a;
					
					
					
						$acctsavingstransfermutationfrom = $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutationFrom($val['savings_transfer_mutation_id']);

						foreach ($acctsavingstransfermutationfrom as $kForm => $vFrom) {
							if(is_numeric($kForm)){
								$no++;
								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
							
								

								$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
								$this->excel->getActiveSheet()->setCellValue('C'.$j, tgltoview($val['savings_transfer_mutation_date']));
								$this->excel->getActiveSheet()->setCellValueExplicit('D'.$j, $vFrom['savings_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
								$this->excel->getActiveSheet()->setCellValue('E'.$j, $vFrom['member_name']);
								$this->excel->getActiveSheet()->setCellValue('F'.$j, $this->AcctSavingsDailyTransferMutation_model->getMutationCode($vFrom['mutation_id']));
								$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($vFrom['savings_transfer_mutation_from_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($vFrom['savings_account_last_balance'],2));
							}else{
								continue;
							}
							$j++;
						}
						$i=$j;
	
						
				
						$acctsavingstransfermutationto = $this->AcctSavingsDailyTransferMutation_model->getAcctSavingsTransferMutationTo($val['savings_transfer_mutation_id']);
						
						foreach ($acctsavingstransfermutationto as $kTo => $vTo) {
							if(is_numeric($kTo)){
								$no++;

								$this->excel->setActiveSheetIndex(0);
								$this->excel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
								$this->excel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$this->excel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$this->excel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$this->excel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

								$this->excel->getActiveSheet()->setCellValue('B'.$i, $no);
								$this->excel->getActiveSheet()->setCellValue('C'.$i, tgltoview($val['savings_transfer_mutation_date']));
								$this->excel->getActiveSheet()->setCellValueExplicit('D'.$i, $vTo['savings_account_no'],PHPExcel_Cell_DataType::TYPE_STRING);
								$this->excel->getActiveSheet()->setCellValue('E'.$i, $vTo['member_name']);
								$this->excel->getActiveSheet()->setCellValue('F'.$i, $this->AcctSavingsDailyTransferMutation_model->getMutationCode($vTo['mutation_id']));
								$this->excel->getActiveSheet()->setCellValue('G'.$i, number_format($vTo['savings_transfer_mutation_to_amount'],2));
								$this->excel->getActiveSheet()->setCellValue('H'.$i, number_format($vTo['savings_account_last_balance'],2));

								$totalnominalto 	+= $vTo['savings_transfer_mutation_to_amount'];
								$totalsaldoto 		+= $vTo['savings_account_last_balance'];
							}else{
								continue;
							}
							$i++;
						}

						
					$a = $i;
				}

				$grandtotalnominal 	= $totalnominalfrom + $totalnominalto;
				$grandtotalsaldo	= $totalsaldoto + $totalsaldofrom;

				$m = $a;

				$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$m.':F'.$m);
				$this->excel->getActiveSheet()->setCellValue('B'.$m, 'Total');

				$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($grandtotalnominal,2));
				$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($grandtotalsaldo,2));
				
				$filename='Laporan_Mutasi_Harian_Non_Tunai_Simpanan.xls';
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
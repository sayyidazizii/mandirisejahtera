<?php ob_start(); ?>
<?php
	ini_set('memory_limit', '512M');
	defined('BASEPATH') OR exit('No direct script access allowed');


	Class TaxReport extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('TaxReport_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$corebranch 									= create_double_branch($this->TaxReport_model->getCoreBranch(),'branch_id','branch_name');
			$corebranch[0] 									= 'Semua Cabang';
			ksort($corebranch);
			$data['main_view']['corebranch']				= $corebranch;
			$data['main_view']['acctsavings']				= create_double($this->TaxReport_model->getAcctSavings(),'savings_id','savings_name');
			$data['main_view']['kelompoklaporansimpanan']	= $this->configuration->KelompokLaporanSimpanan1();	
			$data['main_view']['content']					= 'TaxReport/ListTaxReport_view';
			$this->load->view('MainPage_view',$data);
		}
 
		public function viewreport(){
			$sesi = array (
				"branch_id"					=> $this->input->post('branch_id', true),
				"start_date" 				=> tgltodb($this->input->post('start_date',true)),
				"end_date" 					=> tgltodb($this->input->post('end_date',true)),
				"view"						=> $this->input->post('view',true),
			);

			if($sesi['view'] == 'pdf'){
				$this->processPrinting($sesi);
			} else {
				$this->export($sesi);
			}
		}

		public function processPrinting($sesi){
			$auth 				=	$this->session->userdata('auth'); 
			$preferencecompany 	= $this->TaxReport_model->getPreferenceCompany();
			
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

			$datatax 		= $this->TaxReport_model->getTaxReport($sesi['start_date'], $sesi['end_date'], $preferencecompany['account_income_tax_id']);
			$totaltax = 0;
			
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new tcpdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			    require_once(dirname(__FILE__).'/lang/eng.php');
			    $pdf->setLanguageArray($l);
			}

			// set font
			$pdf->SetFont('helvetica', 'B', 20);

			// add a page
			$pdf->AddPage();

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
			<br/>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"100%\"><div style=\"text-align: left; font-size:14px; font-weight:bold\">DAFTAR PAJAK ".$sesi['start_date']." - ".$sesi['end_date']."</div></td>
			    </tr>
			</table>
			<br>";

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Tanggal</div></td>
					<td width=\"50%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Keterangan</div></td>
			        <td width=\"30%\" style=\"font-weight:bold; border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Jumlah</div></td>
			    </tr>				
			</table>";

			if(count($datatax) > 0){
			foreach($datatax as $key => $val){
				$tbl1 .= "
						<tr>
							<td width=\"20%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">".$val['journal_voucher_date']."</div></td>
							<td width=\"50%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: left;font-size:10;\">".$val['journal_voucher_description']."</div></td>
							<td width=\"30%\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: right;font-size:10;\">".number_format($val['journal_voucher_debit_amount'], 2)."</div></td>
						</tr>";
				$totaltax += $val['journal_voucher_debit_amount'];
			}
			}else{
				$tbl1 .= "
					<tr>
						<td width=\"100%\" colspan =\"3\" style=\"border-bottom: 1px solid black;border-top: 1px solid black\"><div style=\"text-align: center;font-size:10;\">Data Kosong</div></td>
					</tr>";
			}
			
			$tbltot = "
					<tr>
						<td colspan =\"1\"><div style=\"border-bottom: 1px solid black;border-top: 1px solid black; font-size:10;font-weight:bold;text-align:center\"> </div></td>
						<td><div style=\"border-bottom: 1px solid black;border-top: 1px solid black; font-size:10;font-weight:bold;text-align:center\">Total </div></td>
						<td><div style=\"border-bottom: 1px solid black;border-top: 1px solid black; font-size:10;text-align:right\">".number_format($totaltax, 2)."</div></td>
					</tr>
				</table>
				<br>
			";

			$pdf->writeHTML($tbl0.$tbl1.$tbltot, true, false, false, '');

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
			$auth 				= $this->session->userdata('auth'); 
			$preferencecompany 	= $this->TaxReport_model->getPreferenceCompany();
			
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

			$datatax 		= $this->TaxReport_model->getTaxReport($sesi['start_date'], $sesi['end_date'], $preferencecompany['account_income_tax_id']);
			$totaltax = 0;

			
			if(count($datatax) !=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("CST FISRT")
									 ->setLastModifiedBy("CST FISRT")
									 ->setTitle("Laporan Data Pajak")
									 ->setSubject("")
									 ->setDescription("Laporan Data Pajak")
									 ->setKeywords("Laporan Data Pajak")
									 ->setCategory("Laporan Data Pajak");
									 
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
					$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Data Pajak");
				} else {
					$this->excel->getActiveSheet()->setCellValue('B1',"Laporan Data Pajak");
				}
					
				
				$this->excel->getActiveSheet()->setCellValue('B3',"Tanggal");
				$this->excel->getActiveSheet()->setCellValue('C3',"Keterangan");
				$this->excel->getActiveSheet()->setCellValue('D3',"Jumlah");
				
				$no=0;
				$saldo = 0;
						$nov= 0;
						$j=1;
							$subtotalbasil = 0;
							$subtotalsaldo = 0;
						foreach($datatax as $key3=>$val3){
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

						

						$m = 13;

						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
						$this->excel->getActiveSheet()->getStyle('B'.$m.':H'.$m)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->mergeCells('B'.$m.':G'.$m);
						$this->excel->getActiveSheet()->setCellValue('B'.$m, 'SubTotal');

						$this->excel->getActiveSheet()->setCellValue('G'.$m, number_format($subtotalbasil,2));
						$this->excel->getActiveSheet()->setCellValue('H'.$m, number_format($subtotalsaldo,2));

						$i = $m + 1;
					

					$totaltax += $saldo;

				}

				if($sesi['kelompok_laporan_simpanan'] == 0){

				$n = $j;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');
				$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totaltax,2));

				$n = 12;

				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FFFF00');
				$this->excel->getActiveSheet()->getStyle('B'.$n.':F'.$n)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->mergeCells('B'.$n.':E'.$n);
				$this->excel->getActiveSheet()->setCellValue('B'.$n, 'Total');
				$this->excel->getActiveSheet()->setCellValue('F'.$n, number_format($totaltax,2));
				
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
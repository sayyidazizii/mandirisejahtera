<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctRescheduleCredit extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->model('Core_account_Officer_model');
			$this->load->model('Core_source_fund_model');
			$this->load->model('AcctDepositoAccount_model');
			$this->load->model('AcctCredit_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
			define('FINANCIAL_MAX_ITERATIONS', 128);
			define('FINANCIAL_PRECISION', 1.0e-08);
		}
		
		public function index(){
			
		}

		public function detailAcctCreditsAccount(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsAccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id']		= '';
				$sesi['credits_id']		= '';
			}

			$start_date = tgltodb($sesi['start_date']);
			$end_date 	= tgltodb($sesi['end_date']);

			$data['main_view']['coremember']				= create_double($this->AcctCreditAccount_model->getCoreMember($auth['branch_id']), 'member_id', 'member_name');

			$data['main_view']['acctcredits']				= create_double($this->AcctCreditAccount_model->getAcctCredits(), 'credits_id', 'credits_name');

			$data['main_view']['acctcreditsaccount']		= $this->AcctCreditAccount_model->getAcctCreditsAccount($start_date, $end_date, $auth['branch_id'], $sesi['member_id'], $sesi['credits_id']);

			$data['main_view']['content']					= 'AcctCreditAccount/ListAcctCreditsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				'start_date'			=> $this->input->post('start_date',true),
				'end_date'				=> $this->input->post('end_date',true),
				'member_id'				=> $this->input->post('member_id',true),
				'credits_id'			=> $this->input->post('credits_id',true),
			);
			$this->session->set_userdata('filter-AcctCreditsAccount', $data);
			redirect('credit-account/detail');
		}
		
		public function reset_search(){
			$sesi= $this->session->userdata('filter-AcctCreditsAccount');
			$this->session->unset_userdata('filter-AcctCreditsAccount');
			redirect('credit-account/detail');
		}

		
		public function addform(){
			$credits_account_id=$this->uri->segment(3);
			
			$data['main_view']['credit_account']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['content']				= 'AcctRecheduleCredit/FormAddAcctRecheduleCredit_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function akadlist(){
			$auth = $this->session->userdata('auth');
			$list = $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
			$segment3=$this->uri->segment(3);
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	             $row[] = '<a href="'.base_url().'AcctRescheduleCredit/addform/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctCreditAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctCreditAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        echo json_encode($output);
			
		}
		
		public function addcreditaccount(){
			$auth 					= $this->session->userdata('auth');
			$credits_account_id 	= $this->input->post('credits_account_id',true);
			$payment_type_id 		= $this->input->post('payment_type_id',true);
			$acctcreditsaccount 	= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);

			$credits_account_date 	= date('Y-m-d');

			$credits_account_payment_date = date('Y-m-d', strtotime("+1 months", strtotime($credits_account_date)));

			$data = array (
				"credits_account_id"						=> $this->input->post('credits_account_id',true),
				"credits_account_date" 						=> date('Y-m-d'),
				"credits_account_period"					=> $this->input->post('new_credits_account_period',true),
				"credits_account_due_date"					=> tgltodb($this->input->post('new_due_date',true)),
				"credits_account_interest"					=> $this->input->post('new_credits_account_interest',true),
				"credits_account_last_balance"				=> $this->input->post('new_credits_account_last_balance',true),
				"credits_account_principal_amount"			=> $this->input->post('new_credits_account_principal_amount',true),
				"credits_account_interest_amount"			=> $this->input->post('new_credits_account_margin_amount',true),
				"credits_account_payment_amount"			=> $this->input->post('new_credits_account_payment_amount',true),
				"credits_account_payment_to"				=> 0,
				"credits_account_accumulated_fines"			=> 0,
				"credits_account_payment_date"				=> $credits_account_payment_date,
				"credits_reschedule_status"					=> 1,
			);

			if($this->AcctCreditAccount_model->updateAcctCreditAccount($data)){
				$datareschedule = array (
					'branch_id'										=> $auth['branch_id'],
					'credits_account_id'							=> $data['credits_account_id'],
					'credits_account_last_balance_old'				=> $this->input->post('credits_account_last_balance_old', true),
					'credits_account_interest_old'					=> $this->input->post('credits_account_interest_old', true),
					'credits_account_period_old'					=> $this->input->post('credits_account_period_old', true),
					'credits_account_date_old'						=> $this->input->post('credits_account_date_old', true),
					'credits_account_due_date_old'					=> $this->input->post('credits_account_due_date_old', true),
					'credits_account_payment_to_old'				=> $this->input->post('credits_account_payment_to_old', true),
					'credits_account_last_balance_new'				=> $data['credits_account_last_balance'],
					'credits_account_interest_new'					=> $data['credits_account_interest'],
					'credits_account_period_new'					=> $data['credits_account_period'],
					'credits_account_date_new'						=> $data['credits_account_date'],
					'credits_account_due_date_new'					=> $data['credits_account_due_date'],

				);

				if($this->AcctCreditAccount_model->insertAcctCreditsAccountReschedule($datareschedule)){
					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Credit Berjangka Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addacctcreditaccount-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					$url='AcctRescheduleCredit/showdetail/'.$data['credits_account_id'].'/'.$payment_type_id;
					redirect($url);
				} else{
					$this->session->set_userdata('addacctdepositoaccount',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Credit Berjangka Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					$url='AcctRescheduleCredit/addform';
					redirect($url);
				}
			}
		}
		
		public function showdetail(){
			$credits_account_id 	= $this->uri->segment(3);
			$payment_type_id 		= $this->uri->segment(4);

			if($payment_type_id== '' || $payment_type_id == 1){
				$datapola=$this->flat($credits_account_id);
			}else if($payment_type_id == 2){
				$datapola=$this->anuitas($credits_account_id);
			}

			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();
			$data['main_view']['membergender']			= $this->configuration->MemberGender();
			$data['main_view']['credit_account']		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$data['main_view']['datapola']				= $datapola;
			$data['main_view']['content']				= 'AcctRecheduleCredit/FormDetailAcctRescheduleCredit_view';
			$this->load->view('MainPage_view',$data);
		}

		
		public function angsuran(){
			$id=$this->uri->segment(3);
			$type=$this->uri->segment(4);

			if($type== '' || $type == 1){
				$datapola=$this->flat($credits_account_id);
			}else if($type == 2){
				$datapola=$this->anuitas($credits_account_id);
			}
			
			$creditaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($this->uri->segment(3));
			redirect('AcctRescheduleCredit/showdetail/'.$id.'/'.$type,compact('datapola'));
		}
		
		public function cekPolaAngsuran(){
			$id=$this->input->post('id_credit',true);
			$pola=$this->input->post('pola_angsuran',true);
			$url='AcctRescheduleCredit/angsuran/'.$id.'/'.$pola;
			redirect($url);
		}

		public function flat($id){
			$credistaccount					= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			/*print_r("credistaccount ");
			print_r($credistaccount);
			exit;*/

			$total_credits_account 			= $credistaccount['credits_account_last_balance'];

			$credits_account_interest 		= $credistaccount['credits_account_interest'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

/*			$jangkawaktuth 					= $jangkawaktu/12;
			$percentageth = ($margin*100)/$pinjaman;
			$percentagebl=round($percentageth/$jangkawaktu,2);
			
			$angsuranpokok=round($pinjaman/$jangkawaktuth/12,2);
			$angsuranmargin=round($pinjaman*$percentageth/100/12,2);
			$totangsuran=$angsuranpokok+$angsuranmargin;*/
			$installment_pattern			= array();
			$opening_balance				= $total_credits_account;

			for($i=1; $i<=$credits_account_period; $i++){
				/*$totpokok=$totpokok+$angsuranpokok;
				$sisapokok=$pinjaman-$totpokok;*/

				$angsuran_pokok									= $total_credits_account / $credits_account_period;				

				$angsuran_margin								= ($total_credits_account * $credits_account_interest) / 100;				

				$angsuran 										= $angsuran_pokok + $angsuran_margin;

				$last_balance 									= $opening_balance - $angsuran_pokok;

				$installment_pattern[$i]['opening_balance']		= $opening_balance;
				$installment_pattern[$i]['ke'] 					= $i;
				$installment_pattern[$i]['angsuran'] 			= $angsuran;
				$installment_pattern[$i]['angsuran_pokok']		= $angsuran_pokok;
				$installment_pattern[$i]['angsuran_bunga'] 		= $angsuran_margin;
				$installment_pattern[$i]['akumulasi_pokok'] 	= $totpokok;
				$installment_pattern[$i]['last_balance'] 		= $last_balance;
				
				$opening_balance 								= $last_balance;
			}
			
			return $installment_pattern;
			
		}
		
		public function slidingrate($id){
			$creditsaccount 	= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			/*print_r("detailpinjaman ");
			print_r($detailpinjaman);
			exit;*/
			$credits_account_net_price 		= $creditsaccount['credits_account_net_price'];
			$credits_account_um 			= $creditsaccount['credits_account_um'];
			$credits_account_margin 		= $creditsaccount['credits_account_margin'];
			$credits_account_period 		= $creditsaccount['credits_account_period'];			

			$total_credits_account 			= $credits_account_net_price - $credits_account_um;




			
			$jangkawaktuth 		= $jangkawaktu/12;
			$percentageth 		= ($margin*100)/$pinjaman;
			$percentagebl 		= round($percentageth/$jangkawaktu,2);
			
			$angsuranpokok 		= round($pinjaman/$jangkawaktuth/12,2);
			
			$pola 				= array();
			$totpinjaman 		= $pinjaman;
			$totpokok 			= 0;
			for($i=1; $i<=$jangkawaktu; $i++){
				$angsuranmargin 				= round(($totpinjaman * $percentageth/100)/$jangkawaktu,2);
				$totangsuran 					= $angsuranpokok + $angsuranmargin;
				$totpokok						= $totpokok + $angsuranpokok;
				$sisapokok 						= $pinjaman - $totpokok;
				$pola[$i]['ke']					= $i;
				$pola[$i]['angsuran']			= $totangsuran;
				$pola[$i]['angsuran_pokok']		= $angsuranpokok;
				$pola[$i]['angsuran_margin']	= $angsuranmargin;
				$pola[$i]['akumulasi_pokok']	= $totpokok;
				$pola[$i]['sisa_pokok']			= $sisapokok;
				$totpinjaman					= $totpinjaman - $angsuranpokok;
			}
			
			return $pola;
			
		}
		
		public function anuitas($id){
			$creditsaccount 	= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			$pinjaman 	= $creditsaccount['credits_account_last_balance'];
			$bunga 		= $creditsaccount['credits_account_interest'] / 100;
			$period 	= $creditsaccount['credits_account_period'];

			$bungaA 		= pow((1 + $bunga), $period);
			$bungaB 		= pow((1 + $bunga), $period) - 1;
			$bAnuitas 		= $bungaA / $bungaB;
			$totangsuran 	= $pinjaman * $bunga * $bAnuitas;

			$sisapinjaman = $pinjaman;
			for ($i=1; $i <= $period ; $i++) {
				$angsuranbunga 	= $sisapinjaman * $bunga;
				$angsuranpokok 	= $totangsuran - $angsuranbunga;
				$sisapokok 		= $sisapinjaman - $angsuranpokok;


				$pola[$i]['ke']					= $i;
				$pola[$i]['opening_balance']	= $sisapinjaman;
				$pola[$i]['angsuran']			= $totangsuran;
				$pola[$i]['angsuran_pokok']		= $angsuranpokok;
				$pola[$i]['angsuran_bunga']		= $angsuranbunga;
				$pola[$i]['last_balance']		= $sisapokok;

				$sisapinjaman = $sisapinjaman - $angsuranpokok;
			}

			return $pola;

		}
		
		function RATE($nper, $pmt, $pv, $fv = 0.0, $type = 0, $guess = 0.1) {
			$rate = $guess;
			if (abs($rate) < $this->FINANCIAL_PRECISION) {
				$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
			} else {
				$f = exp($nper * log(1 + $rate));
				$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
			}
			$y0 = $pv + $pmt * $nper + $fv;
			$y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;

			// find root by secant method
			$i  = $x0 = 0.0;
			$x1 = $rate;
			while ((abs($y0 - $y1) > $this->FINANCIAL_PRECISION) && ($i < $this->FINANCIAL_MAX_ITERATIONS)) {
				$rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
				$x0 = $x1;
				$x1 = $rate;

				if (abs($rate) < $this->FINANCIAL_PRECISION) {
					$y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
				} else {
					$f = exp($nper * log(1 + $rate));
					$y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
				}

				$y0 = $y1;
				$y1 = $y;
				++$i;
			}
			return $rate;
		}

		public function printPolaAngsuran(){
			$credits_account_id 	= $this->input->post('id_credit', true);
			$type					= $this->input->post('pola', true);
			if($type== '' || $type==1){
				$datapola=$this->flat($credits_account_id);
			}else{
				$datapola=$this->anuitas($credits_account_id);
			}

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$paymenttype 			= $this->configuration->PaymentType();
			// print_r($acctcreditsaccount);exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			// set document information
			/*$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('');
			$pdf->SetTitle('');
			$pdf->SetSubject('');
			$pdf->SetKeywords('TCPDF, PDF, example, test, guide');*/

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

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top
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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------

			/*print_r($preference_company);*/
			
			$tblheader = "
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><b>Pola Angsuran</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>No. Akad</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"10%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Tipe Angsuran</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$paymenttype[$acctcreditsaccount['payment_type_id']]."</b></div>
						</td>			
	 				</tr>
	 			</table>
	 			<br><br>
			";
				
			$pdf->writeHTML($tblheader, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
			    <tr>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;\">Ke</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Saldo Pokok</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Angsuran Pokok</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Angsuran Bunga</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Total Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;\">Sisa Pokok</div></td>

			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
				// print_r($acctcreditspayment);exit;

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran_bunga'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['angsuran'], 2)." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp; </div></td>
				       	
				    </tr>
				";

				$no++;
				$totalpokok += $val['angsuran_pokok'];
				$totalmargin += $val['angsuran_bunga'];
				$total += $val['angsuran'];
			}

			$tbl4 = "
				<tr>
					<td colspan=\"2\"><div style=\"text-align: right;font-weight:bold\">Total</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalpokok, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($totalmargin, 2)."</div></td>
					<td><div style=\"text-align: right;font-weight:bold\">".number_format($total, 2)."</div></td>
				</tr>							
			</table>";
			


			

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');

			

			ob_clean();

			$filename = 'Pola_Angsuran_'.$acctcreditsaccount['credits_account_serial'].'.pdf';
			$pdf->Output($filename, 'I');

			// exit;
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			// $filename = 'IST Test '.$testingParticipantData['participant_name'].'.pdf';
			// $pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
	}
?>
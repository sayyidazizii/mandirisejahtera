<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctCreditsPaymentSuspend extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('AcctCreditsPaymentSuspend_model');
			$this->load->model('AcctCreditAccount_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('AcctSavingsCashMutation_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi'); 
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}

		public function index(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');

			$data['main_view']['acctcredits']	= create_double($this->AcctCreditAccount_model->getAcctCredits(),'credits_id', 'credits_name');
			$data['main_view']['corebranch']	= create_double($this->AcctCreditAccount_model->getCoreBranch(),'branch_id', 'branch_name');
			$data['main_view']['content']		= 'AcctCreditsPaymentSuspend/ListAcctCreditsPaymentSuspend_view';
			$this->load->view('MainPage_view', $data);
		}

		public function filter(){
			$data = array (  
				'start_date'	=> tgltodb($this->input->post('start_date', true)),
				'end_date'		=> tgltodb($this->input->post('end_date', true)),
				'credits_id'	=> $this->input->post('credits_id', true),
				'branch_id'		=> $this->input->post('branch_id', true),
			);

			$this->session->set_userdata('filter-AcctCreditsPaymentSuspend', $data);
			redirect('credits-payment-suspend');
		}
		public function reset(){
			$this->session->unset_userdata('filter-AcctCreditsPaymentSuspend');
			redirect('credits-payment-suspend');
		}

		public function angsuransuspend($id){
			$credistaccount					= $this->AcctCreditAccount_model->getCreditsAccount_Detail($id);

			$creditspaymentsuspend			= $this->AcctCreditAccount_model->getCreditsPaymentSuspend_Detail($id);

			$total_credits_account 			= $credistaccount['credits_account_last_balance'];
			$credits_account_interest 		= $credistaccount['credits_account_interest'];
			$credits_account_period 		= $credistaccount['credits_account_period'];

			$paymentsuspen					= array();
			$opening_balance				= $total_credits_account;

			$credits_payment_to 			= $credistaccount['credits_account_payment_to']+ 1;

			$credits_account_period 		= $credistaccount['credits_account_period'];

			$credits_grace_period 			= $creditspaymentsuspend['credits_grace_period'];

			
				for($i=$credits_payment_to; $i<=$credits_account_period; $i++){

				$a = $i - $credits_payment_to;

				$tanggal_angsuran = date('d-m-Y', strtotime("+".$a." months", strtotime($creditspaymentsuspend['credits_payment_date_new'])));

				
				$angsuran_pokok									= $credistaccount['credits_account_principal_amount'];				

				$angsuran_margin								= $credistaccount['credits_account_interest_amount'];				

				$angsuran 										= $angsuran_pokok + $angsuran_margin;

				$last_balance 									= $opening_balance - $angsuran_pokok;


				$paymentsuspen[$i]['opening_balance']		= $opening_balance;
				$paymentsuspen[$i]['ke'] 					= $i;
				$paymentsuspen[$i]['tanggal_angsuran'] 		= $tanggal_angsuran;
				$paymentsuspen[$i]['angsuran'] 				= $angsuran;
				$paymentsuspen[$i]['angsuran_pokok']		= $angsuran_pokok;
				$paymentsuspen[$i]['angsuran_bunga'] 		= $angsuran_margin;
				/*$installment_pattern[$i]['akumulasi_pokok'] 	= $totpokok;*/
				$paymentsuspen[$i]['last_balance'] 			= $last_balance;
				
				$opening_balance 							= $last_balance;
			
		}

			
			return $paymentsuspen;
			
		}

		

		public function getAcctCreditsPaymentSuspend(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-AcctCreditsPaymentSuspend');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['credits_id']		='';
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}
			} else {
				if($auth['branch_status'] == 1){
					$sesi['branch_id']	= '';
				}
				if($auth['branch_status'] == 0){
					$sesi['branch_id']	= $auth['branch_id'];
				}

				/*print_r(" Sesi");*/
			}

			$creditspaymentperiod = $this->configuration->CreditsPaymentPeriod();

			$list = $this->AcctCreditsPaymentSuspend_model->get_datatables($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $cashpayment) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $cashpayment->credits_account_serial;
	            $row[] = $cashpayment->member_name;
	            $row[] = $cashpayment->credits_name;
	            $row[] = $creditspaymentperiod[$cashpayment->credits_payment_period];
	            $row[] = $cashpayment->credits_grace_period;
	            $row[] = tgltoview($cashpayment->credits_payment_date_old);
	            $row[] = tgltoview($cashpayment->credits_payment_date_new);
	            $row[] = '
			    		
			    		<a href="'.base_url().'credits-payment-suspend/print-schedule/'.$cashpayment->credits_account_id.'" class="btn btn-xs yellow-lemon" role="button"><i class="fa fa-print"></i> Jadwal Angsuran Baru</a>';
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" 				=> $_POST['draw'],
	                        "recordsTotal" 		=> $this->AcctCreditsPaymentSuspend_model->count_all($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "recordsFiltered" 	=> $this->AcctCreditsPaymentSuspend_model->count_filtered($sesi['start_date'] , $sesi['end_date'], $sesi['credits_id'], $sesi['branch_id']),
	                        "data" 				=> $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function printScheduleCreditsPayment(){
			$credits_account_id 	= $this->uri->segment(3);

			$acctcreditsaccount		= $this->AcctCreditAccount_model->getAcctCreditsAccount_Detail($credits_account_id);
			$paymenttype 			= $this->configuration->PaymentType();
			$preferencecompany 		= $this->AcctCreditAccount_model->getPreferenceCompany();		
			$paymentperiod 			= $this->configuration->CreditsPaymentPeriod();	

			if($acctcreditsaccount['payment_type_id'] == '' || $acctcreditsaccount['payment_type_id'] == 1){
				$datapola=$this->angsuransuspend($credits_account_id);
			}else if ($acctcreditsaccount['payment_type_id'] == 2){
				$datapola=$this->anuitas($credits_account_id);
			}
			// print_r('data');
			// print_r($datapola);exit;
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			
			$pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(10, 10, 10, 10); // put space of 10 on top

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

			/*print_r($preference_company);*/
			
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tblheader = "
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
				<table id=\"items\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
					<tr>
						<td style=\"text-align:center;\" width=\"100%\">
							<div style=\"font-size:14px\";><BR><b>Pola Angsuran Baru</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>No. Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_serial']."</b></div>
						</td>

						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jenis Pinjaman</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$this->AcctCreditAccount_model->getAcctCreditsName($acctcreditsaccount['credits_id'])."</b></div>
						</td>		
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Nama</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['member_name']."</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Jangka Waktu</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: ".$acctcreditsaccount['credits_account_period']." ".$paymentperiod[$acctcreditsaccount['credits_payment_period']]."</b></div>
						</td>			
	 				</tr>
	 				<tr>
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Tipe Angsuran</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"45%\">
							<div style=\"font-size:12px\";><b>: ".$paymenttype[$acctcreditsaccount['payment_type_id']]."</b></div>
						</td>	
						<td style=\"text-align:left;\" width=\"20%\">
							<div style=\"font-size:12px\";><b>Plafon</b></div>
						</td>
						<td style=\"text-align:left;\" width=\"50%\">
							<div style=\"font-size:12px\";><b>: Rp.".number_format($acctcreditsaccount['credits_account_amount'])."</b></div>
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
			        <td width=\"5%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Ke</div></td>
			        <td width=\"12%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Tanggal Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Saldo Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Pokok</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Angsuran Bunga</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Total Angsuran</div></td>
			        <td width=\"18%\"><div style=\"text-align: center;font-size:10;font-weight:bold\">Sisa Pokok</div></td>

			       
			    </tr>				
			</table>";

			$no = 1;

			$tbl2 = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">";
		
			foreach ($datapola as $key => $val) {
			 //print_r($val);exit;

				$tbl3 .= "
					<tr>
				    	<td width=\"5%\"><div style=\"text-align: left;\">&nbsp; ".$val['ke']."</div></td>
				    	<td width=\"12%\"><div style=\"text-align: right;\">".tgltoview($val['tanggal_angsuran'])." &nbsp; </div></td>
				        <td width=\"18%\"><div style=\"text-align: right;\">".number_format($val['opening_balance'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_pokok'], 2)." &nbsp; </div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['angsuran_bunga'], 2)." &nbsp; </div></td>
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
					<td colspan=\"3\"><div style=\"text-align: right;font-weight:bold\">Total</div></td>
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

		public function addAcctCreditsPaymentSuspend(){	
			$credits_account_id 	= $this->uri->segment(3);

			$data['main_view']['creditspaymentperiod']	= $this->configuration->CreditsPaymentPeriod();
			$data['main_view']['accountcredit']			= $this->AcctCreditAccount_model->getDetailByID($credits_account_id);
			$data['main_view']['content']				= 'AcctCreditsPaymentSuspend/FormAddAcctCreditsPaymentSuspend_view';
			$this->load->view('MainPage_view',$data);
		} 

		public function akadlisttunai(){
			$auth 	= $this->session->userdata('auth');
			$list 	= $this->AcctCreditAccount_model->get_datatables($auth['branch_id']);
	        $data 	= array();
	        $no 	= $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->credits_account_serial;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_no;
	            $row[] = tgltoview($customers->credits_account_date);
	            $row[] = tgltoview($customers->credits_account_due_date);
	             $row[] = '<a href="'.base_url().'credits-payment-suspend/add/'.$customers->credits_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	    
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" 				=> $_POST['draw'],
	                        "recordsTotal" 		=> $this->AcctCreditAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" 	=> $this->AcctCreditAccount_model->count_filtered($auth['branch_id']),
	                        "data" 				=> $data,
	                );
	        echo json_encode($output);
			
		}

		public function processAddAcctCreditsPaymentSuspend(){
			$auth 									= $this->session->userdata('auth');

			$data = array(
				'branch_id'							=> $auth['branch_id'],
				'member_id'							=> $this->input->post('member_id', true),
				'credits_id'						=> $this->input->post('credits_id', true),
				'credits_account_id'				=> $this->input->post('credits_account_id', true),
				'credits_payment_suspend_date'		=> date('Y-m-d'),
				'credits_payment_period'			=> $this->input->post('credits_payment_period', true),
				'credits_grace_period'				=> $this->input->post('credits_grace_period', true),
				'credits_payment_date_old'			=> tgltodb($this->input->post('credits_payment_date_old', true)),
				'credits_payment_date_new'			=> tgltodb($this->input->post('credits_payment_date_new', true)),
				'created_id'						=> $auth['user_id'],
				'created_on'						=> date('Y-m-d H:i:s'),
			);

		
			$this->form_validation->set_rules('credits_payment_period', 'Periode Penundaan Angsuran', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctCreditsPaymentSuspend_model->insert($data)){
					$updatedata = array(
						"credits_account_id" 					=> $data['credits_account_id'],
						"credits_account_payment_date"			=> $data['credits_payment_date_new'],
					);
					// print_r($updatedata);exit;

					$this->AcctCreditsPaymentSuspend_model->updateAcctCreditsAccount($updatedata);

					


					$auth = $this->session->userdata('auth');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addAcctCreditsPaymentSuspend-'.$sesi['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('credits-payment-suspend');
				}else{
					$this->session->set_userdata('addAcctCreditsPaymentSuspend-'.$unique['unique'],$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Pembayaran Pinjaman Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('credits-payment-suspend/add');
				}
				
			}else{
				$this->session->set_userdata('addAcctCreditsPaymentSuspend-'.$unique['unique'],$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('credits-payment-suspend/add');
			}
		}
		

	}
?>
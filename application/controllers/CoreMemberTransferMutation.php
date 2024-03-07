<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreMemberTransferMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMemberTransferMutation_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$sesi	= 	$this->session->userdata('filter-coremembertransfermutation');
			if(!is_array($sesi)){
				$sesi['start_date']	= date('Y-m-d');
				$sesi['end_date']	= date('Y-m-d');
				$sesi['member_id']	= '';
			}

			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('membertransfermutationtoken-'.$unique['unique']);

			$data['main_view']['coremember']					= create_double($this->CoreMemberTransferMutation_model->getCoreMember(),'member_id', 'member_name');

			$data['main_view']['coremembertransfermutation'] 	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation($sesi['start_date'], $sesi['end_date'], $sesi['member_id']);

			$data['main_view']['content']						= 'CoreMemberTransferMutation/ListCoreMemberTransferMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"member_id"		=> $this->input->post('member_id',true),
			);

			$this->session->set_userdata('filter-coremembertransfermutation',$data);
			redirect('member-transfer-mutation');
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addcoremembertransfermutation-'.$unique['unique']);
			$this->session->unset_userdata('savings_account_from_id');
			redirect('member-transfer-mutation/add');
		}

		public function addCoreMember(){
			$auth = $this->session->userdata('auth');

			$list = $this->CoreMember_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = '<a href="'.base_url().'member-transfer-mutation/add/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMember_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->CoreMember_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function addAcctSavingsAccount(){
			$member_id = $this->uri->segment(3);
			$auth = $this->session->userdata('auth');

			$list = $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'member-transfer-mutation/add/'.$member_id.'/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}
		
		public function addCoreMemberTransferMutation(){
			$member_id 					= $this->uri->segment(3);
			$savings_account_id 		= $this->uri->segment(4);

			$unique	= $this->session->userdata('unique');
			$token 	= $this->session->userdata('membertransfermutationtoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('membertransfermutationtoken-'.$unique['unique'], $token);
			}

			
			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($member_id);	

			$data['main_view']['acctsavingsaccount']		= $this->CoreMemberTransferMutation_model->getAcctSavingsAccount_Detail($savings_account_id);

			$data['main_view']['acctmutation']				= $this->CoreMemberTransferMutation_model->getAcctMutation();	

			$data['main_view']['content']					= 'CoreMemberTransferMutation/FormAddCoreMemberTransferMutation_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function processAddCoreMemberTransferMutation(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$data = array(
				'branch_id'										=> $auth['branch_id'],
				'member_id'										=> $this->input->post('member_id', true),
				'savings_id'									=> $this->input->post('savings_id', true),
				'savings_account_id'							=> $this->input->post('savings_account_id', true),
				'mutation_id'									=> $this->input->post('mutation_id', true),
				'member_transfer_mutation_date'					=> tgltodb($this->input->post('member_transfer_mutation_date', true)),
				'member_mandatory_savings_opening_balance'		=> $this->input->post('member_mandatory_savings_last_balance', true),
				'member_mandatory_savings'						=> $this->input->post('member_mandatory_savings', true),
				'member_mandatory_savings_last_balance'			=> $this->input->post('member_mandatory_savings_last_balance', true) + $this->input->post('member_mandatory_savings', true),
				'member_transfer_mutation_token'				=> $this->input->post('member_transfer_mutation_token', true),
				'operated_name'									=> $auth['username'],
				'created_id'									=> $auth['user_id'],
				'created_on'									=> date('Y-m-d H:i:s'),
			);

			$this->form_validation->set_rules('member_id', 'Anggota', 'required');
			$this->form_validation->set_rules('savings_account_id', 'No. Rekening', 'required');
			$this->form_validation->set_rules('member_mandatory_savings', 'Jumlah Setor', 'required');

			$member_name = $this->CoreMemberTransferMutation_model->getCoreMemberName($data['member_id']);

			$transaction_module_code = "AGTTR";

			$transaction_module_id 	= $this->CoreMemberTransferMutation_model->getTransactionModuleID($transaction_module_code);

			$member_transfer_mutation_token 	= $this->CoreMemberTransferMutation_model->getMemberTransferMutationToken($data['member_transfer_mutation_token']);
					
			
			if($this->form_validation->run()==true){
				if($member_transfer_mutation_token->num_rows() == 0){
					if($this->CoreMemberTransferMutation_model->insertCoreMemberTransferMutation($data)){

						$membertransfer_last 	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Last($data['created_on']);
							
						$journal_voucher_period = date("Ym", strtotime($data['member_transfer_mutation_date']));
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> $data['member_transfer_mutation_date'],
							'journal_voucher_title'			=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$membertransfer_last['member_name'],
							'journal_voucher_description'	=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$membertransfer_last['member_name'],
							'journal_voucher_token'			=> $data['member_transfer_mutation_token'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $membertransfer_last['member_transfer_mutation_id'],
							'transaction_journal_no' 		=> $membertransfer_last['member_no'],
							'created_id' 					=> $data['created_id'],
							'created_on' 					=> $data['created_on'],
						);
						
						$this->CoreMemberTransferMutation_model->insertAcctJournalVoucher($data_journal);
	
						$journal_voucher_id = $this->CoreMemberTransferMutation_model->getJournalVoucherID($data['created_id']);
	
						$preferencecompany 	= $this->CoreMemberTransferMutation_model->getPreferenceCompany();

						$account_id 		= $this->CoreMemberTransferMutation_model->getAccountID($data['savings_id']);

						$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_debit = array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$member_name,
							'journal_voucher_amount'		=> $data['member_mandatory_savings'],
							'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
							'account_id_status'				=> 0,
							'created_id'					=> $auth['user_id'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'1',
						);

						$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_debit);

						$account_id = $this->CoreMemberTransferMutation_model->getAccountID($preferencecompany['mandatory_savings_id']);

						$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);

						$data_credit =array(
							'journal_voucher_id'			=> $journal_voucher_id,
							'account_id'					=> $account_id,
							'journal_voucher_description'	=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$member_name,
							'journal_voucher_amount'		=> $data['member_mandatory_savings'],
							'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
							'account_id_status'				=> 1,
							'created_id'					=> $auth['user_id'],
							'account_id_default_status'		=> $account_id_default_status,
							'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'0',
						);

						$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
						
	
	
						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Debit  Simpanan Wajib Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addcoremembertransfermutation-'.$sesi['unique']);
						$this->session->unset_userdata('membertransfermutationtoken-'.$unique['unique']);
						$this->session->unset_userdata('savings_account_from_id');
						$this->session->set_userdata('message',$msg);
						redirect('member-transfer-mutation');
					}else{
						$this->session->set_userdata('addcoremembertransfermutation',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah DataTransfer Simpanan Wajib Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('member-transfer-mutation');
					}
				} else {
					$membertransfer_last 	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Last($data['created_on']);
							
					$journal_voucher_period = date("Ym", strtotime($data['member_transfer_mutation_date']));
					
					$data_journal = array(
						'branch_id'						=> $auth['branch_id'],
						'journal_voucher_period' 		=> $journal_voucher_period,
						'journal_voucher_date'			=> $data['member_transfer_mutation_date'],
						'journal_voucher_title'			=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$membertransfer_last['member_name'],
						'journal_voucher_description'	=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$membertransfer_last['member_name'],
						'journal_voucher_token'			=> $data['member_transfer_mutation_token'],
						'transaction_module_id'			=> $transaction_module_id,
						'transaction_module_code'		=> $transaction_module_code,
						'transaction_journal_id' 		=> $membertransfer_last['member_transfer_mutation_id'],
						'transaction_journal_no' 		=> $membertransfer_last['member_no'],
						'created_id' 					=> $data['created_id'],
						'created_on' 					=> $data['created_on'],
					);

					$journal_voucher_token 	= $this->CoreMemberTransferMutation_model->getJournalVoucherToken($data_journal['journal_voucher_token']);

					if($journal_voucher_token->num_rows() == 0){
						$this->CoreMemberTransferMutation_model->insertAcctJournalVoucher($data_journal);
					}
					
					$journal_voucher_id = $this->CoreMemberTransferMutation_model->getJournalVoucherID($data['created_id']);

					$preferencecompany 	= $this->CoreMemberTransferMutation_model->getPreferenceCompany();

					$account_id 		= $this->CoreMemberTransferMutation_model->getAccountID($data['savings_id']);

					$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);

					$data_debit = array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $account_id,
						'journal_voucher_description'	=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$member_name,
						'journal_voucher_amount'		=> $data['member_mandatory_savings'],
						'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
						'account_id_status'				=> 0,
						'account_id_default_status'		=> $account_id_default_status,
						'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'1',
					);

					$journal_voucher_item_token = $this->CoreMemberTransferMutation_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_debit);
					}


					$account_id = $this->CoreMemberTransferMutation_model->getAccountID($preferencecompany['mandatory_savings_id']);

					$account_id_default_status = $this->CoreMemberTransferMutation_model->getAccountIDDefaultStatus($account_id);

					$data_credit =array(
						'journal_voucher_id'			=> $journal_voucher_id,
						'account_id'					=> $account_id,
						'journal_voucher_description'	=> 'SETOR SIMPANAN WAJIB NON TUNAI '.$member_name,
						'journal_voucher_amount'		=> $data['member_mandatory_savings'],
						'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
						'account_id_status'				=> 1,
						'created_id'					=> $auth['user_id'],
						'account_id_default_status'		=> $account_id_default_status,
						'journal_voucher_item_token'	=> $data['member_transfer_mutation_token'].$account_id.'0',
					);

					$journal_voucher_item_token = $this->CoreMemberTransferMutation_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

					if($journal_voucher_item_token->num_rows() == 0){
						$this->CoreMemberTransferMutation_model->insertAcctJournalVoucherItem($data_credit);
					}


					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Debit  Simpanan Wajib Sukses
							</div> ";
					$sesi = $this->session->userdata('unique');
					$this->session->unset_userdata('addcoremembertransfermutation-'.$sesi['unique']);
					$this->session->unset_userdata('membertransfermutationtoken-'.$unique['unique']);
					$this->session->unset_userdata('savings_account_from_id');
					$this->session->set_userdata('message',$msg);
					redirect('member-transfer-mutation');
				}
				
			}else{
				$this->session->set_userdata('addcoremembertransfermutation',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member-transfer-mutation');
			}
		}

		public function printCoreMemberTransferMutation(){
			$auth = $this->session->userdata('auth');

			$member_transfer_mutation_id 	= $this->uri->segment(3);

			$coremembertransfermutation	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Detail($member_transfer_mutation_id);

			$preferencecompany 	= $this->CoreMemberTransferMutation_model->getPreferenceCompany();


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

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

			$pdf->SetFont('helvetica', '', 12);

			// -----------------------------------------------------------------------------

			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"20%\">".$img."</td>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">BUKTI TRANSFER SIMPANAN WAJIB</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			Telah diterima uang dari :
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$this->CoreMemberTransferMutation_model->getMemberName($coremembertransfermutation['member_id_savings'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$coremembertransfermutation['savings_account_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$this->CoreMemberTransferMutation_model->getMemberAddress($coremembertransfermutation['member_id_savings'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($coremembertransfermutation['member_mandatory_savings'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: TRANSFER SIMPANAN WAJIB A.N. ".$coremembertransfermutation['member_name']." (".$coremembertransfermutation['member_no'].")</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($coremembertransfermutation['member_mandatory_savings'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctSavingsAccount_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
			    </tr>
			    <tr>
			        <td width=\"30%\"><div style=\"text-align: center;\">Penyetor</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">Teller/Kasir</div></td>
			    </tr>				
			</table>";

			$pdf->writeHTML($tbl1.$tbl2, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			$js = '';
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function validationCoreMemberTransferMutation(){
			$auth = $this->session->userdata('auth');
			$member_transfer_mutation_id = $this->uri->segment(3);

			$data = array (
				'member_transfer_mutation_id'  	=> $member_transfer_mutation_id,
				'validation'					=> 1,
				'validation_id'					=> $auth['user_id'],
				'validation_on'					=> date('Y-m-d H:i:s'),
			);

			if($this->CoreMemberTransferMutation_model->validationCoreMemberTransferMutation($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Debit  Simpanan Wajib Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('member-transfer-mutation/print-validation/'.$member_transfer_mutation_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Debit  Simpanan Wajib Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member-transfer-mutation');
			}
		}

		public function printValidationCoreMemberTransferMutation(){
			$member_transfer_mutation_id 	= $this->uri->segment(3);

			$coremembertransfermutation	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Detail($member_transfer_mutation_id);


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

			$pdf->SetFont('helveticaI', '', 7);
			$preferencecompany 	= $this->CoreMemberTransferMutation_model->getPreferenceCompany();
			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"1200%\" height=\"520%\"/>";

			$tbl1 = "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
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

			$pdf->writeHTML($tbl1, true, false, false, false, '');

			$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$coremembertransfermutation['savings_account_no']."</div></td>
			        <td width=\"45%\"><div style=\"text-align: right; font-size:14px\">".$this->CoreMemberTransferMutation_model->getMemberName($coremembertransfermutation['member_id_savings'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$coremembertransfermutation['validation_on']."</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->CoreMemberTransferMutation_model->getUsername($coremembertransfermutation['validation_id'])."</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($coremembertransfermutation['member_mandatory_savings'], 2)."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			
			
			ob_clean();


			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Validasi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
		public function voidCoreMemberTransferMutation(){
			$data['main_view']['coremembertransfermutation']	= $this->CoreMemberTransferMutation_model->getCoreMemberTransferMutation_Detail($this->uri->segment(3));
			$data['main_view']['content']					= 'CoreMemberTransferMutation/FormVoidCoreMemberTransferMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidCoreMemberTransferMutation(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_transfer_mutation_id"	=> $this->input->post('savings_transfer_mutation_id',true),
				"voided_on"					=> date('Y-m-d H:i:s'),
				'data_state'				=> 2,
				"voided_remark" 			=> $this->input->post('voided_remark',true),
				"voided_id"					=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->CoreMemberTransferMutation_model->voidCoreMemberTransferMutation($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('member-transfer-mutation');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Mutasi Setoran Simpanan Non Tunai Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member-transfer-mutation');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member-transfer-mutation');
			}
		}

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcoremembertransfermutation-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addcoremembertransfermutation-'.$unique['unique'],$sessions);
		}
		
		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addcoremembertransfermutation-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addcoremembertransfermutation-'.$unique['unique'],$sessions);
		}

		// public function reset_data(){
		// 	$unique 	= $this->session->userdata('unique');
		// 	$sessions	= $this->session->unset_userdata('addcoremembertransfermutation-'.$unique['unique']);
		// 	redirect('member-transfer-mutation/add');
		// }
		
		
	}
?>
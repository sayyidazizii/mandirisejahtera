<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctJournalVoucher extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctJournalVoucher_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctjournalvoucher');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['branch_id']		= $auth['branch_id'] ;	
			}else{
				if(!$sesi['branch_id']){
					$sesi['branch_id']		= $auth['branch_id'];	
				}
			}
			$unique 		= $this->session->userdata('unique');
			$this->session->unset_userdata('addacctjournalvoucher-'.$unique['unique']);
			$this->session->unset_userdata('addacctjournalvoucheritem-'.$unique['unique']);
			$this->session->unset_userdata('acctjournalvouchertoken-'.$unique['unique']);
						
			$data['main_view']['acctaccount']			= create_double($this->AcctJournalVoucher_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['corebranch']			= create_double($this->AcctJournalVoucher_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctjournalvoucher']	= $this->AcctJournalVoucher_model->getAcctJournalVoucher($sesi['start_date'], $sesi['end_date'], $sesi['branch_id']);
			$data['main_view']['content']				= 'AcctJournalVoucher/ListAcctJournalVoucher_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctjournalvoucher',$data);
			redirect('journal-voucher');
		}
		public function reset_search(){
			$this->session->unset_userdata('filter-acctjournalvoucher');
			redirect('journal-voucher');
		}
		public function addAcctJournalVoucher(){
			$sesi 	= $this->session->userdata('unique');
			$token 	= $this->session->userdata('acctjournalvouchertoken-'.$sesi['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctjournalvouchertoken-'.$sesi['unique'], $token);
			}

			$data['main_view']['accountstatus']		= $this->configuration->AccountStatus();
			$data['main_view']['acctaccount']		= create_double($this->AcctJournalVoucher_model->getAcctAccount(),'account_id','account_code');
			$data['main_view']['content']			= 'AcctJournalVoucher/FormAddAcctJournalVoucher_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctjournalvoucher-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctjournalvoucher-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$sesi 		= $this->session->userdata('unique');
			$this->session->unset_userdata('addacctjournalvoucher-'.$sesi['unique']);
			$this->session->unset_userdata('addacctjournalvoucheritem-'.$sesi['unique']);
			redirect('journal-voucher/add');
		}

		public function processAddArrayAcctjournalVoucher(){
			$date = date('YmdHis');
			$data_acctjournalvoucheritem = array(
				'record_id'								=> $date.$this->input->post('account_id', true),
				'account_id'							=> $this->input->post('account_id', true),
				'journal_voucher_status'				=> $this->input->post('journal_voucher_status', true),
				'journal_voucher_amount'				=> $this->input->post('journal_voucher_amount', true),
				'journal_voucher_description_item'		=> $this->input->post('journal_voucher_description_item', true),
			);

			$this->form_validation->set_rules('account_id', 'Account Name', 'required');
			
			if($this->form_validation->run()==true){
				$unique 			= $this->session->userdata('unique');
				$session_name 		= $this->input->post('session_name',true);
				$dataArrayHeader	= $this->session->userdata('addacctjournalvoucheritem-'.$unique['unique']);
				
				$dataArrayHeader[$data_acctjournalvoucheritem['record_id']] = $data_acctjournalvoucheritem;
				
				$this->session->set_userdata('addacctjournalvoucheritem-'.$unique['unique'],$dataArrayHeader);

				$data_acctjournalvoucheritem = $this->session->userdata('addacctjournalvoucher-'.$unique['unique']);
				
				$data_acctjournalvoucheritem['record_id']							= '';
				$data_acctjournalvoucheritem['account_id']							= '';
				$data_acctjournalvoucheritem['journal_voucher_status'] 				= '';
				$data_acctjournalvoucheritem['journal_voucher_amount'] 				= '';
				$data_acctjournalvoucheritem['journal_voucher_description_item'] 	= '';

				$this->session->set_userdata('addacctjournalvoucher-'.$unique['unique'],$data_acctjournalvoucheritem);
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
			}
		}
		
		public function processAddAcctJournalVoucher(){
			$auth = $this->session->userdata('auth');
			$sesi = $this->session->userdata('unique');
			$acctjournalvoucheritem = $this->session->userdata('addacctjournalvoucheritem-'.$sesi['unique']);

			$journal_voucher_period = date("Ym", strtotime($this->input->post('journal_voucher_date', true)));

			$transaction_module_code = "JU";

			$transaction_module_id 		= $this->AcctJournalVoucher_model->getTransactionModuleID($transaction_module_code);

			$data = array(
				'branch_id'						=> $auth['branch_id'],
				'journal_voucher_period' 		=> $journal_voucher_period,
				'journal_voucher_date'			=> tgltodb($this->input->post('journal_voucher_date', true)),
				'journal_voucher_title'			=> $this->input->post('journal_voucher_description', true),
				'journal_voucher_description'	=> $this->input->post('journal_voucher_description', true),
				'journal_voucher_token'			=> $this->input->post('journal_voucher_token', true),
				'transaction_module_id'			=> $transaction_module_id,
				'transaction_module_code'		=> $transaction_module_code,
				'created_id'					=> $auth['user_id'],
				'created_on'					=> date('Y-m-d H:i:s'),
			);

			
			$this->form_validation->set_rules('journal_voucher_description', 'Uraian', 'required');

			$journal_voucher_token = $this->AcctJournalVoucher_model->getJournalVoucherToken($data['journal_voucher_token']);

			if($this->form_validation->run()==true){
				if(!empty($acctjournalvoucheritem)){
					if($journal_voucher_token->num_rows() == 0){
						if($this->AcctJournalVoucher_model->insertAcctJournalVoucher($data)){
							$journal_voucher_id = $this->AcctJournalVoucher_model->getJournalVoucherID($data['created_id']);

							foreach ($acctjournalvoucheritem as $key => $val) {
								if($val['journal_voucher_status'] == 0){
									$data_debet =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $val['account_id'],
										'journal_voucher_description'	=> $data['journal_voucher_description'],
										'journal_voucher_amount'		=> $val['journal_voucher_amount'],
										'journal_voucher_debit_amount'	=> $val['journal_voucher_amount'],
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['journal_voucher_token'].$val['record_id'],
									);
									$this->AcctJournalVoucher_model->insertAcctJournalVoucherItem($data_debet);
								} else {
									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $val['account_id'],
										'journal_voucher_description'	=> $data['journal_voucher_description'],
										'journal_voucher_amount'		=> $val['journal_voucher_amount'],
										'journal_voucher_credit_amount'	=> $val['journal_voucher_amount'],
										'account_id_status'				=> 1,
										'journal_voucher_item_token'	=> $data['journal_voucher_token'].$val['record_id'],
									);

									$this->AcctJournalVoucher_model->insertAcctJournalVoucherItem($data_credit);
								}
							}


							$auth = $this->session->userdata('auth');
							// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Data Jurnal Umum Sukses
									</div> ";

							$data 		= '';
							$sesi 		= $this->session->userdata('unique');
							$this->session->set_userdata('addacctjournalvoucher-'.$sesi['unique'], $data);
							$this->session->set_userdata('addacctjournalvoucheritem-'.$sesi['unique'], $data);
							$this->session->set_userdata('acctjournalvouchertoken-'.$sesi['unique'], $data);
							$this->session->unset_userdata('addacctjournalvoucher-'.$sesi['unique']);
							$this->session->unset_userdata('addacctjournalvoucheritem-'.$sesi['unique']);
							$this->session->unset_userdata('acctjournalvouchertoken-'.$sesi['unique']);
							$this->session->set_userdata('message',$msg);
							redirect('journal-voucher');
						}else{
							$this->session->set_userdata('addacctjournalvoucher',$data);
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Tambah Jurnal Umum Tidak Berhasil
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('journal-voucher/add');
						}
					} else {
						$journal_voucher_id = $this->AcctJournalVoucher_model->getJournalVoucherID($data['created_id']);

						foreach ($acctjournalvoucheritem as $key => $val) {
							if($val['journal_voucher_status'] == 0){
								$data_debet =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $val['account_id'],
									'journal_voucher_description'	=> $data['journal_voucher_description'],
									'journal_voucher_amount'		=> $val['journal_voucher_amount'],
									'journal_voucher_debit_amount'	=> $val['journal_voucher_amount'],
									'account_id_status'				=> 0,
									'journal_voucher_item_token'	=> $data['journal_voucher_token'].$val['account_id'],
								);

								$journal_voucher_item_token = $this->AcctJournalVoucher_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->AcctJournalVoucher_model->insertAcctJournalVoucherItem($data_debet);
								}
								
							} else {
								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $val['account_id'],
									'journal_voucher_description'	=> $data['journal_voucher_description'],
									'journal_voucher_amount'		=> $val['journal_voucher_amount'],
									'journal_voucher_credit_amount'	=> $val['journal_voucher_amount'],
									'account_id_status'				=> 1,
									'journal_voucher_item_token'	=> $data['journal_voucher_token'].$val['account_id'],
								);

								$journal_voucher_item_token = $this->AcctJournalVoucher_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->AcctJournalVoucher_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}


						$auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Jurnal Umum Sukses
								</div> ";

						$data 		= '';
						$sesi 		= $this->session->userdata('unique');
						$this->session->set_userdata('addacctjournalvoucher-'.$sesi['unique'], $data);
						$this->session->set_userdata('addacctjournalvoucheritem-'.$sesi['unique'], $data);
						$this->session->set_userdata('acctjournalvouchertoken-'.$sesi['unique'], $data);
						$this->session->unset_userdata('addacctjournalvoucher-'.$sesi['unique']);
						$this->session->unset_userdata('addacctjournalvoucheritem-'.$sesi['unique']);
						$this->session->unset_userdata('acctjournalvouchertoken-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('journal-voucher');
					}
					
				} else {
					$this->session->set_userdata('addacctjournalvoucher',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									No. Perkiraan Masih Kosong
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('journal-voucher/add');
				}
				
			}else{
				$this->session->set_userdata('addacctjournalvoucher',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('journal-voucher/add');
			}
		}
		
		public function deleteAcctJournalVoucher(){
			if($this->AcctJournalVoucher_model->deleteAcctJournalVoucher($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Mutasi Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('journal-voucher');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Mutasi Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('journal-voucher');
			}
		}
		public function processPrinting(){
			$auth 	=	$this->session->userdata('auth'); 
					
			
			$journal_voucher_id 		= $this->uri->segment(3);

			$preferencecompany 			= $this->AcctJournalVoucher_model->getPreferenceCompany();
			$acctjournalvoucher 		= $this->AcctJournalVoucher_model->getAcctJournalVoucher_Detail($journal_voucher_id);
			$acctjournalvoucheritem 	= $this->AcctJournalVoucher_model->getAcctJournalVoucherItem_Detail($journal_voucher_id);

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

			$pdf->SetFont('helvetica', '', 10);

			// -----------------------------------------------------------------------------

			// print_r($preferencecompany['logo_koperasi']);exit;
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"3\" width=\"20%\">".$img."</td>
			        <td><div style=\"text-align: center; font-size:14px;font-weight: bold\">JURNAL UMUM</div></td>
			    </tr>
			     <tr>
			        <td><div style=\"text-align: center; font-size:10px\">".$acctjournalvoucher['branch_name']."</div></td>
			    </tr>
			    <tr>
			        <td><div style=\"text-align: center; font-size:10px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			
			$tbl1 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Tanggal Jurnal</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".tgltoview($acctjournalvoucher['journal_voucher_date'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Jurnal</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctjournalvoucher['journal_voucher_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Uraian</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctjournalvoucher['journal_voucher_description']."</div></td>
			    </tr>		
			</table>";

			$tbl2 = "
			<br>
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
				<tr>
					<td width=\"5%\"><div style=\"text-align: center;font-weight: bold\">No.</div></td>
					<td width=\"40%\"><div style=\"text-align: center;font-weight: bold\">Perkiraan</div></td>
					<td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Debet</div></td>
					<td width=\"20%\"><div style=\"text-align: center;font-weight: bold\">Kredit</div></td>
				</tr>
			";
			$no =1;
			foreach ($acctjournalvoucheritem as $key => $val) {
				$tbl3 .= "
					    <tr>
					        <td width=\"5%\"><div style=\"text-align: center;font-size:12px\">".$no."</div></td>
					        <td width=\"40%\"><div style=\"text-align: left;font-size:12px\">(".$val['account_code'].") ".$val['account_name']."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_debit_amount'],2)."</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\">".number_format($val['journal_voucher_credit_amount'],2)."</div></td>
					    </tr>
				";
				$total_debet += $val['journal_voucher_debit_amount'];
				$total_kredit += $val['journal_voucher_credit_amount'];
				$no++;
			}
			$tbl4 = "
				<tr>
			        <td width=\"5%\"><div style=\"text-align: center;font-size:12px\"></div></td>
			        <td width=\"40%\"><div style=\"text-align: left;font-size:12px\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: right;font-size:12px\"></div></td>
			    </tr>		
			</table>

			<table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
				<tr>
					<td colspan=\"2\" width=\"45%\"></td>
					<td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_debet, 2)."</div></td>
					<td width=\"20%\"><div style=\"text-align: right;font-weight:bold\">".number_format($total_kredit, 2)."</div></td>
				</tr>
			</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Jurnal_'.$acctjournalvoucher['journal_voucher_no'].'_'.$acctjournalvoucher['journal_voucher_date'].'.pdf';

			// // force print dialog
			// $js .= 'print(true);';

			// // set javascript
			// $pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

	}
?>
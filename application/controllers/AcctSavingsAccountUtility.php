<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsAccountUtility extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsAccountUtility_model');
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
			$sesi	= $this->session->userdata('filter-acctsavingsaccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
				
			}

			$unique = $this->session->userdata('unique');
			$this->session->unset_userdata('addacctsavingsaccount-'.$unique['unique']);
			$this->session->unset_userdata('acctsavingsaccounttoken-'.$unique['unique']);

			$data['main_view']['acctsavingsaccount']	= $this->AcctSavingsAccountUtility_model->getAcctSavingsAccountUtility($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);
			$data['main_view']['corebranch']			= create_double($this->AcctSavingsAccountUtility_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['acctsavings']			= create_double($this->AcctSavingsAccountUtility_model->getAcctSavings(),'savings_id', 'savings_name');	
			$data['main_view']['content']			= 'AcctSavingsAccountUtility/ListAcctSavingsAccountUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"savings_id"	=> $this->input->post('savings_id',true),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-acctsavingsaccount',$data);
			redirect('savings-account-utility');
		}

		public function getAcctSavingsAccountUtilityList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-acctsavingsaccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
				
			}

			
			$list = $this->AcctSavingsAccountUtility_model->get_datatables_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->savings_name;
	            $row[] = tgltoview($savingsaccount->savings_account_date);
	            $row[] = number_format($savingsaccount->savings_account_first_deposit_amount, 2);
	            $row[] = number_format($savingsaccount->savings_account_last_balance, 2);
	            if($savingsaccount->validation == 0){
	            	$row[] = '<a href="'.base_url().'savings-account-utility/print-note/'.$savingsaccount->savings_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>
				        <a href="'.base_url().'savings-account-utility/validation/'.$savingsaccount->savings_account_id.'" class="btn btn-xs green-jungle" role="button"><i class="fa fa-check"></i> Validasi</a>';
				        // <a href="'.base_url().'AcctSavingsAccountUtility/voidAcctSavingsAccountUtility/'.$savingsaccount->savings_account_id.'" class="btn btn-xs red" role="button"><i class="fa fa-trash-o"></i> Batal</a>';
			    } else {
			    	$row[] = '<a href="'.base_url().'savings-account-utility/print-note/'.$savingsaccount->savings_account_id.'" class="btn btn-xs blue" role="button"><i class="fa fa-print"></i> Kwitansi</a>';
				        // <a href="'.base_url().'AcctSavingsAccountUtility/voidAcctSavingsAccountUtility/'.$savingsaccount->savings_account_id.'" class="btn btn-xs red" role="button"><i class="fa fa-trash-o"></i> Batal</a>';
			    }
	            
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccountUtility_model->count_all_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccountUtility_model->count_filtered_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function getMasterDataSavingsAccount(){
			$data['main_view']['acctsavings']		= create_double($this->AcctSavingsAccountUtility_model->getAcctSavings(),'savings_id', 'savings_name');
			$data['main_view']['corebranch']		= create_double($this->AcctSavingsAccountUtility_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctSavingsAccountUtility/ListMasterDataSavingsAccount_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filtermasterdata(){
			$data = array (
				"start_date" 	=> tgltodb($this->input->post('start_date',true)),
				"end_date" 		=> tgltodb($this->input->post('end_date',true)),
				"savings_id"	=> $this->input->post('savings_id',true),
				"branch_id"		=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-masterdataacctsavingsaccount',$data);
			redirect('savings-account-utility/get-master');
		}

		public function getMasterDataSavingsAccountList(){
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-masterdataacctsavingsaccount');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_id']		='';
				if($auth['branch_status'] == 0){
					$sesi['branch_id']		= $auth['branch_id'];
				} else {
					$sesi['branch_id']		= '';
				}
			}

			$list = $this->AcctSavingsAccountUtility_model->get_datatables_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->savings_name;
	            $row[] = tgltoview($savingsaccount->savings_account_date);
	            $row[] = number_format($savingsaccount->savings_account_first_deposit_amount, 2);
	            $row[] = number_format($savingsaccount->savings_account_last_balance, 2);
	            $data[] = $row;
	        }



	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccountUtility_model->count_all_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccountUtility_model->count_filtered_master($sesi['start_date'], $sesi['end_date'], $sesi['savings_id'], $sesi['branch_id']),
	                        "data" => $data,
	                );

	        // print_r($output['recordsTotal']);exit;
	        //output to json format
	        echo json_encode($output);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsaccount-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addacctsavingsaccount-'.$unique['unique'],$sessions);
		}

		public function reset_data(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('addacctsavingsaccount-'.$unique['unique']);
			redirect('savings-account-utility/add');
		}

		public function getListCoreMember(){
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
	            $row[] = '<a href="'.base_url().'savings-account-utility/add/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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
		
		public function addAcctSavingsAccountUtility(){
			$member_id 	= $this->uri->segment(3);
			$unique 	= $this->session->userdata('unique');
			$token 		= $this->session->userdata('acctsavingsaccounttoken-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('acctsavingsaccounttoken-'.$unique['unique'], $token);
			}


			$data['main_view']['coremember']			= $this->AcctSavingsAccountUtility_model->getCoreMember_Detail($this->uri->segment(3));	
			$data['main_view']['acctsavings']			= create_double($this->AcctSavingsAccountUtility_model->getAcctSavings(),'savings_id', 'savings_name');	
			$data['main_view']['coreoffice']			= create_double($this->AcctSavingsAccountUtility_model->getCoreOffice(),'office_id', 'office_name');	
			$data['main_view']['membergender']			= $this->configuration->MemberGender();
			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();
			$data['main_view']['familyrelationship']	= $this->configuration->FamilyRelationship();
			$data['main_view']['content']				= 'AcctSavingsAccountUtility/FormAddAcctSavingsAccountUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getCoreMember_Detail(){
			$member_id 	= $this->input->post('member_id');

			// $member_id = 25;
			
			$data 			= $this->AcctSavingsAccountUtility_model->getCoreMember_Detail($member_id);
			// print_r($data);
			$membergender	= $this->configuration->MemberGender();
			$memberidentity = $this->configuration->MemberIdentity();

			$result = array();
			$result = array(
				"member_no"					=> $data['member_no'], 
				"member_date_of_birth" 		=> $data['member_date_of_birth'], 
				"member_gender"				=> $membergender[$data['member_gender']],
				"member_address"			=> $data['member_address'],
				"city_name"					=> $data['city_name'],
				"kecamatan_name"			=> $data['kecamatan_name'],
				"member_job"				=> $data['member_job'],
				"identity_name"				=> $memberidentity[$data['identity_id']],
				"member_identity_no"		=> $data['member_identity_no'],
				"member_phone"				=> $data['member_phone'],
			);
			echo json_encode($result);		
		}

		public function getSavingsAccountNo(){
			$auth = $this->session->userdata('auth');

			$savings_id 	= $this->input->post('savings_id');

			// $savings_id = 3;
			
			$branchcode = $this->AcctSavingsAccountUtility_model->getBranchCode($auth['branch_id']);
			$savingscode = $this->AcctSavingsAccountUtility_model->getSavingsCode($savings_id);
			$lastsavingsaccountno = $this->AcctSavingsAccountUtility_model->getLastAccountSavingsNo($auth['branch_id'], $savings_id);
			$savingsnisbah = $this->AcctSavingsAccountUtility_model->getSavingsNisbah($savings_id);

			if($lastsavingsaccountno->num_rows() <> 0){      
			   //jika kode ternyata sudah ada.      
			   $data = $lastsavingsaccountno->row_array();    
			   $kode = intval($data['last_savings_account_no']) + 1;    
			 } else {      
			   //jika kode belum ada      
			   $kode = 1;    
			}
			
			$kodemax 				= str_pad($kode, 5, "0", STR_PAD_LEFT);
			$new_savings_account_no = $savingscode.$branchcode.$kodemax;

			$result = array ();
			$result = array (
				'savings_nisbah'			=> $savingsnisbah,
			);

			echo json_encode($result);			
		}
		
		public function processAddAcctSavingsAccountUtility(){
			$auth = $this->session->userdata('auth');

			$username = $this->AcctSavingsAccountUtility_model->getUsername($auth['user_id']);

			
			$data = array(
				'member_id'									=> $this->input->post('member_id', true),
				'savings_id'								=> $this->input->post('savings_id', true),
				'office_id'									=> $this->input->post('office_id', true),
				'savings_account_date'						=> date('Y-m-d'),
				'branch_id'									=> $auth['branch_id'],
				'savings_account_no'						=> $this->input->post('savings_account_no', true),
				'savings_account_first_deposit_amount'		=> $this->input->post('savings_account_first_deposit_amount', true),
				'savings_account_last_balance'				=> $this->input->post('savings_account_first_deposit_amount', true),
				'savings_account_adm_amount'				=> $this->input->post('savings_account_adm_amount', true),
				'savings_member_heir'						=> $this->input->post('savings_member_heir', true),
				'savings_member_heir_address'				=> $this->input->post('savings_member_heir_address', true),
				'savings_member_heir_relationship'			=> $this->input->post('savings_member_heir_relationship', true),
				'savings_account_token'						=> $this->input->post('savings_account_token', true),
				'operated_name'								=> $username,
				'created_id'								=> $auth['user_id'],
				'created_on'								=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('member_id', 'Anggota', 'required');
			$this->form_validation->set_rules('savings_id', 'Jenis Simpanan', 'required');
			$this->form_validation->set_rules('savings_account_no', 'No. Rek Simpanan', 'required');
			$this->form_validation->set_rules('savings_account_first_deposit_amount', 'Setoran', 'required');
			$this->form_validation->set_rules('savings_account_adm_amount', 'Biaya Adm', 'required');

			$savings_account_token 		= $this->AcctSavingsAccountUtility_model->getSavingsAccountToken($data['savings_account_token']);
			
			if($this->form_validation->run()==true){
				if($savings_account_token->num_rows() == 0){
					if($this->AcctSavingsAccountUtility_model->insertAcctSavingsAccountUtility($data)){
						
						// $auth = $this->session->userdata('auth');
						// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Rekening Simpanan Sukses
								</div> ";
						$sesi = $this->session->userdata('unique');
						$this->session->unset_userdata('addacctsavingsaccount-'.$sesi['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('savings-account-utility');
					}else{
						// $this->session->set_userdata('addacctsavingsaccount',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Rekening Simpanan Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('savings-account-utility');
					}
				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Rekening Sudah Ada
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('savings-account-utility');
				}
				
			}else{
				$this->session->set_userdata('addacctsavingsaccount',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-utility');
			}
		}

		public function printNoteAcctSavingsAccountUtility(){
			$auth = $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);
			$preferencecompany 	= $this->AcctSavingsAccountUtility_model->getPreferenceCompany();
			$acctsavingsaccount	= $this->AcctSavingsAccountUtility_model->getAcctSavingsAccountUtility_Detail($savings_account_id);


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
			        <td width=\"40%\"><div style=\"text-align: left; font-size:14px\">BUKTI SETORAN AWAL SIMPANAN</div></td>
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
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsaccount['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsaccount['savings_account_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsaccount['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($acctsavingsaccount['savings_account_first_deposit_amount'])."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: SETORAN AWAL SIMPANAN</div></td>
			    </tr>
			     <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsaccount['savings_account_first_deposit_amount'], 2)."</div></td>
			    </tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"20%\"><div style=\"text-align: center;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: center;\">".$this->AcctSavingsAccountUtility_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
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

		public function validationAcctSavingsAccountUtility(){
			$auth = $this->session->userdata('auth');
			$savings_account_id = $this->uri->segment(3);

			$data = array (
				'savings_account_id'  	=> $savings_account_id,
				'validation'			=> 1,
				'validation_id'			=> $auth['user_id'],
				'validation_on'			=> date('Y-m-d H:i:s'),
			);

			if($this->AcctSavingsAccountUtility_model->validationAcctSavingsAccountUtility($data)){
				$msg = "<div class='alert alert-success alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Rekening Sukses
						</div>";
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-utility/print-validation/'.$savings_account_id);
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'> 
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
							Validasi Rekening Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-utility');
			}
		}

		public function printValidationAcctSavingsAccountUtility(){
			$savings_account_id = $this->uri->segment(3);
			$acctsavingsaccount	= $this->AcctSavingsAccountUtility_model->getAcctSavingsAccountUtility_Detail($savings_account_id);


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

			$pdf->SetFont('helveticaI', '', 7);

			// -----------------------------------------------------------------------------

			$tbl = "
			<br><br><br><br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"55%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['savings_account_no']."</div></td>
			        <td width=\"40%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['member_name']."</div></td>
			        <td width=\"5%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['office_id']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"52%\"><div style=\"text-align: right; font-size:14px\">".$acctsavingsaccount['validation_on']."</div></td>
			        <td width=\"18%\"><div style=\"text-align: right; font-size:14px\">".$this->AcctSavingsAccountUtility_model->getUsername($acctsavingsaccount['validation_id'])."</div></td>
			        <td width=\"30%\"><div style=\"text-align: right; font-size:14px\"> IDR &nbsp; ".number_format($acctsavingsaccount['savings_account_first_deposit_amount'], 2)."</div></td>
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

		public function voidAcctSavingsAccountUtility(){
			$data['main_view']['membergender']				= $this->configuration->MemberGender();
			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();
			$data['main_view']['acctsavingsaccount']		= $this->AcctSavingsAccountUtility_model->getAcctSavingsAccountUtility_Detail($this->uri->segment(3));
			$data['main_view']['content']			= 'AcctSavingsAccountUtility/FormVoidAcctSavingsAccountUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processVoidAcctSavingsAccountUtility(){
			$auth	= $this->session->userdata('auth');

			$newdata = array (
				"savings_account_id"	=> $this->input->post('savings_account_id',true),
				"voided_on"				=> date('Y-m-d H:i:s'),
				'data_state'			=> 2,
				"voided_remark" 		=> $this->input->post('voided_remark',true),
				"voided_id"				=> $auth['user_id']
			);
			
			$this->form_validation->set_rules('voided_remark', 'Keterangan', 'required');

			if($this->form_validation->run()==true){
				if($this->AcctSavingsAccountUtility_model->voidAcctSavingsAccountUtility($newdata)){
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Sukses
							</div>";
					$this->session->set_userdata('message',$msg);
					redirect('savings-account-utility');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Pembatalan Rekening Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('savings-account-utility');
				}
					
			}else{
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('savings-account-utility');
			}
		}
		
		public function exportMasterDataAcctSavingsAccountUtility(){	
			$acctsavingsaccount	= $this->AcctSavingsAccountUtility_model->getExport();

			
			if($acctsavingsaccount->num_rows()!=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									 ->setLastModifiedBy("SIS")
									 ->setTitle("Master Data Simpanan")
									 ->setSubject("")
									 ->setDescription("Master Data Simpanan")
									 ->setKeywords("Master, Data, Simpanan")
									 ->setCategory("Master Data Simpanan");
									 
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(20);		

				
				$this->excel->getActiveSheet()->mergeCells("B1:H1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:H3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Simpanan");	
				
				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3',"Jenis Simpanan");
				$this->excel->getActiveSheet()->setCellValue('E3',"No. Rekening");
				$this->excel->getActiveSheet()->setCellValue('F3',"Tanggal Buka");
				$this->excel->getActiveSheet()->setCellValue('G3',"Setoran Awal");
				$this->excel->getActiveSheet()->setCellValue('H3',"Saldo");
				
				$j=4;
				$no=0;
				
				foreach($acctsavingsaccount->result_array() as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':H'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['savings_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['savings_account_no']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, tgltoview($val['savings_account_date']));
						$this->excel->getActiveSheet()->setCellValue('G'.$j, number_format($val['savings_account_first_deposit_amount'], 2));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, number_format($val['savings_account_last_balance'], 2));	
			
						
					}else{
						continue;
					}
					$j++;
				}
				$filename='Master Data Simpanan.xls';
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

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsaccount-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsaccount-'.$unique['unique'],$sessions);
		}	
		
	}
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreMemberMbayar extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMemberMbayar_model');
			$this->load->model('CoreMember_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['corebranch']		= create_double($this->CoreMemberMbayar_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'CoreMemberMbayar/ListCoreMemberMbayar_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-corememberpassword',$data);
			redirect('member-mbayar');
		}

		public function getCoreMemberMbayarList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-corememberpassword');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$list = $this->CoreMemberMbayar_model->get_datatables($sesi['branch_id']);

			// print_r($list);exit;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $customers) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $customers->member_no;
	            $row[] = $customers->member_name;
	            $row[] = $customers->member_address;
	            $row[] = $memberstatus[$customers->member_status];
	            $row[] = $membercharacter[$customers->member_character];
	            $row[] = $customers->member_phone;
	            $row[] = $customers->savings_account_no;
	            $row[] = '
	            	<a href="'.base_url().'member-mbayar/process-print-qrcode/'.$customers->savings_account_id.'" class="btn default btn-xs blue"><i class="fa fa-print"></i> Cetak Barcode</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMemberMbayar_model->count_all($sesi['branch_id']),
	                        "recordsFiltered" => $this->CoreMemberMbayar_model->count_filtered($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function addCoreMemberMbayar(){
			$member_id 				= $this->uri->segment(3);
			$savings_account_id 	= $this->uri->segment(4);

			$data['main_view']['coremember']			= $this->CoreMemberMbayar_model->getCoreMember_Detail($member_id);

			$data['main_view']['acctsavingsaccount']	= $this->CoreMemberMbayar_model->getAcctSavingsAccount_Detail($savings_account_id);

			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();	

			$data['main_view']['membergender']			= $this->configuration->MemberGender();	

			$data['main_view']['membercharacter']		= $this->configuration->MemberCharacter();

			$data['main_view']['content']				= 'CoreMemberMbayar/FormAddCoreMemberMbayar_view';

			$this->load->view('MainPage_view',$data);
		}

		public function reset_add(){
			$this->session->unset_userdata('filter-coremembermbayar');
			redirect('member-mbayar/add');
		}

		public function getListCoreMemberEdit(){
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
	            $row[] = '<a href="'.base_url().'member-mbayar/add/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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

		public function getListAcctSavingsAccount(){
			$auth 		= $this->session->userdata('auth');
			$member_id 	= $this->uri->segment(3);

			$list 		= $this->AcctSavingsAccount_model->get_datatables_mbayar($member_id);
	        $data 		= array();
	        $no 		= $_POST['start'];

	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'member-mbayar/add/'.$member_id.'/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all_mbayar($member_id),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered_mbayar($member_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}	

		public function processAddCoreMemberMbayar(){
			$auth = $this->session->userdata('auth');

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'savings_account_id'					=> $this->input->post('savings_account_id', true),				
			);

			// print_r($data);exit;


			$this->form_validation->set_rules('member_id', 'Anggota', 'required');
			$this->form_validation->set_rules('savings_account_id', 'Rekening', 'required');

			
			if($this->form_validation->run()==true){
				if($this->CoreMemberMbayar_model->updateCoreMember($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Simpan Rekening Mbayar Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->set_userdata('message',$msg);
					redirect('member-mbayar/process-print-qrcode/'.$data['savings_account_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Simpan Rekening Mbayar Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member-mbayar/add/'.$data['member_id']);
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member-mbayar/add/'.$data['member_id']);
			}				
		}

		public function processPrintingQRCode(){
			$auth 							= $this->session->userdata('auth');

			$savings_account_id				= $this->uri->segment(3);

			$acctsavingsaccount 			= $this->CoreMemberMbayar_model->getAcctSavingsAccount_Detail($savings_account_id);

			/*print_r("salesdeliverynoteitempiece ");
			print_r($salesdeliverynoteitempiece);
			*/
			require_once('phpqrcode/qrlib.php');
			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(P, PDF_UNIT, 'A7', true, 'UTF-8', false);
			// Check the example n. 29 for viewer preferences

			$tempdir = "temp/"; //<-- Nama Folder file QR Code kita nantinya akan disimpan
			if (!file_exists($tempdir))#kalau folder belum ada, maka buat.
			    mkdir($tempdir);

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

			$pdf->SetMargins(6, 6, 6, 6); // put space of 10 on top
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

			// -----------------------------------------------------------------------------

			$pdf->SetFont('helvetica', '', 8);

			$base_url = base_url();

			// $image = "<img alt=\"Coding Sips\" src=\"".$base_url."Barcode/barcode.php?text=asdasdasdwead1232132423432432423b&print=true\" />";

			$isi_teks = $acctsavingsaccount['savings_account_id'];
			$namafile = $acctsavingsaccount['savings_account_no'].".png";
			$quality = 'H'; //ada 4 pilihan, L (Low), M(Medium), Q(Good), H(High)
			$ukuran = 10; //batasan 1 paling kecil, 10 paling besar
			$padding = 0;
			 
			QRCode::png($isi_teks,$tempdir.$namafile,$quality,$ukuran,$padding);
 
 			$image = "<img src=\"".$base_url."temp/".$namafile."\">";
 
 			

 			$tbl1 = "
 				<table id=\"items\" width=\"100%\" cellspacing=\"1\" cellpadding=\"3\" border=\"1\">
					<tr>
						<td width=\"100%\">".$image."</td>
					</tr>
				</table>
 			";

 			$pdf->writeHTML($tbl1, true, false, false, false, '');

			// print_r("base_url ");
			// print_r($base_url);
			// print_r("<BR> ");

			// print_r("image ");
			// print_r($image);
			// print_r("<BR> ");

			// exit;
			
			//Close and output PDF document
			ob_clean();
			$filename = 'QR_Code_'.$acctsavingsaccount['savings_account_no'].'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
			
		}			
	}
?>
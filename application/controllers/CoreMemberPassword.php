<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreMemberPassword extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMemberPassword_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['corebranch']		= create_double($this->CoreMemberPassword_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'CoreMemberPassword/ListCoreMemberPassword_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-corememberpassword',$data);
			redirect('CoreMemberPassword');
		}

		public function getCoreMemberPasswordList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-corememberpassword');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$list = $this->CoreMemberPassword_model->get_datatables($sesi['branch_id']);

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
	            $row[] = $customers->member_password_default;
	            $row[] = '
	            	<a href="'.base_url().'CoreMemberPassword/processPrinting/'.$customers->member_id.'" class="btn default btn-xs blue"><i class="fa fa-print"></i> Cetak Password</a>
	            	<a href="'.base_url().'CoreMemberPassword/processResetCoreMemberPassword/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon", onClick="javascript:return confirm(\'apakah yakin ingin mereset password  ?\')" role="button"><i class="fa fa-edit"></i> Reset Password</a>';
	            $data[] = $row;
	        }
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->CoreMemberPassword_model->count_all($sesi['branch_id']),
	                        "recordsFiltered" => $this->CoreMemberPassword_model->count_filtered($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function processResetCoreMemberPassword(){
			$this->load->model('CoreMemberPassword_model');
			$auth = $this->session->userdata('auth');
			$member_id 	= $this->uri->segment(3);

			$member = $this->CoreMemberPassword_model->getCoreMember_Detail($member_id);

			// print_r('member');
			// print_r($member);exit;

			$date = date('dmYHis');
			$data = array(
					'member_id'				=> $member_id,
					'member_password' 		=> md5($member['member_password_default']),
					'last_update'			=> date('Y-m-d H:i:s'),
					
			);
			
			// print_r('data');
			// print_r($data);exit;
			$this->form_validation->set_rules('member_password', 'Password', 'required');
			
			
				if($this->CoreMemberPassword_model->updateCoreMemberPassword($data)==true){
					$auth 	= $this->session->userdata('auth');

					$this->fungsi->set_log($auth['user_id'], $auth['username'], '3123','Application.CoreMember.processResetCoreMemberPassword', $data['member_id'],'Edit Invt Item Category');

					$this->fungsi->set_change_log($old_data, $data, $auth['user_id'], $data['member_id']);

					$msg = "<div class='alert alert-success alert-dismissable'>                  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Reset Password Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreMemberPassword');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>    
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Reset Password Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('CoreMemberPassword');
				}
			}
				
			// }else{
			// 	$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>    
			// 						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
			// 	$this->session->set_userdata('message',$msg);
			// 	redirect('CoreMemberPassword']);
			// }

				

		public function processPrinting(){
			$auth 						= $this->session->userdata('auth');
			$member_id 					= $this->uri->segment(3);
			$coremember					= $this->CoreMemberPassword_model->getCoreMember_Detail($member_id);
			$preferencecompany 			= $this->CoreMemberPassword_model->getPreferenceCompany();


			// print_r($keterangan);

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
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"500%\" height=\"200%\"/>";

			// print_r($preferencecompany['logo_koperasi']);exit;

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			    	<td rowspan=\"2\" width=\"15%\">".$img."</td>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:12px\">BUKTI CETAK PASSWORD ANGGOTA</div></td>
			    </tr>
			    <tr>
			        <td width=\"40%\"><div style=\"text-align: left; font-size:12px\">Jam : ".date('H:i:s')."</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = 

			"<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$coremember['member_name']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">No. Anggota</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$coremember['member_no']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$coremember['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"20%\"><div style=\"text-align: left;\">Password</div></td>
			        <td width=\"80%\"><div style=\"text-align: left;\">: ".$coremember['member_password_default']."</div></td>
			    </tr>		
			</table>";

			$pdf->writeHTML($tbl1, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Password_Anggota_'.$coremember['member_name'].'.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}


		
	}
?>
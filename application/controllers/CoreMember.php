<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreMember extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreMember_model');
			$this->load->model('Library_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addCoreMember-'.$unique['unique']);	
			$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
			$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
			
			$export_menu_id 					= $this->Library_model->getIDMenu('member/export-master-data');
			$export_menu_id_mapping 			= $this->Library_model->getIDMenuOnSystemMapping($export_menu_id);

			$data['main_view']['export_menu_id_mapping']	= $export_menu_id_mapping;
			$data['main_view']['corebranch']				= create_double($this->CoreMember_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['corecompany']				= create_double($this->CoreMember_model->getCorecompanyNoDateState(),'company_id','company_name');
			$data['main_view']['content']					= 'CoreMember/ListCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"company_id" 	=> $this->input->post('company_id',true),
			);

			$this->session->set_userdata('filter-coremember',$data);
			redirect('member');
		}

		public function getCoreMemberList(){
			$auth 		= $this->session->userdata('auth');

			
			$sesi	= 	$this->session->userdata('filter-coremember');
			if(!is_array($sesi)){
				$sesi['company_id']		= '';
			}

			$detail_menu_id 				= $this->Library_model->getIDMenu('member/detail');
			$detail_menu_id_mapping 		= $this->Library_model->getIDMenuOnSystemMapping($detail_menu_id);
			
			$edit_menu_id 					= $this->Library_model->getIDMenu('member/edit');
			$edit_menu_id_mapping 			= $this->Library_model->getIDMenuOnSystemMapping($edit_menu_id);
			
			$delete_menu_id 				= $this->Library_model->getIDMenu('member/delete');
			$delete_menu_id_mapping 		= $this->Library_model->getIDMenuOnSystemMapping($delete_menu_id);
			
			$activate_id 					= $this->Library_model->getIDMenu('member/activate');
			$activate_id_mapping 			= $this->Library_model->getIDMenuOnSystemMapping($activate_id);

			$list 							= $this->CoreMember_model->get_datatables($sesi['branch_id']);
			$company 						= $this->CoreMember_model->getCoreCompany();

			$memberstatus					= $this->configuration->MemberStatus();	
			$data 							= array();
			$no 							= $_POST['start'];
			foreach ($list as $customers) {
				$button_edit 						= '<a href="'.base_url().'member/edit/'.$customers->member_id.'" class="btn default btn-xs blue"><i class="fa fa-edit"></i> Edit</a>';

				$button_detail 						= '<a href="'.base_url().'member/detail/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon"><i class="fa fa-bars"></i> Detail</a>';

				$button_delete 						= '<a href="'.base_url().'member/delete/'.$customers->member_id.'" class="btn default btn-xs red", onClick="javascript:return confirm(\'apakah yakin ingin dihapus ?\')" role="button"><i class="fa fa-trash"></i> Hapus</a>';

				$button_activate 					= '<a href="'.base_url().'member/activate/'.$customers->member_id.'" class="btn default btn-xs green-jungle", onClick="javascript:return confirm(\'apakah yakin ingin diaktifkan ?\')" role="button"><i class="fa fa-check"></i> Aktifkan</a>';

				$button_non_activate 				= '<a href="'.base_url().'member/non-activate/'.$customers->member_id.'" class="btn default btn-xs purple", onClick="javascript:return confirm(\'apakah yakin ingin dinon-aktifkan ?\')" role="button"><i class="fa fa-times"></i> Non Aktifkan</a>';


				$button ='';

				if($detail_menu_id_mapping == 1){
					$button .= $button_detail;
				} 

				if ($edit_menu_id_mapping == 1){
					$button .= $button_edit;
				} 

				if ($activate_id_mapping == 1){
					if($customers->member_active_status == 1){
						$button .= $button_activate;
					}else{
						$button .= $button_non_activate;
					}
				} 

				if ($delete_menu_id_mapping == 1){
					$button .= $button_delete;
				} 
				if($customers->company_id == 0){
					$customers_company_name = 'Tidak Ada';
				}
				foreach($company as $company_item){
					if($customers->company_id == $company_item['company_id']){
						$customers_company_name = $company_item['company_name'];
					}
				}

				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = $memberstatus[$customers->member_status];
				$row[] = $customers->member_phone;
				$row[] = number_format($customers->member_principal_savings_last_balance, 2);
				$row[] = number_format($customers->member_special_savings_last_balance, 2);
				$row[] = number_format($customers->member_mandatory_savings_last_balance, 2);
				$row[] = $button;
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all($sesi['branch_id']),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($sesi['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function activateCoreMember(){
			if($this->CoreMember_model->activateCoreMember($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1005','Application.CoreMember.deleteCoreMember',$auth['user_id'],'Delete Member');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Aktivasi Data Anggota Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Aktivasi Data Anggota Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member');
			}
		}

		public function nonActivateCoreMember(){
			$savingsaccount 	= $this->CoreMember_model->getAcctSavingsAccount2($this->uri->segment(3));
			$depositoaccount 	= $this->CoreMember_model->getAcctDepositoAccount($this->uri->segment(3));
			$creditsaccount 	= $this->CoreMember_model->getAcctCreditsAccount($this->uri->segment(3));
			foreach($savingsaccount as $item){
				if($item['savings_account_last_balance'] > 0){
					$msg = "<div class='alert alert-danger alert-dismissable'>                
								Non Aktivasi Data Anggota Tidak Berhasil, Anggota Masih Memiliki Saldo Tabungan!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member');
				}
			}
			foreach($depositoaccount as $item){
				if($item['deposito_account_status'] == 0 || $item['deposito_account_closed_date'] == null){
					$msg = "<div class='alert alert-danger alert-dismissable'>                
								Non Aktivasi Data Anggota Tidak Berhasil, Anggota Masih Memiliki Tabungan Berjangka!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member');
				}
			}
			foreach($creditsaccount as $item){
				if($item['credits_account_status'] == 0){
					$msg = "<div class='alert alert-danger alert-dismissable'>                
								Non Aktivasi Data Anggota Tidak Berhasil, Anggota Masih Memiliki Pinjaman!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member');
				}
			}
			if($this->CoreMember_model->nonActivateCoreMember($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1005','Application.CoreMember.deleteCoreMember',$auth['user_id'],'Delete Member');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Non Aktivasi Data Anggota Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Non Aktivasi Data Anggota Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member');
			}
		}

		public function getMasterDataCoreMember(){
			$auth = $this->session->userdata('auth');	

			$data['main_view']['corebranch']		= create_double($this->CoreMember_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'CoreMember/ListMasterDataCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterMasterData(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-coremembermasterdata',$data);
			redirect('member/get-member');
		}

		public function getMemberNameAndPlaceOfBirth(){
			$member_name 				= $this->input->post('member_name', true);
			$member_place_of_birth		= $this->input->post('member_place_of_birth', true);
			$member_date_of_birth		= $this->input->post('member_date_of_birth', true);
			
			$core_member 			= $this->CoreMember_model->getCoreMemberNameandPlaceOfBirth($member_name, $member_place_of_birth, $member_date_of_birth);
			$output = array(
				"message" => 'success',
				"data" => $core_member,
			);
			echo json_encode($output);
		}

		public function getMasterDataCoreMemberList(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$sesi	= 	$this->session->userdata('filter-coremembermasterdata');
				if(!is_array($sesi)){
					$sesi['branch_id']		= '';
				}
			} else {
				$sesi['branch_id']	= $auth['branch_id'];
			}

			$list = $this->CoreMember_model->get_datatables($sesi['branch_id']);

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
				$row[] = number_format($customers->member_principal_savings_last_balance, 2);
				$row[] = number_format($customers->member_special_savings_last_balance, 2);
				$row[] = number_format($customers->member_mandatory_savings_last_balance, 2);
				$row[] = '
					
					<a href="'.base_url().'member/detail/'.$customers->member_id.'" class="btn default btn-xs yellow-lemon"><i class="fa fa-bars"></i> Detail</a>';
				$data[] = $row;
			}
	
			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->CoreMember_model->count_all($sesi['branch_id']),
							"recordsFiltered" => $this->CoreMember_model->count_filtered($sesi['branch_id']),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		public function showdetail(){
			$member_id 	= $this->uri->segment(3);

			$data['main_view']['coremember']				= $this->CoreMember_model->getCoreMember_Detail($member_id);
			$data['main_view']['acctsavingsaccount']		= $this->CoreMember_model->getAcctSavingsAccount_Member($member_id);
			$data['main_view']['acctcreditsaccount']		= $this->CoreMember_model->getAcctCreditsAccount_Member($member_id);

			$data['main_view']['memberidentity']			= $this->configuration->MemberIdentity();	
			$data['main_view']['membergender']				= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']			= $this->configuration->MemberCharacter();	

			$data['main_view']['content']					= 'CoreMember/FormDetailCoreMember_view';

			$this->load->view('MainPage_view',$data);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addCoreMember-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addCoreMember-'.$unique['unique'],$sessions);
		}

		public function reset_add(){
			$unique 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addCoreMember-'.$unique['unique']);
			redirect('member/add');
		}
		
		public function addCoreMember(){
			$unique = $this->session->userdata('unique');
			$auth 	= $this->session->userdata('auth');
			$token 	= $this->session->userdata('coremembertoken-'.$unique['unique']);

			if(empty($token)){
				$member_token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('coremembertoken-'.$unique['unique'], $member_token);
			}

			$branchcode = $this->CoreMember_model->getBranchCode($auth['branch_id']);
			
			$last_member_no = $this->CoreMember_model->getLastMemberNo($auth['branch_id']);
			
			if($last_member_no->num_rows() <> 0){      
			//jika kode ternyata sudah ada.      
			$data = $last_member_no->row_array();    			   			   
			$kode = intval($data['last_member_no']) + 1;    
			} else {      
			//jika kode belum ada      
			$kode = 1;    
			}

			$kodemax 		= str_pad($kode, 6, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0
			$new_member_no 	= $branchcode.$kodemax;    // hasilnya ODJ-9921-0001 dst.

			$data['main_view']['coreprovince']			= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');
			$data['main_view']['coreidentity']			= create_double($this->CoreMember_model->getCoreIdentity(),'identity_id', 'identity_name');
			$data['main_view']['corememberclass']		= create_double($this->CoreMember_model->getCoreMemberClass(),'member_class_id', 'member_class_name');
			$data['main_view']['corecompany']			= create_double($this->CoreMember_model->getCoreCompany(),'company_id', 'company_name');
			$data['main_view']['preferencecompany']		= $this->CoreMember_model->getPreferenceCompany();
			$data['main_view']['membergender']			= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']		= $this->configuration->MemberCharacter();	
			$data['main_view']['memberidentity']		= $this->configuration->MemberIdentity();
			$data['main_view']['maritalstatus']			= $this->configuration->MaritalStatus();	
			$data['main_view']['homestatus']			= $this->configuration->HomeStatus();	
			$data['main_view']['membervehicle']			= $this->configuration->Vehicle();
			$data['main_view']['lasteducation']			= $this->configuration->LastEducation();	
			$data['main_view']['unituser']				= $this->configuration->UnitUser();	
			$data['main_view']['workingtype']			= $this->configuration->WorkingType();	
			$data['main_view']['businessscale']			= $this->configuration->BusinessScale();	
			$data['main_view']['businessowner']			= $this->configuration->BusinessOwner();
			$data['main_view']['familyrelationship']	= $this->configuration->FamilyRelationship();
			$data['main_view']['new_member_no']			= $new_member_no;
			$data['main_view']['content']				= 'CoreMember/FormAddCoreMemberUi_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getCoreCity(){
			$province_id 		= $this->uri->segment(3);
			
			$item = $this->CoreMember_model->getCoreCity($province_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['city_id']	= $mp['city_id'];
				$jsond[$i]['city_name']	= $mp['city_name'];
				$i++;
			}
			echo json_encode($jsond);
		}

		public function getCoreKecamatan(){
			$city_id 		= $this->uri->segment(3);
			
			$item = $this->CoreMember_model->getCoreKecamatan($city_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['kecamatan_id']		= $mp['kecamatan_id'];
				$jsond[$i]['kecamatan_name'] 	= $mp['kecamatan_name'];
			$i++;
			}
			echo json_encode($jsond);
		}

		public function getCoreKelurahan(){
			$kecamatan_id 		= $this->uri->segment(3);
			
			$item = $this->CoreMember_model->getCoreKelurahan($kecamatan_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['kelurahan_id']		= $mp['kelurahan_id'];
				$jsond[$i]['kelurahan_name']	= $mp['kelurahan_name'];
				$i++;
			}
			echo json_encode($jsond);
		}

		public function getCoreDusun(){
			$kelurahan_id 		= $this->uri->segment(3);
			
			$item = $this->CoreMember_model->getCoreDusun($kelurahan_id);
			$data = "<option value=''>--Pilih Salah Satu--</option>";
			$jsond=array();
			$i=0;
			foreach ($item as $mp){
				$jsond[$i]['dusun_id']		= $mp['dusun_id'];
				$jsond[$i]['dusun_name']	= $mp['dusun_name'];
				$i++;
			}
			echo json_encode($jsond);
		}
		
		public function processAddCoreMember(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$member_password = rand();

			if($this->input->post('member_class_mandatory_savings', true)){
				$member_class_mandatory_savings = $this->input->post('member_class_mandatory_savings', true);
			}else{
				$member_class_mandatory_savings = 0;
			}

			if($this->input->post('member_company_mandatory_savings', true)){
				$member_company_mandatory_savings = $this->input->post('member_company_mandatory_savings', true);
			}else{
				$member_company_mandatory_savings = 0;
			}

			$member_mandatory_savings = 10000.00;
			$member_principal_savings = $this->input->post('member_principal_savings', true);
			$data = array(
				'branch_id'										=> $auth['branch_id'],
				'member_no'										=> $this->input->post('member_no', true),
				'member_name'									=> $this->input->post('member_name', true),
				'member_nick_name'								=> $this->input->post('member_nick_name', true),
				'member_gender'									=> $this->input->post('member_gender', true),
				'province_id'									=> $this->input->post('province_id', true),
				'city_id'										=> $this->input->post('city_id', true),
				'kecamatan_id'									=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'									=> $this->input->post('kelurahan_id', true),
				'company_id'									=> $this->input->post('company_id', true),
				'member_job'									=> $this->input->post('member_job', true),
				'member_identity'								=> $this->input->post('member_identity', true),
				'member_place_of_birth'							=> $this->input->post('member_place_of_birth', true),
				'member_date_of_birth'							=> tgltodb($this->input->post('member_date_of_birth', true)),
				'member_address'								=> $this->input->post('member_address', true),
				'member_identity_no'							=> $this->input->post('member_identity_no', true), 
				'member_partner_identity_no'					=> $this->input->post('member_partner_identity_no', true),
				'member_marital_status'							=> $this->input->post('member_marital_status', true),
				'member_heir'									=> $this->input->post('member_heir', true),
				'member_heir_mobile_phone'						=> $this->input->post('member_heir_mobile_phone', true),
				'member_heir_relationship'						=> $this->input->post('member_heir_relationship', true),
				'member_postal_code'							=> $this->input->post('member_postal_code', true),
				'member_mother'									=> $this->input->post('member_mother', true),
				'member_token'									=> $this->input->post('member_token', true),
				'member_address_now'							=> $this->input->post('member_address_now', true),
				'member_phone'									=> $this->input->post('member_phone', true),
				'member_dependent'								=> $this->input->post('member_dependent', true),
				'member_home_status'							=> $this->input->post('member_home_status', true),
				'member_long_stay'								=> $this->input->post('member_long_stay', true),
				'member_vehicle'								=> $this->input->post('member_vehicle', true),
				'member_last_education'							=> $this->input->post('member_last_education', true),
				'member_unit_user'								=> $this->input->post('member_unit_user', true),
				'member_partner_name'							=> $this->input->post('member_partner_name', true),
				'member_email'									=> $this->input->post('member_email', true),
				'member_class_id'								=> $this->input->post('member_class_id', true),
				'member_class_mandatory_savings'				=> $member_class_mandatory_savings,
				'member_company_mandatory_savings'				=> $member_company_mandatory_savings,
				'member_mandatory_savings'						=> $member_mandatory_savings,
				'member_status'									=> 0, //langsung bisa buka pinjaman (1) / (0) perlu update
				'member_password_default'						=> $member_password,
				'member_password'								=> md5($member_password),
				'member_register_date'							=> date('Y-m-d H:i:s'),
				'created_id'									=> $auth['user_id'],
				'created_on'									=> date('Y-m-d H:i:s'),
				'member_principal_savings'						=> $member_principal_savings,
				//!komen dibawah untuk yg tambah anggota lgsg bayar simp pokok
				// 'member_principal_savings_last_balance'		=> $member_principal_savings,
			);

			if($member_mandatory_savings <= 0){
				$this->session->set_userdata('addcoremember',$data);
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Simpanan Wajib Harus Diisi Salah Satu !
						</div> ";
				$this->session->set_userdata('message_check',0);
						$this->session->set_userdata('message',$msg);
				redirect('member/add');
			}

			// print_r($data);exit;
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_gender', 'Jenis Kelamin', 'required');
			$this->form_validation->set_rules('member_place_of_birth', 'Tempat Lahir', 'required');
			$this->form_validation->set_rules('member_date_of_birth', 'Tanggal Lahir', 'required');
			$this->form_validation->set_rules('province_id', 'Provinsi', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('member_address_now', 'Alamat Sekarang', 'required');
			$this->form_validation->set_rules('member_mother', 'Nama Ibu ', 'required');


			$membertoken = $this->CoreMember_model->getMemberToken($data['member_token']);

			
			if($this->form_validation->run()==true){
				if($membertoken->num_rows() == 0){
					if($this->CoreMember_model->insertCoreMember($data)){
						$member_id 		= $this->CoreMember_model->getMemberID($data['created_id']);

						$dataworking 	= array (
							'member_id'									=> $member_id,
							'member_working_type'						=> $this->input->post('member_working_type', true),
							'member_company_name'						=> $this->input->post('member_company_name', true),
							'member_company_address'					=> $this->input->post('member_company_address', true),
							'member_company_specialities'				=> $this->input->post('member_company_specialites', true),
							'member_company_job_title'					=> $this->input->post('member_company_job_title', true),
							'member_company_city'						=> $this->input->post('member_company_city', true),
							'member_company_period'						=> $this->input->post('member_company_period', true),
							'member_company_postal_code'				=> $this->input->post('member_company_postal_code', true),
							'member_company_phone'						=> $this->input->post('member_company_phone', true),
							'member_business_name'						=> $this->input->post('member_business_name', true),
							'member_business_scale'						=> $this->input->post('member_business_scale', true),
							'member_business_period'					=> $this->input->post('member_business_period', true),
							'member_business_address'					=> $this->input->post('member_business_address', true),
							'member_business_city'						=> $this->input->post('member_business_city', true),
							'member_business_phone'						=> $this->input->post('member_business_phone', true),
							'member_business_postal_code'				=> $this->input->post('member_business_postal_code', true),
							'member_monthly_income'						=> $this->input->post('member_monthly_income', true),
							'partner_working_type'						=> $this->input->post('partner_working_type', true),
							'partner_company_name'						=> $this->input->post('partner_company_name', true),
							'partner_company_address'					=> $this->input->post('partner_company_address', true),
							'partner_company_specialities'				=> $this->input->post('partner_company_specialities', true),
							'partner_company_job_title'					=> $this->input->post('partner_company_job_title', true),
							'partner_company_phone'						=> $this->input->post('partner_company_phone', true),
							'partner_business_name'						=> $this->input->post('partner_business_name', true),
							'partner_business_scale'					=> $this->input->post('partner_business_scale', true),
							'partner_business_period'					=> $this->input->post('partner_business_period', true),
							'partner_business_owner'					=> $this->input->post('partner_business_owner', true),
						);

						$this->CoreMember_model->insertCoreMemberWorking($dataworking);
						$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

						//!komen dibawah untuk yg tambah anggota lgsg bayar simp pokok
						// $data_detail = array (
						// 	'branch_id'						=> $auth['branch_id'],
						// 	'member_id'						=> $data['member_id'],
						// 	'mutation_id'					=> $preferencecompany['cash_deposit_id'],
						// 	'transaction_date'				=> date('Y-m-d'),
						// 	'principal_savings_amount'		=> $data['member_principal_savings'],
						// 	'operated_name'					=> $auth['username'],
						// 	'savings_member_detail_token'	=> $data['member_token'].'ADDMEMBER'.$auth['branch_id'],
						// );

						// if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){
						// 	if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){

						// 		$transaction_module_code 	= "AGT";

						// 		$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
						// 		$coremember 				= $this->CoreMember_model->getCoreMember_Detail($dataworking['member_id']);
						// 		$journal_voucher_period 	= date("Ym", strtotime($coremember['member_register_date']));

						// 		//-------------------------Jurnal Cabang----------------------------------------------------
								
						// 		$data_journal_cabang = array(
						// 			'branch_id'						=> $auth['branch_id'],
						// 			'journal_voucher_period' 		=> $journal_voucher_period,
						// 			'journal_voucher_date'			=> date('Y-m-d'),
						// 			'journal_voucher_title'			=> 'SIMPANAN POKOK MEMBER BARU TUNAI '.$coremember['member_name'],
						// 			'journal_voucher_description'	=> 'SIMPANAN POKOK MEMBER BARU TUNAI '.$coremember['member_name'],
						// 			'journal_voucher_token'			=> $data['member_token'].$auth['branch_id'],
						// 			'transaction_module_id'			=> $transaction_module_id,
						// 			'transaction_module_code'		=> $transaction_module_code,
						// 			'transaction_journal_id' 		=> $coremember['member_id'],
						// 			'transaction_journal_no' 		=> $coremember['member_no'],
						// 			'created_id' 					=> $auth['user_id'],
						// 			'created_on' 					=> date('Y-m-d H:i:s'),
						// 		);
								
						// 		$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);

						// 		$journal_voucher_id 			= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						// 		$preferencecompany 				= $this->CoreMember_model->getPreferenceCompany();


						// 		$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

						// 		$data_debet = array (
						// 			'journal_voucher_id'			=> $journal_voucher_id,
						// 			'account_id'					=> $preferencecompany['account_cash_id'],
						// 			'journal_voucher_description'	=> 'SETORAN TUNAI SIMP POKOK '.$coremember['member_name'],
						// 			'journal_voucher_amount'		=> $data['member_principal_savings'],
						// 			'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
						// 			'account_id_default_status'		=> $account_id_default_status,
						// 			'account_id_status'				=> 0,
						// 			'created_id' 					=> $auth['user_id'],
						// 			'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
						// 		);

						// 		$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

						// 		$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

						// 		$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

						// 		$data_credit =array(
						// 			'journal_voucher_id'			=> $journal_voucher_id,
						// 			'account_id'					=> $account_id,
						// 			'journal_voucher_description'	=> 'SETORAN TUNAI SIMP POKOK '.$coremember['member_name'],
						// 			'journal_voucher_amount'		=> $data['member_principal_savings'],
						// 			'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
						// 			'account_id_default_status'		=> $account_id_default_status,
						// 			'account_id_status'				=> 1,
						// 			'created_id' 					=> $auth['user_id'],
						// 			'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
						// 		);

						// 		$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
						// 	}
						// }

						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.CoreMember.processAddCoreMember',$auth['user_id'],'Add New Member');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Sukses
								</div> ";

						$unique 	= $this->session->userdata('unique');
						$this->session->unset_userdata('addCoreMember-'.$unique['unique']);
						$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
						$this->session->set_userdata('message_check',0);
						$this->session->set_userdata('message',$msg);
						redirect('member/add');
					}else{
						$this->session->set_userdata('addcoremember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message_check',0);
								$this->session->set_userdata('message',$msg);
						redirect('member/add');
					}
				} else {
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Anggota Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message_check',0);
							$this->session->set_userdata('message',$msg);
					redirect('member/add');
				}
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message_check',0);
				$this->session->set_userdata('message',$msg);
				redirect('member/add');
			}
	}

		public function editCoreMember(){
			$member_id 	= $this->uri->segment(3);

			$data['main_view']['coremember']		= $this->CoreMember_model->getCoreMember_Detail($member_id);
			// print_r( $this->CoreMember_model->getCoreMember_Detail($member_id));exit;

			$data['main_view']['corememberworking']	= $this->CoreMember_model->getCoreMemberWorking_Detail($member_id);

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');

			$data['main_view']['acctmutation']		= create_double($this->CoreMember_model->getAcctMutation(),'mutation_id', 'mutation_name');

			$data['main_view']['corememberclass']	= create_double($this->CoreMember_model->getCoreMemberClass(),'member_class_id', 'member_class_name');

			$data['main_view']['corecompany']		= create_double($this->CoreMember_model->getCoreCompany(),'company_id', 'company_name');
			$data['main_view']['acctsavingsaccount']= create_double($this->CoreMember_model->getAcctSavingsAccount_Member($member_id),'savings_account_id', 'savings_account_no');
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();
			$data['main_view']['maritalstatus']		= $this->configuration->MaritalStatus();	
			$data['main_view']['homestatus']		= $this->configuration->HomeStatus();	
			$data['main_view']['membervehicle']		= $this->configuration->Vehicle();
			$data['main_view']['lasteducation']		= $this->configuration->LastEducation();	
			$data['main_view']['unituser']			= $this->configuration->UnitUser();	
			$data['main_view']['workingtype']		= $this->configuration->WorkingType();	
			$data['main_view']['businessscale']		= $this->configuration->BusinessScale();	
			$data['main_view']['businessowner']		= $this->configuration->BusinessOwner();	
			$data['main_view']['paymentpreference']	= $this->configuration->PaymentPreference();	
			$data['main_view']['familyrelationship']= $this->configuration->FamilyRelationship();

			$data['main_view']['content']			= 'CoreMember/FormEditCoreMember_view';

			$this->load->view('MainPage_view',$data);
		}

		public function processEditCoreMember(){
			$auth = $this->session->userdata('auth');

			if($this->input->post('member_class_mandatory_savings', true)){
				$member_class_mandatory_savings = $this->input->post('member_class_mandatory_savings', true);
			}else{
				$member_class_mandatory_savings = 0;
			}

			if($this->input->post('member_company_mandatory_savings', true)){
				$member_company_mandatory_savings = $this->input->post('member_company_mandatory_savings', true);
			}else{
				$member_company_mandatory_savings = 0;
			}

			$member_mandatory_savings = $member_class_mandatory_savings + $member_company_mandatory_savings;

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'member_no'								=> $this->input->post('member_no', true),
				'member_name'							=> $this->input->post('member_name', true),
				'member_nick_name'						=> $this->input->post('member_nick_name', true),
				'member_gender'							=> $this->input->post('member_gender', true),
				'province_id'							=> $this->input->post('province_id', true),
				'city_id'								=> $this->input->post('city_id', true),
				'kecamatan_id'							=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'							=> $this->input->post('kelurahan_id', true),
				'member_job'							=> $this->input->post('member_job', true),
				'member_identity'						=> $this->input->post('member_identity', true),
				'member_place_of_birth'					=> $this->input->post('member_place_of_birth', true),
				'member_date_of_birth'					=> tgltodb($this->input->post('member_date_of_birth', true)),
				'member_address'						=> $this->input->post('member_address', true),
				'member_phone'							=> $this->input->post('member_phone', true),
				'member_identity_no'					=> $this->input->post('member_identity_no', true),
				'member_partner_identity_no'			=> $this->input->post('member_partner_identity_no', true),
				'member_marital_status'					=> $this->input->post('member_marital_status', true),
				'member_heir'							=> $this->input->post('member_heir', true),
				'member_heir_mobile_phone'				=> $this->input->post('member_heir_mobile_phone', true),
				'member_heir_relationship'				=> $this->input->post('member_heir_relationship', true),
				'member_postal_code'					=> $this->input->post('member_postal_code', true),
				'member_mother'							=> $this->input->post('member_mother', true),
				'member_token'							=> $this->input->post('member_token', true),
				'member_address_now'					=> $this->input->post('member_address_now', true),
				'member_home_phone'						=> $this->input->post('member_home_phone', true),
				'member_dependent'						=> $this->input->post('member_dependent', true),
				'member_home_status'					=> $this->input->post('member_home_status', true),
				'member_long_stay'						=> $this->input->post('member_long_stay', true),
				'member_vehicle'						=> $this->input->post('member_vehicle', true),
				'member_last_education'					=> $this->input->post('member_last_education', true),
				'member_unit_user'						=> $this->input->post('member_unit_user', true),
				'member_partner_name'					=> $this->input->post('member_partner_name', true),
				'member_partner_place_of_birth'			=> $this->input->post('member_partner_place_of_birth', true),
				'member_partner_date_of_birth'			=> tgltodb($this->input->post('member_partner_date_of_birth', true)),
				'member_email'							=> $this->input->post('member_email', true),
				'member_principal_savings'				=> $this->input->post('member_principal_savings', true),
				'company_id'							=> $this->input->post('company_id', true),
				'member_class_id'						=> $this->input->post('member_class_id', true),
				'member_class_mandatory_savings'		=> $member_class_mandatory_savings,
				'member_company_mandatory_savings'		=> $member_company_mandatory_savings,
				'member_mandatory_savings'				=> $this->input->post('member_mandatory_savings', true),
				'member_debet_preference'				=> $this->input->post('member_debet_preference', true),
				'member_debet_savings_account_id'		=> $this->input->post('member_debet_savings_account_id', true),
			);
			// print_r($data);exit;

			// if($member_mandatory_savings <= 0){
			// 	$msg = "<div class='alert alert-danger alert-dismissable'> 
			// 			<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
			// 				Simpanan Wajib Harus Diisi Salah Satu !
			// 			</div> ";
			// 	$this->session->set_userdata('message',$msg);
			// 	redirect('member/edit/'.$data['member_id']);
			// }

			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');

			
			if($this->form_validation->run()==true){
				if($this->CoreMember_model->updateCoreMember($data)){
					$dataworking 	= array (
						'member_id'									=> $this->input->post('member_id', true),
						'member_working_type'						=> $this->input->post('member_working_type', true),
						'member_company_name'						=> $this->input->post('member_company_name', true),
						'member_company_address'					=> $this->input->post('member_company_address', true),
						'member_company_specialities'				=> $this->input->post('member_company_specialites', true),
						'member_company_job_title'					=> $this->input->post('member_company_job_title', true),
						'member_company_city'						=> $this->input->post('member_company_city', true),
						'member_company_period'						=> $this->input->post('member_company_period', true),
						'member_company_postal_code'				=> $this->input->post('member_company_postal_code', true),
						'member_company_phone'						=> $this->input->post('member_company_phone', true),
						'member_business_name'						=> $this->input->post('member_business_name', true),
						'member_business_scale'						=> $this->input->post('member_business_scale', true),
						'member_business_period'					=> $this->input->post('member_business_period', true),
						'member_business_address'					=> $this->input->post('member_business_address', true),
						'member_business_city'						=> $this->input->post('member_business_city', true),
						'member_business_phone'						=> $this->input->post('member_business_phone', true),
						'member_business_postal_code'				=> $this->input->post('member_business_postal_code', true),
						'member_monthly_income'						=> $this->input->post('member_monthly_income', true),
						'partner_working_type'						=> $this->input->post('partner_working_type', true),
						'partner_company_name'						=> $this->input->post('partner_company_name', true),
						'partner_company_address'					=> $this->input->post('partner_company_address', true),
						'partner_company_specialities'				=> $this->input->post('partner_company_specialities', true),
						'partner_company_job_title'					=> $this->input->post('partner_company_job_title', true),
						'partner_company_phone'						=> $this->input->post('partner_company_phone', true),
						'partner_business_name'						=> $this->input->post('partner_business_name', true),
						'partner_business_scale'					=> $this->input->post('partner_business_scale', true),
						'partner_business_period'					=> $this->input->post('partner_business_period', true),
						'partner_business_owner'					=> $this->input->post('partner_business_owner', true),
					);


					$this->CoreMember_model->updateCoreMemberWorking($dataworking);

					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('member/edit/'.$data['member_id']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member/edit/'.$data['member_id']);
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member/edit/'.$data['member_id']);
			}				
		}

		public function function_elements_edit(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('editCoreMember-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('editCoreMember-'.$unique['unique'],$sessions);
		}

		public function reset_edit(){
			$unique 	= $this->session->userdata('unique');
			$member_id 	= $this->uri->segment(3);

			$this->session->unset_userdata('editCoreMember-'.$unique['unique']);
			redirect('member/edit/'.$member_id);
		}

		public function reset_edit_member(){
			$unique 	= $this->session->userdata('unique');
			$member_id 	= $this->uri->segment(3);

			$this->session->unset_userdata('editCoreMember-'.$unique['unique']);
			redirect('member/edit-member-savings/'.$member_id);
		}
		
		public function changeMemberClass(){
			$member_class_id = $this->uri->segment(3);

			$member_class_mandatory_savings = $this->CoreMember_model->getCoreMemberClassMandatorySavings($member_class_id);

			echo $member_class_mandatory_savings;
		}
		
		public function changeCompany(){
			$member_class_id = $this->uri->segment(3);

			$member_class_mandatory_savings = $this->CoreMember_model->getCoreCompanyMandatorySavings($member_class_id);

			echo $member_class_mandatory_savings;
		}

		public function editCoreMemberSavings(){
			$member_id 	= $this->uri->segment(3);
			$unique 	= $this->session->userdata('unique');
			$token 		= $this->session->userdata('coremembertokenedit-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('coremembertokenedit-'.$unique['unique'],
					$token);
			}

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');

			$data['main_view']['acctmutation']		= create_double($this->CoreMember_model->getAcctMutation(),'mutation_id', 'mutation_name');

			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	

			$data['main_view']['membergender']		= $this->configuration->MemberGender();	

			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	

			$data['main_view']['coremember']		= $this->CoreMember_model->getCoreMember_Detail($member_id);

			$data['main_view']['content']			= 'CoreMember/FormEditCoreMemberSavings_view';

			$this->load->view('MainPage_view',$data);
		}

		public function editDebetCoreMemberSavings(){
			$member_id 	= $this->uri->segment(3);
			$unique 	= $this->session->userdata('unique');
			$token 		= $this->session->userdata('coremembertokenedit-'.$unique['unique']);

			if(empty($token)){
				$token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('coremembertokenedit-'.$unique['unique'],
					$token);
			}

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');

			$data['main_view']['acctmutation']		= create_double($this->CoreMember_model->getAcctMutation(),'mutation_id', 'mutation_name');

			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	

			$data['main_view']['membergender']		= $this->configuration->MemberGender();	

			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	

			$data['main_view']['debetsource']		= $this->configuration->DebetSource();	

			$data['main_view']['coremember']		= $this->CoreMember_model->getCoreMember_Detail($member_id);

			$data['main_view']['content']			= 'CoreMember/FormEditDebetCoreMemberSavings_view';

			$this->load->view('MainPage_view',$data);
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
				$row[] = '<a href="'.base_url().'member/edit-member-savings/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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

		public function getListCoreMemberEditDebet(){
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
				$row[] = '<a href="'.base_url().'member/edit-debet-member-savings/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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

		public function getMutationFunction(){
			$mutation_id 	= $this->input->post('mutation_id');

			// $mutation_id = 2;
			
			$mutation_function 			= $this->CoreMember_model->getMutationFunction($mutation_id);
			echo json_encode($mutation_function);		
		}	
		
		public function processEditCoreMemberSavings(){
			$auth = $this->session->userdata('auth');

			$username = $this->CoreMember_model->getUserName($auth['user_id']);

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'branch_id'								=> $auth['branch_id'],
				'member_name'							=> $this->input->post('member_name', true),
				'member_address'						=> $this->input->post('member_address', true),
				'city_id'								=> $this->input->post('city_id', true),
				'kecamatan_id'							=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'							=> $this->input->post('kelurahan_id', true),
				// 'dusun_id'								=> $this->input->post('dusun_id', true),
				'member_character'						=> $this->input->post('member_character', true),
				'member_principal_savings'				=> $this->input->post('member_principal_savings', true),
				'member_special_savings'				=> $this->input->post('member_special_savings', true),
				'member_mandatory_savings'				=> $this->input->post('member_mandatory_savings', true),
				'member_principal_savings_last_balance'	=> $this->input->post('member_principal_savings_last_balance', true),
				'member_special_savings_last_balance'	=> $this->input->post('member_special_savings_last_balance', true),
				'member_mandatory_savings_last_balance'	=> $this->input->post('member_mandatory_savings_last_balance', true),
				'member_token_edit'						=> $this->input->post('member_token_edit', true),
				
			);

			// print_r($data); exit;


			$total_cash_amount = $data['member_principal_savings'] + $data['member_special_savings'] + $data['member_mandatory_savings'];
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			// $this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			$member_token_edit = $this->CoreMember_model->getMemberTokenEdit($data['member_token_edit']);
			
			if($this->form_validation->run()==true){
				if($member_token_edit->num_rows() == 0){
					if($this->CoreMember_model->updateCoreMember($data)){
						if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> '' || $data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''  || $data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){

							$data_detail = array (
								'branch_id'						=> $auth['branch_id'],
								'member_id'						=> $data['member_id'],
								'mutation_id'					=> $this->input->post('mutation_id', true),
								'transaction_date'				=> date('Y-m-d'),
								'principal_savings_amount'		=> $data['member_principal_savings'],
								'special_savings_amount'		=> $data['member_special_savings'],
								'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
								'operated_name'					=> $auth['username'],
								'savings_member_detail_token'	=> $data['member_token_edit'],
							);

							if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){
								$transaction_module_code 	= "AGT";

								$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
								$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();
								$coremember 				= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
									
								$journal_voucher_period 	= date("Ym", strtotime($coremember['member_register_date']));

								//-------------------------Jurnal Cabang----------------------------------------------------
								
								$data_journal_cabang = array(
									'branch_id'						=> $auth['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'],
									'journal_voucher_description'	=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'],
									'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $coremember['member_id'],
									'transaction_journal_no' 		=> $coremember['member_no'],
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);

								$journal_voucher_id 			= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

								$preferencecompany 				= $this->CoreMember_model->getPreferenceCompany();

								if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){	

									$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_debet = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

									if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_principal_savings'],
											'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}

									if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}

									if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_special_savings'],
											'journal_voucher_credit_amount'	=> $data['member_special_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}
								} else {
									if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
										
										$mutation_type = '';
										if($data_detail['mutation_id'] == 2){
											$mutation_type = 'PENARIKAN TUNAI';
										}else if($data_detail['mutation_id'] == 3){
											$mutation_type = 'KOREKSI KREDIT';
										}else if($data_detail['mutation_id'] == 4){
											$mutation_type = 'KOREKSI DEBET';
										}else{
											$mutation_type = 'TUTUP REKENING'; //masuk else
										}

										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> $mutation_type.' '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_principal_savings'],
											'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
										// print_r('ulala2');exit;
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
										// print_r('ulala3');exit;
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_special_savings'],
											'journal_voucher_debit_amount'	=> $data['member_special_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}
									// print_r('ulala4');exit;

									$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}




						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1005','Application.CoreMember.processEditCoreMemberSavings',$auth['user_id'],'Edit  Member Savings');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Sukses
								</div> ";

						$unique = $this->session->userdata('unique');
						$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('member/process-printing/'.$data['member_id']);
					}else{
						$msg = "<div class='alert alert-danger alert-dismissable'> 
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('member/edit-member-savings/'.$data['member_id']);
					}
				} else {
					if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> '' || $data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''  || $data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
						
						$data_detail = array (
							'branch_id'						=> $auth['branch_id'],
							'member_id'						=> $data['member_id'],
							'mutation_id'					=> $this->input->post('mutation_id', true),
							'transaction_date'				=> date('Y-m-d'),
							'principal_savings_amount'		=> $data['member_principal_savings'],
							'special_savings_amount'		=> $data['member_special_savings'],
							'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
							'operated_name'					=> $auth['username'],
							'savings_member_detail_token'	=> $data['member_token_edit'],
						);

						$savings_member_detail_token = $this->CoreMember_model->getSavingsMemberDetailToken($data['member_token_edit']);

						if($savings_member_detail_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail);
						}
						
						$transaction_module_code = "AGT";

						$transaction_module_id 	= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
						$coremember 			= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
							
						$journal_voucher_period = date("Ym", strtotime($coremember['member_register_date']));
						
						//-------------------------Jurnal Cabang----------------------------------------------------
							
						$data_journal_cabang = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'SETORAN TUNAI '.$coremember['member_name'],
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
							'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);

						$journal_voucher_token = $this->CoreMember_model->getJournalVoucherToken($data_journal_cabang['journal_voucher_token']);
						
						if($journal_voucher_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);
						}


						$journal_voucher_id = $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

						if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){	

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
							);


							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);
							}						

							if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_principal_savings'],
									'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}

							if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}

							if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_special_savings'],
									'journal_voucher_credit_amount'	=> $data['member_special_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}
						} else {
							if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_principal_savings'],
									'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}	
							}

							if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}
							}

							if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_special_savings'],
									'journal_voucher_debit_amount'	=> $data['member_special_savings'],
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}
							}

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						}
					}
					



					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['user_id'], $auth['username'],'1005','Application.CoreMember.processEditCoreMemberSavings',$auth['user_id'],'Edit  Member Savings');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('member/process-printing/'.$data['member_id']);
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member/edit-member-savings/'.$data['member_id']);
			}				
		}

		public function processEditDebetCoreMemberSavings(){
			$auth 	= $this->session->userdata('auth');
			$unique = $this->session->userdata('unique');

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'member_principal_savings_last_balance'	=> $this->input->post('member_principal_savings_last_balance', true),
				'member_special_savings_last_balance'	=> $this->input->post('member_special_savings_last_balance', true),
				'member_mandatory_savings_last_balance'	=> $this->input->post('member_mandatory_savings_last_balance', true),
				'member_token_edit'						=> $this->input->post('member_token_edit', true),
			);

			$mandatory_amount 		= $this->input->post('member_mandatory_savings', true);
			$special_amount			= $this->input->post('member_special_savings', true);
			$principal_amount		=($mandatory_amount+$special_amount)*-1;

			if($data['member_principal_savings_last_balance'] < 0){
				$msg = "<div class='alert alert-danger alert-dismissable'> 
				<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
					Saldo Simpanan Pokok Tidak Mencukupi
				</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member/edit-debet-member-savings/'.$data['member_id']);
			}
			
			$member_token_edit = $this->CoreMember_model->getMemberTokenEdit($data['member_token_edit']);

			if($member_token_edit->num_rows() == 0){
				if($this->CoreMember_model->updateCoreMember($data)){
					$data_detail = array (
						'branch_id'						=> $auth['branch_id'],
						'member_id'						=> $data['member_id'],
						'mutation_id'					=> $this->input->post('mutation_id', true),
						'transaction_date'				=> date('Y-m-d'),
						'principal_savings_amount'		=> $principal_amount,
						'special_savings_amount'		=> $mandatory_amount,
						'mandatory_savings_amount'		=> $special_amount,
						'operated_name'					=> $auth['username'],
						'savings_member_detail_token'	=> $data['member_token_edit'],
					);

					if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){
						$transaction_module_code 	= "AGT";

						$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
						$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();
						$coremember 				= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
							
						$journal_voucher_period 	= date("Ym", strtotime($coremember['member_register_date']));

						//-------------------------Jurnal Cabang----------------------------------------------------
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'MUTASI ANGGOTA DEBET '.$coremember['member_name'],
							'journal_voucher_description'	=> 'MUTASI ANGGOTA DEBET '.$coremember['member_name'],
							'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);
						
						$this->CoreMember_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany 				= $this->CoreMember_model->getPreferenceCompany();

						if($principal_amount <> 0 || $principal_amount <> ''){
							$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

							$data_debet =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN DEBET '.$coremember['member_name'],
								'journal_voucher_amount'		=> $principal_amount*-1,
								'journal_voucher_debit_amount'	=> $principal_amount*-1,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
							);

							$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);	
						}

						if($mandatory_amount <> 0 || $mandatory_amount <> ''){
							$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN DEBET '.$coremember['member_name'],
								'journal_voucher_amount'		=> $mandatory_amount,
								'journal_voucher_credit_amount'	=> $mandatory_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
							);

							$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
						}
						

						if($special_amount <> 0 || $special_amount <> ''){
							$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN DEBET '.$coremember['member_name'],
								'journal_voucher_amount'		=> $special_amount,
								'journal_voucher_credit_amount'	=> $special_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
							);

							$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
						}
					}
					$msg = "<div class='alert alert-success alert-dismissable'> 
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
						Debit Simpanan Pokok Berhasil
					</div> ";
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('member');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
						Debit Simpanan Pokok Tidak Berhasil
					</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member/edit-debet-member-savings/'.$data['member_id']);
				}
			}else{
				$data_detail = array (
					'branch_id'						=> $auth['branch_id'],
					'member_id'						=> $data['member_id'],
					'mutation_id'					=> $this->input->post('mutation_id', true),
					'transaction_date'				=> date('Y-m-d'),
					'principal_savings_amount'		=> $principal_amount,
					'special_savings_amount'		=> $mandatory_amount,
					'mandatory_savings_amount'		=> $special_amount,
					'operated_name'					=> $auth['username'],
					'savings_member_detail_token'	=> $data['member_token_edit'],
				);

				$savings_member_detail_token = $this->CoreMember_model->getSavingsMemberDetailToken($data['member_token_edit']);

				if($savings_member_detail_token->num_rows() == 0){
					if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){
						$transaction_module_code 	= "AGT";

						$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
						$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();
						$coremember 				= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
							
						$journal_voucher_period 	= date("Ym", strtotime($coremember['member_register_date']));

						//-------------------------Jurnal Cabang----------------------------------------------------
						
						$data_journal = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'MUTASI ANGGOTA DEBET '.$coremember['member_name'],
							'journal_voucher_description'	=> 'MUTASI ANGGOTA DEBET '.$coremember['member_name'],
							'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);
						
						$this->CoreMember_model->insertAcctJournalVoucher($data_journal);

						$journal_voucher_id 			= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany 				= $this->CoreMember_model->getPreferenceCompany();

						if($principal_amount <> 0 || $principal_amount <> ''){
							$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

							$data_debet =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN DEBET '.$coremember['member_name'],
								'journal_voucher_amount'		=> $principal_amount*-1,
								'journal_voucher_debit_amount'	=> $principal_amount*-1,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);	
							}
						}

						if($mandatory_amount <> 0 || $mandatory_amount <> ''){
							$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN DEBET '.$coremember['member_name'],
								'journal_voucher_amount'		=> $mandatory_amount,
								'journal_voucher_credit_amount'	=> $mandatory_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}	
						}
						

						if($special_amount <> 0 || $special_amount <> ''){
							$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $account_id,
								'journal_voucher_description'	=> 'SETORAN DEBET '.$coremember['member_name'],
								'journal_voucher_amount'		=> $special_amount,
								'journal_voucher_credit_amount'	=> $special_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
							}
						}
					}
					$msg = "<div class='alert alert-success alert-dismissable'> 
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
						Debit Simpanan Pokok Berhasil
					</div> ";
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('member');
				}else{
					$msg = "<div class='alert alert-success alert-dismissable'> 
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
						Debit Simpanan Pokok Berhasil
					</div> ";
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('member');
				}
			}
		}

		
		public function processEditCoreMemberSavings_old(){
			$auth = $this->session->userdata('auth');

			$username = $this->CoreMember_model->getUserName($auth['user_id']);

			$data = array(
				'member_id'								=> $this->input->post('member_id', true),
				'branch_id'								=> $auth['branch_id'],
				'member_name'							=> $this->input->post('member_name', true),
				'member_address'						=> $this->input->post('member_address', true),
				'city_id'								=> $this->input->post('city_id', true),
				'kecamatan_id'							=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'							=> $this->input->post('kelurahan_id', true),
				// 'dusun_id'								=> $this->input->post('dusun_id', true),
				'member_character'						=> $this->input->post('member_character', true),
				'member_principal_savings'				=> $this->input->post('member_principal_savings', true),
				'member_special_savings'				=> $this->input->post('member_special_savings', true),
				'member_mandatory_savings'				=> $this->input->post('member_mandatory_savings', true),
				'member_principal_savings_last_balance'	=> $this->input->post('member_principal_savings_last_balance', true),
				'member_special_savings_last_balance'	=> $this->input->post('member_special_savings_last_balance', true),
				'member_mandatory_savings_last_balance'	=> $this->input->post('member_mandatory_savings_last_balance', true),
				'member_token_edit'						=> $this->input->post('member_token_edit', true),
				
			);


			$total_cash_amount = $data['member_principal_savings'] + $data['member_special_savings'] + $data['member_mandatory_savings'];
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			// $this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			$member_token_edit = $this->CoreMember_model->getMemberTokenEdit($data['member_token_edit']);
			
			if($this->form_validation->run()==true){
				if($member_token_edit->num_rows() == 0){
					if($this->CoreMember_model->updateCoreMember($data)){
						if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> '' || $data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> '' || $data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){

							$data_detail = array (
								'branch_id'						=> $auth['branch_id'],
								'member_id'						=> $data['member_id'],
								'mutation_id'					=> $this->input->post('mutation_id', true),
								'transaction_date'				=> date('Y-m-d'),
								'principal_savings_amount'		=> $data['member_principal_savings'],
								'special_savings_amount'		=> $data['member_special_savings'],
								'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
								'operated_name'					=> $auth['username'],
								'savings_member_detail_token'	=> $data['member_token_edit'],
							);

							if($this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail)){
								$transaction_module_code 	= "AGT";

								$transaction_module_id 		= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
								$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();
								$coremember 				= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
									
								$journal_voucher_period 	= date("Ym", strtotime($coremember['member_register_date']));

								//-------------------------Jurnal Cabang----------------------------------------------------
								
								$data_journal_cabang = array(
									'branch_id'						=> $auth['branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'],
									'journal_voucher_description'	=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'],
									'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $coremember['member_id'],
									'transaction_journal_no' 		=> $coremember['member_no'],
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);

								$journal_voucher_id = $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

								$preferencecompany 						= $this->CoreMember_model->getPreferenceCompany();

								if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){					

									$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);
									//================================= OLD =====/
									$data_debet = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

									$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

									$data_credit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_central_capital_id'],
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									//=================================END OF OLD =====/
								} else {
									$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

									$data_debit =array(
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_central_capital_id'],
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);

									$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_id,
										'account_id'					=> $preferencecompany['account_cash_id'],
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);

									
								}
								

								//-------------------------Jurnal Pusat----------------------------------------------------

								$data_journal_pusat = array(
									'branch_id'						=> $preferencecompany['central_branch_id'],
									'journal_voucher_period' 		=> $journal_voucher_period,
									'journal_voucher_date'			=> date('Y-m-d'),
									'journal_voucher_title'			=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'],
									'journal_voucher_description'	=> 'MUTASI ANGGOTA TUNAI '.$coremember['member_name'],
									'journal_voucher_token'			=> $data['member_token_edit'].$preferencecompany['central_branch_id'],
									'transaction_module_id'			=> $transaction_module_id,
									'transaction_module_code'		=> $transaction_module_code,
									'transaction_journal_id' 		=> $coremember['member_id'],
									'transaction_journal_no' 		=> $coremember['member_no'],
									'created_id' 					=> $auth['user_id'],
									'created_on' 					=> date('Y-m-d H:i:s'),
								);
								
								$this->CoreMember_model->insertAcctJournalVoucher($data_journal_pusat);

								$journal_voucher_pusat_id 	= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);


								$preferencecompany 						= $this->CoreMember_model->getPreferenceCompany();

								if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){	

									$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

									$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

									$data_debet = array (
										'journal_voucher_id'			=> $journal_voucher_pusat_id,
										'account_id'					=> $account_rak_id,
										'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_debit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 0,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);

									if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_principal_savings'],
											'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}

									if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}

									if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_credit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_special_savings'],
											'journal_voucher_credit_amount'	=> $data['member_special_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 1,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);	
									}
								} else {
									if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_principal_savings'],
											'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_mandatory_savings'],
											'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
										$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

										$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

										$data_debit =array(
											'journal_voucher_id'			=> $journal_voucher_pusat_id,
											'account_id'					=> $account_id,
											'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
											'journal_voucher_amount'		=> $data['member_special_savings'],
											'journal_voucher_debit_amount'	=> $data['member_special_savings'],
											'account_id_default_status'		=> $account_id_default_status,
											'account_id_status'				=> 0,
											'created_id' 					=> $auth['user_id'],
											'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
										);

										$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);	
									}

									$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

									$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

									$data_credit = array (
										'journal_voucher_id'			=> $journal_voucher_pusat_id,
										'account_id'					=> $account_rak_id,
										'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
										'journal_voucher_amount'		=> $total_cash_amount,
										'journal_voucher_credit_amount'	=> $total_cash_amount,
										'account_id_default_status'		=> $account_id_default_status,
										'account_id_status'				=> 1,
										'created_id' 					=> $auth['user_id'],
										'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
									);

									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}
							}
						}




						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Sukses
								</div> ";

						$unique = $this->session->userdata('unique');
						$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('member/process-printing/'.$data['member_id']);
					}else{
						$msg = "<div class='alert alert-danger alert-dismissable'> 
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
									Edit Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('member/edit-member-savings/'.$data['member_id']);
					}
				} else {
					if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> '' || $data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> '' || $data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){

						$data_detail = array (
							'branch_id'						=> $auth['branch_id'],
							'member_id'						=> $data['member_id'],
							'mutation_id'					=> $this->input->post('mutation_id', true),
							'transaction_date'				=> date('Y-m-d'),
							'principal_savings_amount'		=> $data['member_principal_savings'],
							'special_savings_amount'		=> $data['member_special_savings'],
							'mandatory_savings_amount'		=> $data['member_mandatory_savings'],
							'operated_name'					=> $auth['username'],
							'savings_member_detail_token'	=> $data['member_token_edit'],
						);

						$savings_member_detail_token = $this->CoreMember_model->getSavingsMemberDetailToken($data['member_token_edit']);

						if($savings_member_detail_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctSavingsMemberDetail($data_detail);
						}
						
						$transaction_module_code = "AGT";

						$transaction_module_id 	= $this->CoreMember_model->getTransactionModuleID($transaction_module_code);
						$coremember 			= $this->CoreMember_model->getCoreMember_Detail($data['member_id']);
							
						$journal_voucher_period = date("Ym", strtotime($coremember['member_register_date']));
						
						//-------------------------Jurnal Cabang----------------------------------------------------
							
						$data_journal_cabang = array(
							'branch_id'						=> $auth['branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'SETORAN TUNAI '.$coremember['member_name'],
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
							'journal_voucher_token'			=> $data['member_token_edit'].$auth['branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);

						$journal_voucher_token = $this->CoreMember_model->getJournalVoucherToken($data_journal_cabang['journal_voucher_token']);
						
						if($journal_voucher_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctJournalVoucher($data_journal_cabang);
						}


						$journal_voucher_id = $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany = $this->CoreMember_model->getPreferenceCompany();


						if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){					

							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
							);


							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);
							}

							$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

							$data_credit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_central_capital_id'],
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						} else {
							$account_id_default_status 		= $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_central_capital_id']);

							$data_debit =array(
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_central_capital_id'],
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_central_capital_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
							}


							$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($preferencecompany['account_cash_id']);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_id,
								'account_id'					=> $preferencecompany['account_cash_id'],
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$preferencecompany['account_cash_id'],
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						}

						//-------------------------Jurnal Pusat----------------------------------------------------

						$data_journal_pusat = array(
							'branch_id'						=> $preferencecompany['central_branch_id'],
							'journal_voucher_period' 		=> $journal_voucher_period,
							'journal_voucher_date'			=> date('Y-m-d'),
							'journal_voucher_title'			=> 'SETORAN TUNAI '.$coremember['member_name'],
							'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
							'journal_voucher_token'			=> $data['member_token_edit'].$preferencecompany['central_branch_id'],
							'transaction_module_id'			=> $transaction_module_id,
							'transaction_module_code'		=> $transaction_module_code,
							'transaction_journal_id' 		=> $coremember['member_id'],
							'transaction_journal_no' 		=> $coremember['member_no'],
							'created_id' 					=> $auth['user_id'],
							'created_on' 					=> date('Y-m-d H:i:s'),
						);
						
						$journal_voucher_token = $this->CoreMember_model->getJournalVoucherToken($data_journal_pusat['journal_voucher_token']);
						
						if($journal_voucher_token->num_rows() == 0){
							$this->CoreMember_model->insertAcctJournalVoucher($data_journal_pusat);
						}

						$journal_voucher_pusat_id 	= $this->CoreMember_model->getJournalVoucherID($auth['user_id']);

						$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

						if($data_detail['mutation_id'] == $preferencecompany['cash_deposit_id']){	

							$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

							$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

							$data_debet = array (
								'journal_voucher_id'			=> $journal_voucher_pusat_id,
								'account_id'					=> $account_rak_id,
								'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_debit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 0,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
							);

							$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debet['journal_voucher_item_token']);

							if($journal_voucher_item_token->num_rows() == 0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_debet);
							}						

							if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_principal_savings'],
									'journal_voucher_credit_amount'	=> $data['member_principal_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}

							if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_credit_amount'	=> $data['member_mandatory_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}

							if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_credit =array(
									'journal_voucher_id'			=> $journal_voucher_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'SETORAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_special_savings'],
									'journal_voucher_credit_amount'	=> $data['member_special_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 1,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_credit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows()==0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
								}	
							}
						} else {
							if($data['member_principal_savings'] <> 0 || $data['member_principal_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['principal_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_pusat_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_principal_savings'],
									'journal_voucher_debit_amount'	=> $data['member_principal_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}	
							}

							if($data['member_mandatory_savings'] <> 0 || $data['member_mandatory_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['mandatory_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_pusat_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_mandatory_savings'],
									'journal_voucher_debit_amount'	=> $data['member_mandatory_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}
							}

							if($data['member_special_savings'] <> 0 || $data['member_special_savings'] <> ''){
								$account_id = $this->CoreMember_model->getAccountID($preferencecompany['special_savings_id']);

								$account_id_default_status = $this->CoreMember_model->getAccountIDDefaultStatus($account_id);

								$data_debit =array(
									'journal_voucher_id'			=> $journal_voucher_pusat_id,
									'account_id'					=> $account_id,
									'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
									'journal_voucher_amount'		=> $data['member_special_savings'],
									'journal_voucher_debit_amount'	=> $data['member_special_savings'],
									'account_id_default_status'		=> $account_id_default_status,
									'account_id_status'				=> 0,
									'created_id' 					=> $auth['user_id'],
									'journal_voucher_item_token'	=> $data['member_token_edit'].$account_id,
								);

								$journal_voucher_item_token = $this->CoreMember_model->getJournalVoucherItemToken($data_debit['journal_voucher_item_token']);

								if($journal_voucher_item_token->num_rows() == 0){
									$this->CoreMember_model->insertAcctJournalVoucherItem($data_debit);
								}
							}

							$account_rak_id 			= $this->CoreMember_model->getAccountRAKID($auth['branch_id']);

							$account_id_default_status 	= $this->CoreMember_model->getAccountIDDefaultStatus($account_rak_id);

							$data_credit = array (
								'journal_voucher_id'			=> $journal_voucher_pusat_id,
								'account_id'					=> $account_rak_id,
								'journal_voucher_description'	=> 'PENARIKAN TUNAI '.$coremember['member_name'],
								'journal_voucher_amount'		=> $total_cash_amount,
								'journal_voucher_credit_amount'	=> $total_cash_amount,
								'account_id_default_status'		=> $account_id_default_status,
								'account_id_status'				=> 1,
								'created_id' 					=> $auth['user_id'],
								'journal_voucher_item_token'	=> $data['member_token_edit'].$account_rak_id,
							);

							if($journal_voucher_item_token->num_rows()==0){
								$this->CoreMember_model->insertAcctJournalVoucherItem($data_credit);
							}
						}
					}
					



					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['user_id'], $auth['username'],'1004','Application.CoreMember.processEditCoreMember',$auth['user_id'],'Edit  Member');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Anggota Sukses
							</div> ";

					$unique = $this->session->userdata('unique');
					$this->session->unset_userdata('coremembertokenedit-'.$unique['unique']);
					$this->session->set_userdata('message',$msg);
					redirect('member/process-printing/'.$data['member_id']);
				}
				
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member/edit-member-savings/'.$data['member_id']);
			}				
		}

		public function processPrinting(){
			$auth 						= $this->session->userdata('auth');
			$member_id 					= $this->uri->segment(3);
			$acctsavingsmemberdetail	= $this->CoreMember_model->getLastAcctSavingsMemberDetail($member_id);
			$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

			
			if($acctsavingsmemberdetail['mutation_id'] == $preferencecompany['cash_deposit_id']){
				$keperluan = 'SETORAN TUNAI';
				$keterangan = 'Telah diterima uang dari';
			} else if($acctsavingsmemberdetail['mutation_id'] == $preferencecompany['cash_withdrawal_id']){
				$keperluan = 'PENARIKAN TUNAI';
				$keterangan = 'Telah diserahkan uang kepada';
			}

			$total = $acctsavingsmemberdetail['principal_savings_amount'] + $acctsavingsmemberdetail['mandatory_savings_amount'] + $acctsavingsmemberdetail['special_savings_amount'];

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
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			// print_r($preferencecompany['logo_koperasi']);exit;

			$tbl = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
				<tr>
					<td rowspan=\"2\" width=\"20%\">".$img."</td>
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">BUKTI ".$keperluan." ANGGOTA</div></td>
				</tr>
				<tr>
					<td width=\"40%\"><div style=\"text-align: left; font-size:14px\">Jam : ".date('H:i:s')."</div></td>
				</tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = 
			$keterangan .":
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Nama</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsmemberdetail['member_name']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">No. Rekening</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsmemberdetail['member_no']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Alamat</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$acctsavingsmemberdetail['member_address']."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Keperluan</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".$keperluan."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Simp. Pokok</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsmemberdetail['principal_savings_amount'], 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Simp. Khusus</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($acctsavingsmemberdetail['special_savings_amount'], 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Simp. Wajib</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\"><u>: Rp. &nbsp;".number_format($acctsavingsmemberdetail['mandatory_savings_amount'], 2)."</u></div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Jumlah</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: Rp. &nbsp;".number_format($total, 2)."</div></td>
				</tr>
				<tr>
					<td width=\"20%\"><div style=\"text-align: left;\">Terbilang</div></td>
					<td width=\"80%\"><div style=\"text-align: left;\">: ".numtotxt($total)."</div></td>
				</tr>				
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"30%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"20%\"><div style=\"text-align: center;\"></div></td>
					<td width=\"30%\"><div style=\"text-align: center;\">".$this->CoreMember_model->getBranchCity($auth['branch_id']).", ".date('d-m-Y')."</div></td>
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
			$js='';
			//Close and output PDF document
			$filename = 'Kwitansi_Simpanan_Anggota_'.$acctsavingsmemberdetail['member_name'].'.pdf';

			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);
			
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}
		
		public function deleteCoreMember(){
			if($this->CoreMember_model->deleteCoreMember($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1005','Application.CoreMember.deleteCoreMember',$auth['user_id'],'Delete Member');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Anggota Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Anggota Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member');
			}
		}

		public function updateCoreMemberStatus(){	
			$data['main_view']['content']			= 'CoreMember/ListUpdateCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function getUpdateCoreMemberStatusList(){
			$auth = $this->session->userdata('auth');
			$list = $this->CoreMember_model->get_datatables($auth['branch_id']);

			//print_r($list);exit;
			$memberstatus		= $this->configuration->MemberStatus();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $customers) {
				$preferencecompany = $this->CoreMember_model->getPreferenceCompany();

				$acctsavingsaccount = $this->CoreMember_model->getAcctSavingsAccount($preferencecompany['principal_savings_id'], $customers->member_id);

				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $customers->member_no;
				$row[] = $customers->member_name;
				$row[] = $customers->member_address;
				$row[] = $memberstatus[$customers->member_status];
				$row[] = $membercharacter[$customers->member_character];
				$row[] = $customers->member_phone;
				$row[] = number_format($customers->member_principal_savings_last_balance, 2);
				// $row[] = number_format($customers->member_special_savings, 2);
				$row[] = number_format($customers->member_mandatory_savings_last_balance, 2);	         	         

				if($acctsavingsaccount->num_rows() > 0 ){
					if($customers->member_status == 0){
						$row[] = '<a href="'.base_url().'member/process-update-status/'.$customers->member_id.'" onClick="javascript:return confirm(\'Yakin status anggota akan diupdate ?\')" class="btn default btn-xs purple" role="button"><i class="fa fa-edit"></i> Update</a>';
					} else {
						$row[] = '';
					}
				} else {
					$row[] = '';
				}

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

		public function processUpdateCoreMemberStatus(){
			if($this->CoreMember_model->updateCoreMemberStatus($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['user_id'], $auth['username'],'1006','Application.CoreMember.processUpdateCoreMemberStatus',$auth['user_id'],'Update Member Status');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Update Status Anggota Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member/update-status');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Update Status Anggota Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('member/update-status');
			}
		}

		public function printBookCoreMember(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['coremember']		= $this->CoreMember_model->getDataCoreMember($auth['branch_id']);
			$data['main_view']['memberstatus']		= $this->configuration->MemberStatus();	
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['content']			= 'CoreMember/ListPrintBookCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processPrintCoverBookCoreMember(){
			$member_id 			= $this->uri->segment(3);
			$coremember			= $this->CoreMember_model->getCoreMember_Detail($member_id);
			$MemberCharacter 	= $this->configuration->MemberCharacter();


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

			$pdf->SetMargins(7, 4, 7, 7); // put space of 10 on top
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

			$preferencecompany 	= $this->CoreMember_model->getPreferenceCompany();
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
			<br/>";

			$pdf->writeHTML($tbl, true, false, false, false, '');

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr>
					<td width=\"3%\"></td>
					<td width=\"12%\"><div style=\"text-align: left;\">No. Anggota</div></td>
					<td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_no']."</div></td>
				</tr>
				<tr>
					<td width=\"3%\"></td>
					<td width=\"12%\"><div style=\"text-align: left;\">Keanggotaan</div></td>
					<td width=\"40%\"><div style=\"text-align: left;\">: ".$MemberCharacter[$coremember['member_character']]."</div></td>
				</tr>
				<tr>
					<td width=\"3%\"></td>
					<td width=\"12%\"><div style=\"text-align: left;\">Nama</div></td>
					<td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_name']."</div></td>
				</tr>
				<tr>
					<td width=\"3%\"></td>
					<td width=\"12%\"><div style=\"text-align: left;\">Alamat</div></td>
					<td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_address']."</div></td>
				</tr>
				<tr>
					<td width=\"3%\"></td>
					<td width=\"12%\"><div style=\"text-align: left;\">No. Identitas</div></td>
					<td width=\"40%\"><div style=\"text-align: left;\">: ".$coremember['member_identity_no']."</div></td>
				</tr>				
			</table>";


			$pdf->writeHTML($tbl1, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cover Buku '.$coremember['member_name'].'.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function printMutationCoreMember(){
			$auth = $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-coremembermutation');
			$unique = $this->session->userdata('unique');


			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id'] 		= '';
			}

			$member_id = $this->uri->segment(3);
			if($member_id == ''){
				$member_id = $sesi['member_id'];
			}

			$this->session->unset_userdata('datamutasianggota-'.$unique['unique']);

			$data['main_view']['coremember']					= $this->CoreMember_model->getCoreMember_Detail($member_id);
			$data['main_view']['acctsavingsmemberdetail']		= $this->CoreMember_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);	

			$data['main_view']['content']			= 'CoreMember/ListPrintMutationCoreMember_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterMutation(){
			$data = array (
				"start_date"	=> tgltodb($this->input->post('start_date',true)),
				"end_date"		=> tgltodb($this->input->post('end_date',true)),
				"member_id"		=> $this->input->post('member_id', true),
			);

			$this->session->set_userdata('filter-coremembermutation',$data);
			redirect('member/print-mutation');
		}

		public function reset_search_mutation(){
			$this->session->unset_userdata('filter-coremembermutation');
			redirect('member/print-mutation');
		}

		public function getListCoreMemberMutation(){
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
				$row[] = '<a href="'.base_url().'member/print-mutation/'.$customers->member_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
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


		public function processPrintMutasiCoreMember(){
			$unique = $this->session->userdata('unique');
			$auth 	= $this->session->userdata('auth');
			$sesi	= $this->session->userdata('filter-coremembermutation');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['member_id'] 		= '';
			}

			$datamutasianggota = $this->session->userdata('datamutasianggota-'.$unique['unique']);

			$status 	= $this->uri->segment(3);
			$member_id 	= $this->uri->segment(4);

			if(empty($datamutasianggota)){
				$mutasicoremember		= $this->CoreMember_model->getAcctSavingsMemberDetail($member_id, $sesi['start_date'], $sesi['end_date']);
				$member_last_number 	= $this->CoreMember_model->getMemberLastNumber($member_id);


				if(empty($member_last_number) || $member_last_number == 0){
					$no = 1;
				} else {
					$no = $member_last_number + 1;
				}

				foreach ($mutasicoremember as $key => $val) {
					if($no == 31){
						$no = 1;
					} else {
						$no = $no;
					}

					
					$data[] = array (
						'no'							=> $no,
						'savings_member_detail_id'		=> $val['savings_member_detail_id'],
						'member_id'						=> $val['member_id'],
						'transaction_date'				=> $val['transaction_date'],
						'transaction_code'				=> $val['mutation_code'],
						'principal_savings_amount'		=> $val['principal_savings_amount'],
						'special_savings_amount'		=> $val['special_savings_amount'],
						'mandatory_savings_amount'		=> $val['mandatory_savings_amount'],
						'last_balance'					=> $val['last_balance'],
						'operated_name'					=> $val['operated_name'],	
					);

					
					$no++;
					
				}

				$this->session->set_userdata('datamutasianggota-'.$unique['unique'], $data);
			}
			
			$datamutasianggota = $this->session->userdata('datamutasianggota-'.$unique['unique']);

			if($status == 'print'){
				foreach ($datamutasianggota as $k => $v) {
					$update_data = array(
						'savings_member_detail_id'		=> $v['savings_member_detail_id'],
						'member_id'						=> $v['member_id'],
						'savings_print_status'			=> 1,
						'member_last_number'			=> $v['no'],
					);

					$this->CoreMember_model->updatePrintMutationStatus($update_data);
				}
			}

			// print_r($data);
			// 	print_r("<BR>");
			// 	print_r("<BR>");

			// exit;


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

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

			// $pdf->SetMargins(2, 26, 7, 7);
			$pdf->SetMargins(2, 4, 7, 7); // put space of 10 on top
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
			$resolution= array(180, 170);
			
			$page = $pdf->AddPage('P', $resolution);

			/*$pdf->Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

			$pdf->SetFont('helvetica', '', 8);

			// -----------------------------------------------------------------------------
			$preferencecompany 	= $this->CoreMember_model->getPreferenceCompany();
			// -----------------------------------------------------------------------------
			
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl2 = "
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
			<br/>";

			$pdf->writeHTML($tbl2, true, false, false, false, '');

			$tbl = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			if($member_last_number > 0){
				for ($i=1; $i <= $member_last_number ; $i++) { 
					if($i == 15){
						$tbl1 .= "
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						";

					} else {
						$tbl1 .= "
						<tr>
							<td></td>
						</tr>";
					}
				}
			}
			foreach ($datamutasianggota as $key => $val) { 
				if($val['no'] == 1){
					$tbl1 .= "
							<tr>
								<td width=\"4%\"><div style=\"text-align: center;\">No</div></td>
								<td width=\"10%\"><div style=\"text-align: center;\">Tanggal</div></td>
								<td width=\"9%\"><div style=\"text-align: center;\">Sandi</div></td>
								<td width=\"12%\"><div style=\"text-align: center;\">S.Pokok</div></td>
								<td width=\"13%\"><div style=\"text-align: center;\">S.Khusus</div></td>
								<td width=\"12%\"><div style=\"text-align: center;\">S Wajib</div></td>
								<td width=\"12%\"><div style=\"text-align: center;\">Saldo</div></td>
								<td width=\"5%\"><div style=\"text-align: center;\">Opt</div></td>
							</tr>";
				}

				$tbl1 .= "
					<tr>
						<td width=\"3%\"><div style=\"text-align: left;\">".$val['no'].".</div></td>
						<td width=\"10%\"><div style=\"text-align: center;\">".date('d-m-y',strtotime(($val['transaction_date'])))."</div></td>
						<td width=\"9%\"><div style=\"text-align: center;\">".$val['transaction_code']."</div></td>
						<td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['principal_savings_amount'])." &nbsp;</div></td>
						<td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['special_savings_amount'])." &nbsp;</div></td>
						<td width=\"13%\"><div style=\"text-align: right;\">".number_format($val['mandatory_savings_amount'])." &nbsp;</div></td>
						<td width=\"12%\"><div style=\"text-align: right;\">".number_format($val['last_balance'])." &nbsp;</div></td>
						<td width=\"5%\"><div style=\"text-align: center;\">".substr($val['operated_name'],0,3)."</div></td>
					</tr>
				";

				if($val['no'] == 15){
						$tbl1 .= "
							<tr>
								<td></td>
							</tr>
							<tr>
								<td></td>
							</tr>
							<tr>
								<td></td>
							</tr>
							<tr>
								<td></td>
							</tr>
						";
					}

					if($val['no'] == 30){
						$tbl1 .= "
							<tr>
								<td></td>
							</tr>

						";
					}
			}

			$tbl2 = "</table>";

			$pdf->writeHTML($tbl.$tbl1.$tbl2, true, false, false, false, '');

			if (ob_get_length() > 0){
			ob_clean();
			}
			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cetak Mutasi Anggota.pdf';

			if($status == 'preview'){

				$pdf->Output($filename, 'I');

			} else if($status == 'print'){

				// force print dialog
				$js .= 'print(true);';

				// set javascript
				$pdf->IncludeJS($js);

				$pdf->Output($filename, 'I');

			}

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function exportMasterDataCoreMember(){
			$auth = $this->session->userdata('auth'); 	
			$coremember	= $this->CoreMember_model->getExport($auth['branch_id']);
			$corecompany = $this->CoreMember_model->getCoreCompany();

			$memberstatus		= $this->configuration->MemberStatus();	
			$memberstatusaktif	= $this->configuration->MemberStatusAktif();	
			$membergender		= $this->configuration->MemberGender();	
			$membercharacter	= $this->configuration->MemberCharacter();
			$memberidentity 	= $this->configuration->MemberIdentity();

			

			if($coremember->num_rows()!=0){
				$this->load->library('Excel');
				
				$this->excel->getProperties()->setCreator("SIS")
									->setLastModifiedBy("SIS")
									->setTitle("Master Data Anggota")
									->setSubject("")
									->setDescription("Master Data Anggota")
									->setKeywords("Master, Data, Anggota")
									->setCategory("Master Data Anggota");
									
				$this->excel->setActiveSheetIndex(0);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
				$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
				$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(30);	
				$this->excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
				$this->excel->getActiveSheet()->getColumnDimension('N')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('O')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('P')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('Q')->setWidth(10);	
				$this->excel->getActiveSheet()->getColumnDimension('R')->setWidth(5);	
				$this->excel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
				$this->excel->getActiveSheet()->getColumnDimension('T')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('V')->setWidth(5);	
				$this->excel->getActiveSheet()->getColumnDimension('W')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('X')->setWidth(15);	
				$this->excel->getActiveSheet()->getColumnDimension('Z')->setWidth(5);	
				$this->excel->getActiveSheet()->getColumnDimension('AA')->setWidth(20);	
				$this->excel->getActiveSheet()->getColumnDimension('AB')->setWidth(15);
				
				$this->excel->getActiveSheet()->mergeCells("B1:P1");
				$this->excel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('B3:P3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('B3:P3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('B3:P3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('B1',"Master Data Anggota");
				
				$this->excel->getActiveSheet()->mergeCells("V1:X1");
				$this->excel->getActiveSheet()->getStyle('V1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('V1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('V3:X3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('V3:X3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('V3:X3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('V1',"Data Anggota Berdasar Gender");
				
				$this->excel->getActiveSheet()->mergeCells("Z1:AB1");
				$this->excel->getActiveSheet()->getStyle('Z1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('Z1')->getFont()->setBold(true)->setSize(16);
				$this->excel->getActiveSheet()->getStyle('Z3:AB3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('Z3:AB3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('Z3:AB3')->getFont()->setBold(true);	
				$this->excel->getActiveSheet()->setCellValue('Z1',"Data Anggota Berdasar Status");

				$this->excel->getActiveSheet()->setCellValue('B3',"No");
				$this->excel->getActiveSheet()->setCellValue('C3',"No Anggota");
				$this->excel->getActiveSheet()->setCellValue('D3',"Nama Anggota");
				$this->excel->getActiveSheet()->setCellValue('E3',"Alamat");
				$this->excel->getActiveSheet()->setCellValue('F3',"Tempat Lahir");
				$this->excel->getActiveSheet()->setCellValue('G3',"Tanggal Lahir");
				$this->excel->getActiveSheet()->setCellValue('H3',"Jenis Kelamin");
				$this->excel->getActiveSheet()->setCellValue('I3',"Status");
				$this->excel->getActiveSheet()->setCellValue('J3',"Status Aktif");
				$this->excel->getActiveSheet()->setCellValue('K3',"No. Telp");
				$this->excel->getActiveSheet()->setCellValue('L3',"Tipe Pekerjaan");
				$this->excel->getActiveSheet()->setCellValue('M3',"Perusahaan");
				$this->excel->getActiveSheet()->setCellValue('N3',"Simpanan Pokok");
				$this->excel->getActiveSheet()->setCellValue('O3',"Simpanan Khusus");
				$this->excel->getActiveSheet()->setCellValue('P3',"Simpanan Wajib");

				$this->excel->getActiveSheet()->setCellValue('V3', "No");
				$this->excel->getActiveSheet()->setCellValue('V4', "1");
				$this->excel->getActiveSheet()->setCellValue('V5', "2");
				$this->excel->getActiveSheet()->setCellValue('W3', "Jenis Kelamin");
				$this->excel->getActiveSheet()->setCellValue('W4', "Laki - Laki");
				$this->excel->getActiveSheet()->setCellValue('W5', "Perempuan");
				$this->excel->getActiveSheet()->setCellValue('X3', "Jumlah Anggota");

				$this->excel->getActiveSheet()->setCellValue('Z3', "No");
				$this->excel->getActiveSheet()->setCellValue('Z4', "1");
				$this->excel->getActiveSheet()->setCellValue('Z5', "2");
				$this->excel->getActiveSheet()->setCellValue('AA3', "Status Aktif");
				$this->excel->getActiveSheet()->setCellValue('AA4', "Aktif");
				$this->excel->getActiveSheet()->setCellValue('AA5', "Tidak Aktif");
				$this->excel->getActiveSheet()->setCellValue('AB3', "Jumlah Anggota");
				
				

				$j=4;
				$no=0;
				
				$count_member_entership = 0;
				$count_member_male 		= 0;
				$count_member_female 	= 0;
				$count_member_active 	= 0;
				$count_member_nonactive = 0;
				foreach($coremember->result_array() as $key=>$val){
					if(is_numeric($key)){
						$no++;
						$this->excel->setActiveSheetIndex(0);
						$this->excel->getActiveSheet()->getStyle('B'.$j.':P'.$j)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$this->excel->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$this->excel->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('L'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
						$this->excel->getActiveSheet()->getStyle('M'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('N'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('O'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
						$this->excel->getActiveSheet()->getStyle('P'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


						$this->excel->getActiveSheet()->setCellValue('B'.$j, $no);
						$this->excel->getActiveSheet()->setCellValue('C'.$j, $val['member_no']);
						$this->excel->getActiveSheet()->setCellValue('D'.$j, $val['member_name']);
						$this->excel->getActiveSheet()->setCellValue('E'.$j, $val['member_address']);
						$this->excel->getActiveSheet()->setCellValue('F'.$j, $val['member_place_of_birth']);
						$this->excel->getActiveSheet()->setCellValue('G'.$j, tgltoview($val['member_date_of_birth']));
						$this->excel->getActiveSheet()->setCellValue('H'.$j, $membergender[$val['member_gender']]);
						$this->excel->getActiveSheet()->setCellValue('I'.$j, $memberstatus[$val['member_status']]);
						$this->excel->getActiveSheet()->setCellValue('J'.$j, $memberstatusaktif[$val['member_active_status']]);
						$this->excel->getActiveSheet()->setCellValue('K'.$j, $val['member_phone']);
						if($val['partner_working_type'] == 1){
							$partner_working_type = 'Karyawan';
						}elseif($val['partner_working_type'] == 2){
							$partner_working_type = 'Profesional';
						}elseif($val['partner_working_type'] == 3){
							$partner_working_type = 'Non Karyawan';
						}else{
							$partner_working_type = '-';
						}
						$this->excel->getActiveSheet()->setCellValue('L'.$j, $partner_working_type);
						
						$this->excel->getActiveSheet()->setCellValue('M'.$j, $val['member_company_name']);
						$this->excel->getActiveSheet()->setCellValue('N'.$j, number_format($val['member_principal_savings'], 2));
						$this->excel->getActiveSheet()->setCellValue('O'.$j, number_format($val['member_special_savings'], 2));
						$this->excel->getActiveSheet()->setCellValue('P'.$j, number_format($val['member_mandatory_savings'], 2));	
			
						if($val['company_id'] == 0){
							$count_member_entership = $count_member_entership + 1;
						}

						if($val['member_gender']==0){
							$count_member_female += 1;
						}else{
							$count_member_male += 1;
						}

						if($val['member_active_status']==0){
							$count_member_active += 1;
						}else{
							$count_member_nonactive += 1;
						}
					}else{
						continue;
					}
					$j++;
				}
				
				$this->excel->getActiveSheet()->getStyle('V3:X5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('V3:V5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('W3:W5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('X3:X5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->setCellValue('X4', $count_member_male);
				$this->excel->getActiveSheet()->setCellValue('X5', $count_member_female);
				
				$this->excel->getActiveSheet()->getStyle('Z3:AB5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$this->excel->getActiveSheet()->getStyle('Z3:Z5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$this->excel->getActiveSheet()->getStyle('W3:W5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$this->excel->getActiveSheet()->getStyle('AB3:AB5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$this->excel->getActiveSheet()->setCellValue('AB4', $count_member_active);
				$this->excel->getActiveSheet()->setCellValue('AB5', $count_member_nonactive);


				$filename='Master Data Anggota.xls';
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'.$filename.'"');
				header('Cache-Control: max-age=0');
							
				$objWriter = IOFactory::createWriter($this->excel, 'Excel5');  
				if (ob_get_length() > 0){
					ob_end_clean();
				}
				$objWriter->save('php://output');
			}else{
				echo "Maaf data yang di eksport tidak ada !";
			}
		}

		public function addCoreMemberUtility(){
			$unique = $this->session->userdata('unique');
			$auth 	= $this->session->userdata('auth');
			$token 	= $this->session->userdata('coremembertoken-'.$unique['unique']);

			if(empty($token)){
				$member_token = md5(date('Y-m-d H:i:s'));
				$this->session->set_userdata('coremembertoken-'.$unique['unique'], $member_token);
			}

			$data['main_view']['coreprovince']		= create_double($this->CoreMember_model->getCoreProvince(),'province_id', 'province_name');
			$data['main_view']['coreidentity']		= create_double($this->CoreMember_model->getCoreIdentity(),'identity_id', 'identity_name');
			$data['main_view']['membergender']		= $this->configuration->MemberGender();	
			$data['main_view']['membercharacter']	= $this->configuration->MemberCharacter();	
			$data['main_view']['memberidentity']	= $this->configuration->MemberIdentity();	
			$data['main_view']['content']			= 'CoreMember/FormAddCoreMemberUtility_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processAddCoreMemberUtility(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');

			$member_password = rand();

			$data = array(
				'branch_id'					=> $auth['branch_id'],
				'member_no'					=> $this->input->post('member_no', true),
				'member_name'				=> $this->input->post('member_name', true),
				'member_gender'				=> $this->input->post('member_gender', true),
				'province_id'				=> $this->input->post('province_id', true),
				'city_id'					=> $this->input->post('city_id', true),
				'kecamatan_id'				=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'				=> $this->input->post('kelurahan_id', true),
				'dusun_id'					=> $this->input->post('dusun_id', true),
				'member_job'				=> $this->input->post('member_job', true),
				'member_identity'			=> $this->input->post('member_identity', true),
				'member_place_of_birth'		=> $this->input->post('member_place_of_birth', true),
				'member_date_of_birth'		=> tgltodb($this->input->post('member_date_of_birth', true)),
				'member_address'			=> $this->input->post('member_address', true),
				'member_phone'				=> $this->input->post('member_phone', true),
				'member_identity_no'		=> $this->input->post('member_identity_no', true),
				'member_character'			=> $this->input->post('member_character', true),
				'member_postal_code'		=> $this->input->post('member_postal_code', true),
				'member_mother'				=> $this->input->post('member_mother', true),
				'member_token'				=> $this->input->post('member_token', true),
				'member_password_default'	=> $member_password,
				'member_password'			=> md5($member_password),
				'member_register_date'		=> date('Y-m-d H:i:s'),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);
			
			$this->form_validation->set_rules('member_name', 'Nama', 'required');
			$this->form_validation->set_rules('member_place_of_birth', 'Tempat Lahir', 'required');
			$this->form_validation->set_rules('member_date_of_birth', 'Tanggal Lahir', 'required');
			$this->form_validation->set_rules('member_address', 'Alamat', 'required');
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('member_phone', 'Nomor Telp', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			$this->form_validation->set_rules('kelurahan_id', 'Kelurahan', 'required');
			$this->form_validation->set_rules('dusun_id', 'Dusun', 'required');

			$membertoken = $this->CoreMember_model->getMemberToken($data['member_token']);

			
			if($this->form_validation->run()==true){
				if($membertoken->num_rows() == 0){
					if($this->CoreMember_model->insertCoreMember($data)){
						$auth = $this->session->userdata('auth');
						$this->fungsi->set_log($auth['user_id'], $auth['username'],'1003','Application.CoreMember.processAddCoreMember',$auth['user_id'],'Add New Member');
						$msg = "<div class='alert alert-success alert-dismissable'>  
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Sukses
								</div> ";

						$unique 	= $this->session->userdata('unique');
						$this->session->unset_userdata('addCoreMember-'.$unique['unique']);
						$this->session->unset_userdata('coremembertoken-'.$unique['unique']);
						$this->session->set_userdata('message',$msg);
						redirect('member');
					}else{
						$this->session->set_userdata('addcoremember',$data);
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Anggota Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('member');
					}
				} else {
					$this->session->set_userdata('addcoremember',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Anggota Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('member/add-utility');
				}
			}else{
				$this->session->set_userdata('addcoremember',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('member/add');
			}
		}
	}
?>
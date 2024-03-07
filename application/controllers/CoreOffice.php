<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CoreOffice extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('CoreOffice_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
			$sesi 	= $this->session->userdata('unique');

			$this->session->unset_userdata('addCoreOffice-'.$sesi['unique']);
			$this->session->unset_userdata('addcoredusun-'.$sesi['unique']);
			$this->session->unset_userdata('editCoreOffice-'.$sesi['unique']);
			$this->session->unset_userdata('editcoredusun-'.$sesi['unique']);
			$this->session->unset_userdata('editcoredusunawal-'.$sesi['unique']);


			$data['main_view']['coreoffice']		= $this->CoreOffice_model->getDataCoreOffice();
			$data['main_view']['content']			= 'CoreOffice/ListCoreOffice_view';
			$this->load->view('MainPage_view',$data);
		}
		
		public function addCoreOffice(){
			$data['main_view']['corebranch']		= create_double($this->CoreOffice_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['corecity']			= create_double($this->CoreOffice_model->getCoreCity(),'city_id','city_name');
			$data['main_view']['content']			= 'CoreOffice/FormAddCoreOffice_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addCoreOffice-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('addCoreOffice-'.$unique['unique'],$sessions);
		}

		public function getCoreKecamatan(){
			$city_id 		= $this->uri->segment(3);
			
			$item = $this->CoreOffice_model->getCoreKecamatan($city_id);
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
			
			$item = $this->CoreOffice_model->getCoreKelurahan($kecamatan_id);
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
			
			$item = $this->CoreOffice_model->getCoreDusun($kelurahan_id);
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

		public function addCoreDusun(){
			$date 		= date('YmdHis');

			$data = array (
				'city_id'				=> $this->input->post('city_id', true),
				'kecamatan_id'			=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'			=> $this->input->post('kelurahan_id', true),
				// 'dusun_id'				=> $this->input->post('dusun_id', true),
			);

			if(empty($data['kecamatan_id']) || $data['kecamatan_id'] == ''){
				print_r("kecamatan kosong");
				$corekecamatan 			= $this->CoreOffice_model->getCoreKecamatan($data['city_id']);

				foreach ($corekecamatan as $kKec => $vKec) {
					$corekelurahan 		= $this->CoreOffice_model->getCoreKelurahan($vKec['kecamatan_id']);

					foreach ($corekelurahan as $key => $vKel) {
						// $coredusun 		= $this->CoreOffice_model->getCoreDusun($vKel['kelurahan_id']);

						// foreach ($coredusun as $kDsn => $vDsn) {
							$datakelurahan[] = array(
								'record_id'				=> $vKel['kelurahan_id'].$date,
								'kelurahan_id'			=> $vKel['kelurahan_id'],
								// 'dusun_id'				=> $vDsn['dusun_id'],
							);
						// }
					}
				}
			} else if(empty($data['kelurahan_id']) || $data['kelurahan_id'] == ''){
				$corekelurahan 		= $this->CoreOffice_model->getCoreKelurahan($data['kecamatan_id']);

				foreach ($corekelurahan as $key => $vKel) {
					// $coredusun 		= $this->CoreOffice_model->getCoreDusun($vKel['kelurahan_id']);

					// foreach ($coredusun as $kDsn => $vDsn) {
						$datakelurahan[] = array(
							'record_id'				=> $vKel['kelurahan_id'].$date,
							'kelurahan_id'			=> $vKel['kelurahan_id'],
							// 'dusun_id'				=> $vDsn['dusun_id'],
						);
					// }
				}
			} 
			// else if(empty($data['dusun_id']) || $data['dusun_id'] == ''){
			// 	$coredusun 		= $this->CoreOffice_model->getCoreDusun($data['kelurahan_id']);

			// 	foreach ($coredusun as $kDsn => $vDsn) {
			// 		$datadusun[] = array(
			// 			'record_id'				=> $vDsn['dusun_id'].$date,
			// 			'kelurahan_id'			=> $data['kelurahan_id'],
			// 			'dusun_id'				=> $vDsn['dusun_id'],
			// 		);
			// 	}
			// } 
			else if(!empty($data['kelurahan_id']) || $data['kelurahan_id'] != ''){
				$datakelurahan = array(
					'record_id'				=> $this->input->post('kelurahan_id', true).$date,
					'kelurahan_id'			=> $this->input->post('kelurahan_id', true),
					// 'dusun_id'				=> $this->input->post('dusun_id', true),
				);
			}

			// print_r($datadusun);exit;

			
			
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			
			if($this->form_validation->run()==true){
				$unique 			= $this->session->userdata('unique');
				$session_name 		= $this->input->post('session_name',true);
				$dataArrayHeader	= $this->session->userdata('addcoredusun-'.$unique['unique']);

				if(!empty($data['kelurahan_id']) || $data['kelurahan_id'] != ''){
					$dataArrayHeader[$datakelurahan['record_id']] = $datakelurahan;
				} else {
					$dataArrayHeader = $datakelurahan;
				}
				
				
				$this->session->set_userdata('addcoredusun-'.$unique['unique'],$dataArrayHeader);
				$sesi 	= $this->session->userdata('unique');
				$datadusun = $this->session->userdata('addCoreOffice-'.$sesi['unique']);
				
				$datakelurahan['record_id'] 			= '';
				$datakelurahan['kelurahan_id'] 			= '';
				// $datadusun['dusun_id'] 				= '';
				
				$this->session->set_userdata('addCoreOffice-'.$sesi['unique'],$datadusun);
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
			}
		}

		public function deleteCoreDusun(){
			$arrayBaru			= array();
			$var_to 			= $this->uri->segment(3);
			$session_name		= "addcoredusun-";
			$unique 			= $this->session->userdata('unique');
			$dataArrayHeader	= $this->session->userdata($session_name.$unique['unique']);
			$unique 			= $this->session->userdata('unique');
			
			foreach($dataArrayHeader as $key=>$val){
				if($key != $var_to){
					$arrayBaru[$key] = $val;
				}
			}
			
			$this->session->set_userdata('addcoredusun-'.$unique['unique'],$arrayBaru);
			
			redirect('office/add/');
		}
		
		public function processAddCoreOffice(){
			$auth 		= $this->session->userdata('auth');
			$sesi 		= $this->session->userdata('unique');
			$coredusun 	= $this->session->userdata('addcoredusun-'.$sesi['unique']);

			$data = array(
				'office_code'				=> $this->input->post('office_code', true),
				'office_name'				=> $this->input->post('office_name', true),
				'branch_id'					=> $this->input->post('branch_id', true),
				'created_id'				=> $auth['user_id'],
				'created_on'				=> date('Y-m-d H:i:s'),
			);

			$datauser = array (
				'username'			=> $data['office_name'],
				'password'			=> md5('123'),
				'user_group_id'		=> 2,
				'branch_id'			=> $data['branch_id'],
			);
			
			$this->form_validation->set_rules('office_name', 'Nama', 'required');
			$this->form_validation->set_rules('office_code', 'Kode', 'required');
			$this->form_validation->set_rules('branch_id', 'Cabang', 'required');

			
			if($this->form_validation->run()==true){
				if($this->CoreOffice_model->insertSystemUser($datauser)){
					$user_id 	= $this->CoreOffice_model->getUserID($datauser);

					$data = array(
						'user_id'					=> $user_id,
						'office_code'				=> $this->input->post('office_code', true),
						'office_name'				=> $this->input->post('office_name', true),
						'branch_id'					=> $this->input->post('branch_id', true),
						'created_id'				=> $auth['user_id'],
						'created_on'				=> date('Y-m-d H:i:s'),
					);

					if($this->CoreOffice_model->insertCoreOffice($data)){
						if(!empty($coredusun)){
							foreach ($coredusun as $key => $val) {
								$datadusun = array (
									'user_id'		=> $user_id,
									'kelurahan_id'	=> $val['kelurahan_id'],
									/*'dusun_id'		=> $val['dusun_id'],*/
									'created_id'	=> $auth['user_id'],
									'created_on'	=> date('Y-m-d H:i:s'),
								);

								if($this->CoreOffice_model->insertSystemUserDusun($datadusun)){
									$auth = $this->session->userdata('auth');
									$sesi = $this->session->userdata('unique');

									// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
									$msg = "<div class='alert alert-success alert-dismissable'>  
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Tambah Data Business Office (BO) Sukses
											</div> ";
									$this->session->unset_userdata('addCoreOffice-'.$sesi['unique']);
									$this->session->unset_userdata('addcoredusun-'.$sesi['unique']);
									$this->session->set_userdata('message',$msg);
									continue;
								} else {
									$msg = "<div class='alert alert-danger alert-dismissable'>
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Tambah Data Business Office (BO) Tidak Berhasil
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('office/add');
									break;
								}
							}
						}
					} else {
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Tambah Data Business Office (BO) Tidak Berhasil
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('office/add');
					}


					
					redirect('office/add');
				}else{
					$this->session->set_userdata('addcoreoffice',$data);
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Tambah Data Business Office (BO) Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('office/add');
				}
			}else{
				$this->session->set_userdata('addcoreoffice',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('office/add');
			}
		}
		
		public function editCoreOffice(){
			$coreoffice 							= $this->CoreOffice_model->getCoreOffice_Detail($this->uri->segment(3));
			$data['main_view']['coreoffice']		= $coreoffice;
			$data['main_view']['corebranch']		= create_double($this->CoreOffice_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['corecity']			= create_double($this->CoreOffice_model->getCoreCity(),'city_id','city_name');

			$datadusun								= $this->session->userdata('editcoredusunawal-'.$unique['unique']);

			if (empty($datadusun)){
				$coredusun							= $this->CoreOffice_model->getSystemUserDusun($coreoffice['user_id']);

				foreach ($coredusun  as $key => $val) {
					$datauserdusun = array(
						'user_dusun_id'				=> $val['user_dusun_id'],
						'user_id' 					=> $val['user_id'],
						'kelurahan_id'				=> $val['kelurahan_id'],
						/*'dusun_id'					=> $val['dusun_id'],*/
					);

					$unique 			= $this->session->userdata('unique');
					$session_name 		= $this->input->post('session_name',true);
					$dataArrayHeader	= $this->session->userdata('editcoredusun-'.$unique['unique']);

					$dataArrayHeader[$datauserdusun['dusun_id']] = $datauserdusun;
					
					$this->session->set_userdata('editcoredusun-'.$unique['unique'],$dataArrayHeader);
					$this->session->set_userdata('editcoredusunawal-'.$unique['unique'],$dataArrayHeader);
				}
			}
			$data['main_view']['content']			= 'CoreOffice/FormEditCoreOffice_view';
			$this->load->view('MainPage_view',$data);
		}

		public function function_elements_edit(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('editCoreOffice-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('editCoreOffice-'.$unique['unique'],$sessions);
		}

		public function editCoreDusun(){
			$date = date('YmdHis');

			$data = array (
				'city_id'				=> $this->input->post('city_id', true),
				'kecamatan_id'			=> $this->input->post('kecamatan_id', true),
				'kelurahan_id'			=> $this->input->post('kelurahan_id', true),
				// 'dusun_id'				=> $this->input->post('dusun_id', true),
				'user_id'				=> $this->input->post('user_id', true),
			);

			if(empty($data['kecamatan_id']) || $data['kecamatan_id'] == ''){
				print_r("kecamatan kosong");
				$corekecamatan 			= $this->CoreOffice_model->getCoreKecamatan($data['city_id']);

				foreach ($corekecamatan as $kKec => $vKec) {
					$corekelurahan 		= $this->CoreOffice_model->getCoreKelurahan($vKec['kecamatan_id']);

					foreach ($corekelurahan as $key => $vKel) {
						// $coredusun 		= $this->CoreOffice_model->getCoreDusun($vKel['kelurahan_id']);

						// foreach ($coredusun as $kDsn => $vDsn) {
							$datakelurahan[] = array(
								'user_dusun_id'			=> $vKel['kelurahan_id'].$date,
								'user_id'				=> $data['user_id'],
								'kelurahan_id'			=> $vKel['kelurahan_id'],
								// 'dusun_id'				=> $vDsn['dusun_id'],
							);
						// }
					}
				}
			} else if(empty($data['kelurahan_id']) || $data['kelurahan_id'] == ''){
				$corekelurahan 		= $this->CoreOffice_model->getCoreKelurahan($data['kecamatan_id']);

				foreach ($corekelurahan as $key => $vKel) {
					// $coredusun 		= $this->CoreOffice_model->getCoreDusun($vKel['kelurahan_id']);

					// foreach ($coredusun as $kDsn => $vDsn) {
						$datakelurahan[] = array(
							'user_dusun_id'			=> $vKel['kelurahan_id'].$date,
							'user_id'				=> $data['user_id'],
							'kelurahan_id'			=> $vKel['kelurahan_id'],
							// 'dusun_id'				=> $vDsn['dusun_id'],
						);
					// }
				}
			} 
			// else if(empty($data['dusun_id']) || $data['dusun_id'] == ''){
			// 	$coredusun 		= $this->CoreOffice_model->getCoreDusun($data['kelurahan_id']);

			// 	foreach ($coredusun as $kDsn => $vDsn) {
			// 		$datadusun[] = array(
			// 			'user_dusun_id'			=> $vDsn['dusun_id'].$date,
			// 			'user_id'				=> $data['user_id'],
			// 			'kelurahan_id'			=> $data['kelurahan_id'],
			// 			'dusun_id'				=> $vDsn['dusun_id'],
			// 		);
			// 	}
			// }
			 else if(!empty($data['kelurahan_id']) || $data['kelurahan_id'] != ''){
				$datakelurahan = array(
					'user_dusun_id'			=> $this->input->post('kelurahan_id', true).$date,
					'user_id'				=> $this->input->post('user_id', true),
					'kelurahan_id'			=> $this->input->post('kelurahan_id', true),
					// 'dusun_id'				=> $this->input->post('dusun_id', true),
				);
			}
			
			$this->form_validation->set_rules('city_id', 'Kabupaten', 'required');
			$this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
			
			if($this->form_validation->run()==true){
				$unique 			= $this->session->userdata('unique');
				$session_name 		= $this->input->post('session_name',true);
				$dataArrayHeader	= $this->session->userdata('editcoredusun-'.$unique['unique']);

				if(!empty($data['kelurahan_id']) || $data['kelurahan_id'] != ''){
					$dataArrayHeader[$datakelurahan['kelurahan_id']] = $datakelurahan;
				} else {
					$dataArrayHeader = $datakelurahan;
				}
				
				$this->session->set_userdata('editcoredusun-'.$unique['unique'],$dataArrayHeader);
				$sesi 	= $this->session->userdata('unique');
				$datakelurahan = $this->session->userdata('editCoreOffice-'.$sesi['unique']);
				
				$datakelurahan['user_dusun_id'] 		= '';
				$datakelurahan['user_id'] 				= '';
				$datakelurahan['kelurahan_id'] 			= '';
				// $datadusun['dusun_id'] 				= '';
				
				$this->session->set_userdata('editCoreOffice-'.$sesi['unique'],$datadusun);
			}else{
				$msg = validation_errors("<div class='alert alert-danger'>", "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button></div>");
				$this->session->set_userdata('message',$msg);
			}
		}

		public function deleteEditCoreDusun(){
			$arrayBaru			= array();
			$office_id 			= $this->uri->segment(3);
			$var_to 			= $this->uri->segment(4);
			$session_name		= "editcoredusun-";
			$unique 			= $this->session->userdata('unique');
			$dataArrayHeader	= $this->session->userdata($session_name.$unique['unique']);
			$unique 			= $this->session->userdata('unique');
			
			foreach($dataArrayHeader as $key=>$val){
				if($key != $var_to){
					$arrayBaru[$key] = $val;
				}
			}
			
			$this->session->set_userdata('editcoredusun-'.$unique['unique'],$arrayBaru);
			
			redirect('office/edit/'.$office_id);
		}
		
		public function processEditCoreOffice(){
			$auth		= $this->session->userdata('auth');
			$sesi 		= $this->session->userdata('unique');
			$coredusun 	= $this->session->userdata('editcoredusun-'.$sesi['unique']);

			$data = array(
				'office_id'					=> $this->input->post('office_id', true),
				'user_id'					=> $this->input->post('user_id', true),
				'office_name'				=> $this->input->post('office_name', true),
				'office_code'				=> $this->input->post('office_code', true),
				'branch_id'					=> $this->input->post('branch_id', true),
			);
			
			$this->form_validation->set_rules('office_name', 'Nama', 'required');
			$this->form_validation->set_rules('office_code', 'Kode', 'required');
			$this->form_validation->set_rules('branch_id', 'Cabang', 'required');
			
			if($this->form_validation->run()==true){
				if($this->CoreOffice_model->updateCoreOffice($data)){
					if(!empty($coredusun)){
						if($this->CoreOffice_model->deleteSystemUserDusun($data['user_id'])){
							foreach ($coredusun as $key => $val) {
								$datadusun = array (
									'user_id'		=> $data['user_id'],
									'kelurahan_id'	=> $val['kelurahan_id'],
									'dusun_id'		=> $val['dusun_id'],
									'created_id'	=> $auth['user_id'],
									'created_on'	=> date('Y-m-d H:i:s'),
								);

								if($this->CoreOffice_model->insertSystemUserDusun($datadusun)){
									$auth = $this->session->userdata('auth');
									$sesi = $this->session->userdata('unique');

									// $this->fungsi->set_log($auth['username'],'1003','Application.machine.processAddmachine',$auth['username'],'Add New machine');
									$msg = "<div class='alert alert-success alert-dismissable'>  
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Edit Data Business Office (BO) Sukses
											</div> ";
									$this->session->unset_userdata('editCoreOffice-'.$sesi['unique']);
									$this->session->unset_userdata('editcoredusun-'.$sesi['unique']);
									$this->session->unset_userdata('editcoredusunawal-'.$sesi['unique']);
									$this->session->set_userdata('message',$msg);
									continue;
								} else {
									$msg = "<div class='alert alert-danger alert-dismissable'>
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Edit Data Business Office (BO) Tidak Berhasil
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('office/edit/'.$data['office_id']);
									break;
								}
							}
						}
					}
					redirect('office');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'> 
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>
								Edit Business Office (BO) Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('office/edit/'.$data['office_id']);
				}
			}else{
				$this->session->set_userdata('editmachine',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('office/edit/'.$data['office_id']);
			}				
		}
		
		public function deleteCoreOffice(){
			if($this->CoreOffice_model->deleteCoreOffice($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['suppliername'],'1005','Application.machine.delete',$auth['suppliername'],'Delete machine');
				$msg = "<div class='alert alert-success alert-dismissable'>                 
							Hapus Data Business Office (BO) Sukses
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('office');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>                
							Hapus Data Business Office (BO) Tidak Berhasil
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('office');
			}
		}
	}
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class User extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('MainPage_model');
			$this->load->model('User_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->library('fungsi');
			$this->load->database('default');
		}
		
		public function index(){
			$this->lists();
		}
		
		public function lists(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['user']		= $this->User_model->get_list($auth['branch_status'], $auth['branch_id']);
			$data['main_view']['content']	= 'User/ListUser_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function Add(){
			$auth = $this->session->userdata('auth');
			// $branch_status = $auth['bra']
			$data['main_view']['branchstatus']		= $this->configuration->BranchStatus();
			$data['main_view']['corebranch']		= create_double($this->User_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['group']				= create_double($this->User_model->getGroup($auth['branch_status']),'user_group_level','user_group_name');
			$data['main_view']['content']			= 'User/FormAddUser_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function processAdduser(){
			$auth = $this->session->userdata('auth');

			if($auth['branch_status'] == 1){
				$branch_id = $this->input->post('branch_id',true);
			} else {
				$branch_id = $auth['branch_id'];
			}

			$data = array(
				'username' 				=> str_replace(";","",$this->input->post('username',true)),
				'password' 				=> md5($this->input->post('password',true)),
				'user_group_id'			=> $this->input->post('user_group_id',true),
				'branch_id'				=> $branch_id,
				'branch_status'			=> $this->input->post('branch_status',true),
				// 'database' 				=> $this->input->post('database',true),
				// 'customer_company_code' => $this->input->post('customer_company_code',true),
				'log_stat'		=> 'off'
			);
			$this->form_validation->set_rules('username', 'username', 'required|is_unique[system_user.username]');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('user_group_id', 'Group', 'required');
			if($this->form_validation->run()==true){
				if($this->User_model->saveNewuser($data)){
					$auth = $this->session->userdata('auth');
					// $this->fungsi->set_log($auth['username'],'1003','Application.user.processAdduser',$auth['username'],'Add New user Account');
					$msg = "<div class='alert alert-success alert-dismissable'>                  								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>            
								Add Data user Successfully
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('user/add');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>               
								Add Data user UnSuccessful
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('user/add');
				}
			}else{
				$data['password']='';
				$this->session->set_userdata('Adduser',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('user/add');
			}
		}
		
		function Edit(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['group']		= create_double($this->User_model->getGroup($auth['branch_status']),'user_group_level','user_group_name');
			$data['main_view']['result']	= $this->User_model->getDetail($this->uri->segment(3));
			$data['main_view']['content']	= 'User/FormEditUser_view';
			$this->load->view('MainPage_view',$data);
		}
		
		function processEdituser(){
			$old_avatar 	= $this->input->post('old_avatar',true);
			$last_username 	= str_replace(";","",$this->input->post('last_username',true));
			$password 		= $this->input->post('password',true);
			$repassword 	= $this->input->post('re_password',true);
			
			$data = array(
				'username' 		=> str_replace(";","",$this->input->post('username1',true)),
				'user_group_id' => $this->input->post('user_group_id',true),
				'log_stat'		=> $this->input->post('log_stat',true)
			);
			
			$old_data	= $this->User_model->getDetail($last_username);
			$this->form_validation->set_rules('username', 'username', 'required');
			$this->form_validation->set_rules('user_group_id', 'Group', 'required');
			if($this->form_validation->run()==true){
				if($this->User_model->cekuserNameExist($data['username']) || $last_username==$data['username']){
					if($password!=""){
						$data['password'] = $password;
						if($data['password']!=$repassword){
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
									Password do not Match!
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user/edit/'.$last_username);
							// break;
						}else{
							$data['password'] = md5($data['password']);
						}
					}
						if($this->User_model->saveEdituser($data,$last_username)){
							$auth = $this->session->userdata('auth');
							// $this->fungsi->set_log($auth['username'],'1004','Application.user.processEdituser',$auth['username'],'Edit user Account');
							// $this->fungsi->set_change_log($old_data,$data,$auth['username'],$data['username']);
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Edit Data user Successfully
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user/edit/'.$data['username']);
						}else{
							$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Edit Data user UnSuccessful
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user/edit/'.$last_username);
						}
					
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								username already exist !!!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('user/edit/'.$last_username);
				}
			}else{
				$data['password']='';
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('user/edit/'.$last_username);
			}
		}

		public function changePassword(){
			$auth = $this->session->userdata('auth');

			$data['main_view']['result']	= $this->User_model->getDetail($auth['username']);
			$data['main_view']['content']			= 'FormChangePassword_view';
			$this->load->view('MainPage_view',$data);

		}

		function processChangePassword(){
			$auth = $this->session->userdata('auth');
			$last_username 	= str_replace(";","",$this->input->post('last_username',true));
			$password 		= $this->input->post('password',true);
			$repassword 	= $this->input->post('re_password',true);

			if($password!=""){
				if($password!=$repassword){
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Password do not Match!
						</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('change-password');
				}else{
					$data = array(
						'password' 		=> md5($password),
						'password_date' => date('Y-m-d'),
					);
				}
			}
			
			$old_data	= $this->User_model->getDetail($last_username);
			$this->form_validation->set_rules('password', 'New Password', 'required');
			$this->form_validation->set_rules('re_password', 'Confirm New Password', 'required');
			if($this->form_validation->run()==true){
				if($this->User_model->saveEdituser($data,$last_username)){
					$this->session->unset_userdata('message_password');
					$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Ganti Password Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('change-password/'.$data['username']);
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
					<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								Ganti Password Tidak Berhasil
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('change-password');
				}
			}else{
				$data['password']='';
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('change-password');
			}
		}
		
		function defaultpassword(){
			$result = $this->User_model->getDetail($this->uri->segment(3));
			$pass = $this->User_model->getPassword();
			// $old_avatar 	= $result['avatar'];
			$last_username 	= $result['username'];
			$password = $pass;
			$repassword = $pass;
			$data = array(
				'username' 		=> $result['username'],
				'user_group_id' => $result['user_group_id'],
				'branch_id' 	=> $result['branch_id'],
				// 'hak_akses' 	=> $result['hak_akses'],
				'log_stat'		=> $result['log_stat']
			);
			
			// if($this->form_validation->run()==true){
				// if($this->User_model->cekuserNameExist($data['username']) || $last_username==$data['username']){
					if($password!=""){
						$data['password'] = $password;
						if($data['password']!=$repassword){
							$msg = "<div class='alert alert-error'>                
									Password do not Match!
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user');
							// break;
						}else{
							$data['password'] = $data['password'];
						}
					}
						if($this->User_model->saveEdituser($data,$last_username)){
							$auth = $this->session->userdata('auth');
							// $this->fungsi->set_log($auth['username'],'1004','Application.user.processEdituser',$auth['username'],'Edit user Account');
							$msg = "<div class='alert alert-success'>                
										Reset Password Successfully
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user');
						}else{
							$msg = "<div class='alert alert-error'>                
										Reset Password UnSuccessful
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user');
						}
					
				// }else{
					// $msg = "<div class='alert alert-error'>                
								// username already exist !!!
							// </div> ";
					// $this->session->set_userdata('message',$msg);
					// redirect('user/edit/'.$last_username);
				// }
			// }else{
				// $data['password']='';
				// $msg = validation_errors("<div class='alert alert-error'>", '</div>');
				// $this->session->set_userdata('message',$msg);
				// redirect('user/edit/'.$last_username);
			// }
		}
		
		function delete(){
			if($this->User_model->delete($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				// $this->fungsi->set_log($auth['username'],'1005','Application.user.delete',$auth['username'],'Delete user Account');
				$msg = "<div class='alert alert-success alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Delete Data user Successfully
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('user');
			}else{
				$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
							Delete Data user UnSuccessful
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('user');
			}
		}
		
		function test(){
			echo $this->User_model->isThisMenuInGroup('1','99');
		}
	}
?>
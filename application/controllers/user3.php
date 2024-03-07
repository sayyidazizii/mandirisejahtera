<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class user extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('user_model');
			$this->load->helper('sistem');
			$this->load->library('fungsi');
		}
		
		public function index(){
			$data['main_view']['user']		= $this->user_model->get_list();
			$data['main_view']['content']	= 'User/listUser_view';
			$this->load->view('mainpage_view',$data);
			// $this->lists();
		}
		
		public function lists(){
			$data['main_view']['user']		= $this->user_model->get_list();
			$data['main_view']['content']	= 'user/listuser_view';
			$this->load->view('mainpage_view',$data);
		}
		
		function Add(){
			$data['main_view']['group']		= create_double($this->user_model->getGroup(),'user_group_level','user_group_name');
			$data['main_view']['content']	= 'User/formAddUser_view';
			$this->load->view('mainpage_view',$data);
		}
		
		function processAdduser(){
			$data = array(
				'username' 		=> str_replace(";","",$this->input->post('username',true)),
				'password' 		=> md5($this->input->post('password',true)),
				'user_group_id' => $this->input->post('user_group_id',true),
				'log_stat'		=> 'off'
			);
			$this->form_validation->set_rules('username', 'username', 'required|is_unique[system_user.username]|filterspecialchar');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('user_group_id', 'Group', 'required');
			if($this->form_validation->run()==true){
				if($this->user_model->saveNewuser($data)){
					$auth = $this->session->userdata('auth');
					$this->fungsi->set_log($auth['username'],'1003','Application.user.processAdduser',$auth['username'],'Add New user Account');
					$msg = "<div class='alert alert-success alert-dismissable'>                  								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>            
								Add Data user Successfully
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('user/Add');
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>               
								Add Data user UnSuccessful
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('user/Add');
				}
			}else{
				$data['password']='';
				$this->session->set_userdata('Adduser',$data);
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('user/Add');
			}
		}
		
		function Edit(){
			$data['main_view']['group']		= create_double($this->user_model->getGroup(),'user_group_level','user_group_name');
			$data['main_view']['result']	= $this->user_model->getDetail($this->uri->segment(3));
			$data['main_view']['content']	= 'User/formEditUser_view';
			$this->load->view('mainpage_view',$data);
		}
		
		function processEdituser(){
			$old_avatar 	= $this->input->post('old_avatar',true);
			$last_username 	= str_replace(";","",$this->input->post('last_username',true));
			$password 		= $this->input->post('password',true);
			$repassword 	= $this->input->post('re_password',true);
			
			$data = array(
				'username' 		=> str_replace(";","",$this->input->post('username',true)),
				'user_group_id' => $this->input->post('user_group_id',true),
				'log_stat'		=> $this->input->post('log_stat',true)
			);
			
			$old_data	= $this->user_model->getDetail($last_username);
			$this->form_validation->set_rules('username', 'username', 'required');
			$this->form_validation->set_rules('user_group_id', 'Group', 'required');
			if($this->form_validation->run()==true){
				if($this->user_model->cekuserNameExist($data['username']) || $last_username==$data['username']){
					if($password!=""){
						$data['password'] = $password;
						if($data['password']!=$repassword){
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
									Password do not Match!
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user/Edit/'.$last_username);
							break;
						}else{
							$data['password'] = md5($data['password']);
						}
					}
						if($this->user_model->saveEdituser($data,$last_username)){
							$auth = $this->session->userdata('auth');
							$this->fungsi->set_log($auth['username'],'1004','Application.user.processEdituser',$auth['username'],'Edit user Account');
							$this->fungsi->set_change_log($old_data,$data,$auth['username'],$data['username']);
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Edit Data user Successfully
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user/Edit/'.$data['username']);
						}else{
							$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
										Edit Data user UnSuccessful
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('user/Edit/'.$last_username);
						}
					
				}else{
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>                
								username already exist !!!
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('user/Edit/'.$last_username);
				}
			}else{
				$data['password']='';
				$msg = validation_errors("<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>", '</div>');
				$this->session->set_userdata('message',$msg);
				redirect('user/Edit/'.$last_username);
			}
		}
		
		function delete(){
			if($this->user_model->delete($this->uri->segment(3))){
				$auth = $this->session->userdata('auth');
				$this->fungsi->set_log($auth['username'],'1005','Application.user.delete',$auth['username'],'Delete user Account');
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
			echo $this->user_model->isThisMenuInGroup('1','99');
		}
	}
?>
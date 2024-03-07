<?php defined('BASEPATH') OR exit('No direct script access allowed');
	/*ini_set('memory_limit', '512M');*/

	Class AcctRecalculateEOM extends CI_Controller{
		public function __construct(){
			parent::__construct();


			$this->load->model('MainPage_model');
			$this->load->model('Connection_model');
			$this->load->model('AcctRecalculateEOM_model');
			$this->load->library('configuration');
			$this->load->helper('sistem');
			$this->load->database('default');
		}
		
		public function index(){
			$data['main_view']['monthlist']			= $this->configuration->Month();
			$data['main_view']['preferencecompany'] = $this->AcctRecalculateEOM_model->getPreferenceCompany();
			$data['main_view']['corebranch']		= create_double($this->AcctRecalculateEOM_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']			= 'AcctRecalculate/AcctRecalculate_view';
			$this->load->view('MainPage_view',$data);
		}

		public function processAcctRecalculateEOM(){
			$auth = $this->session->userdata('auth');

			// $data = array (
			// 	"month_period" 	    => $this->input->post('month_period',true),
			// 	"year_period" 		=> $this->input->post('year_period',true),
			// 	"branch_id" 		=> $this->input->post('branch_id',true),
			// );

			$data = array (
				"branch_id" 		=> $this->uri->segment(3),
				"month_period" 	    => $this->uri->segment(4),
				"year_period" 		=> $this->uri->segment(5),
				
			);

			// print_r($data);exit;

			if($auth['branch_status'] == 1){
				if(empty($data['branch_id'])){
					$data['branch_id'] = $auth['branch_id'];
				}
			} else {
				$data['branch_id'] = $auth['branch_id'];
			}

			$data_recalculate_log = array (
				"branch_id"			=> $data['branch_id'],
				"month_period"		=> $data['month_period'],
				"year_period"		=> $data['year_period'],
				"created_id"		=> $auth['user_id'],
				"created_on"		=> date("Y-m-d H:i:s"),
			);

			// print_r($data);exit;

			if($this->AcctRecalculateEOM_model->insertAcctRecalculateEOMLog($data_recalculate_log)){
				$acctaccount 	= $this->AcctRecalculateEOM_model->getAcctAccount();

				foreach ($acctaccount as $key => $val){
					
					$opening_balance_old 	= $this->AcctRecalculateEOM_model->getAccountOpeningBalance($val['account_id'], $data);
					
					if(empty($opening_balance_old)){
						$opening_balance_old = 0;
					}

					$total_mutation_in 		= $this->AcctRecalculateEOM_model->getTotalAccountIn($val['account_id'], $data);

					$total_mutation_out 	= $this->AcctRecalculateEOM_model->getTotalAccountOut($val['account_id'], $data);

					if(empty($total_mutation_in)){
						$total_mutation_in 	= 0;
					}

					if(empty($total_mutation_out)){
						$total_mutation_out = 0;
					}

					$last_balance 			= $total_mutation_in - $total_mutation_out;

					$opening_balance_new 	= $opening_balance_old + $last_balance;

					$next_month = $data['month_period'] + 1;

					if($next_month == 13){
						$next_month = '01';
						$next_year 	= $data['year_period'] + 1;
					} else {
						if($next_month < 10){
							$next_month = '0'.$next_month;
						} else {
							$next_month = $next_month;
						}
						
						$next_year 	= $data['year_period'];
					}

					$data_account_opening_balance[$key] = array (
						'branch_id'				=> $data['branch_id'],
						'account_id'			=> $val['account_id'],
						'month_period'			=> $next_month,
						'year_period'			=> $next_year,
						'opening_balance'		=> $opening_balance_new
					);

					$data_account_mutation[$key] = array (
						'branch_id'				=> $data['branch_id'],
						'account_id'			=> $val['account_id'],
						'month_period'			=> $data['month_period'],
						'year_period'			=> $data['year_period'],
						'mutation_in_amount'	=> $total_mutation_in,
						'mutation_out_amount'	=> $total_mutation_out,
						'last_balance'			=> $last_balance
					);

					// print_r($opening_balance_old);
					// print_r("<BR>");
					// print_r($data_account_mutation);
					// print_r("<BR>");
					// exit;
				}

				// print_r($data_account_opening_balance);exit;

				$check_data_account_opening_balance = $this->AcctRecalculateEOM_model->getCheckAcctAccountOpeningBalance($next_month, $next_year, $data['branch_id']);

				$check_data_account_mutation 		= $this->AcctRecalculateEOM_model->getCheckAcctAccountMutation($data);

				// print_r($check_data_account_mutation->num_rows());exit;

				$data_state = false;
				if($check_data_account_opening_balance->num_rows() == 0){
					if($check_data_account_mutation->num_rows() == 0){
						$data_state = true;	
					} else {
						if($this->AcctRecalculateEOM_model->deleteAcctAccountMutation($data)){
							$data_state = true;
						} else {
							$data_state = false;
						}
					}
					
				} else{
					if($this->AcctRecalculateEOM_model->deleteAcctAccountOpeningBalance($next_month, $next_year, $data['branch_id'])){
						if($check_data_account_mutation->num_rows() == 0){
							$data_state = true;	
						} else {
							if($this->AcctRecalculateEOM_model->deleteAcctAccountMutation($data)){
								$data_state = true;
							} else {
								$data_state = false;
							}
						}
					} else {
						$data_state = false;
					}
				}

				if($data_state == true){
					if($this->AcctRecalculateEOM_model->insertAcctAccountOpeningBalance($data_account_opening_balance)){
						if($this->AcctRecalculateEOM_model->insertAcctAccountMutation($data_account_mutation)){

							$acctprofitloss_top = $this->AcctRecalculateEOM_model->getAcctProfitLossReport_Top();

							foreach ($acctprofitloss_top as $kp => $vp){
								
								if($vp['report_type']	== 3){
									
									$accountamount 	= $this->AcctRecalculateEOM_model->getLastBalance_Account($vp['account_id'], $data['month_period'], $data['year_period'], $data['branch_id']);

									$account_amount[$vp['report_no']] = $accountamount;
								}

								// print_r($account_amount);

								if($vp['report_type'] == 5){
									if(!empty($vp['report_formula']) && !empty($vp['report_operator'])){
										$report_formula 	= explode('#', $vp['report_formula']);
										$report_operator 	= explode('#', $vp['report_operator']);

										$total_account_amount	= 0;
										for($i = 0; $i < count($report_formula); $i++){
											if($report_operator[$i] == '-'){
												if($total_account_amount == 0 ){
													$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
												} else {
													$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
												}
											} else if($report_operator[$i] == '+'){
												if($total_account_amount == 0){
													$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
												} else {
													$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
												}
											}
										}
									}
								}

								if($vp['report_type'] == 6){
									if(!empty($vp['report_formula']) && !empty($vp['report_operator'])){
										$report_formula 	= explode('#', $vp['report_formula']);
										$report_operator 	= explode('#', $vp['report_operator']);

									

										$grand_total_account_amount1	= 0;
										for($i = 0; $i < count($report_formula); $i++){
											if($report_operator[$i] == '-'){
												if($grand_total_account_amount1 == 0 ){
													$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
												} else {
													$grand_total_account_amount1 = $grand_total_account_amount1 - $account_amount[$report_formula[$i]];
												}
											} else if($report_operator[$i] == '+'){
												if($grand_total_account_amount1 == 0){
													$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
												} else {
													$grand_total_account_amount1 = $grand_total_account_amount1 + $account_amount[$report_formula[$i]];
												}
											}
										}
									}

								}
							}

							$acctprofitloss_bottom = $this->AcctRecalculateEOM_model->getAcctProfitLossReport_Bottom();

							foreach ($acctprofitloss_bottom as $kb => $vb){
								
								if($vb['report_type']	== 3){
									
									$accountamount 	= $this->AcctRecalculateEOM_model->getLastBalance_Account($vb['account_id'], $data['month_period'], $data['year_period'], $data['branch_id']);

									$account_amount[$vb['report_no']] = $accountamount;
								}

								if($vb['report_type'] == 5){
									if(!empty($vb['report_formula']) && !empty($vb['report_operator'])){
										$report_formula 	= explode('#', $vb['report_formula']);
										$report_operator 	= explode('#', $vb['report_operator']);

										$total_account_amount	= 0;
										for($i = 0; $i < count($report_formula); $i++){
											if($report_operator[$i] == '-'){
												if($total_account_amount == 0 ){
													$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
												} else {
													$total_account_amount = $total_account_amount - $account_amount[$report_formula[$i]];
												}
											} else if($report_operator[$i] == '+'){
												if($total_account_amount == 0){
													$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
												} else {
													$total_account_amount = $total_account_amount + $account_amount[$report_formula[$i]];
												}
											}
										}
									}
								}

								if($vb['report_type'] == 6){
									if(!empty($vb['report_formula']) && !empty($vb['report_operator'])){
										$report_formula 	= explode('#', $vb['report_formula']);
										$report_operator 	= explode('#', $vb['report_operator']);

										$grand_total_account_amount2	= 0;
										for($i = 0; $i < count($report_formula); $i++){
											if($report_operator[$i] == '-'){
												if($grand_total_account_amount2 == 0 ){
													$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
												} else {
													$grand_total_account_amount2 = $grand_total_account_amount2 - $account_amount[$report_formula[$i]];
												}
											} else if($report_operator[$i] == '+'){
												if($grand_total_account_amount2 == 0){
													$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
												} else {
													$grand_total_account_amount2 = $grand_total_account_amount2 + $account_amount[$report_formula[$i]];
												}
											}
										}
									}

								}
							}

							$profit_loss_amount = $grand_total_account_amount1 - $grand_total_account_amount2;

							// print_r($profit_loss_amount);exit;

							$data_profit_loss = array (
								"branch_id"				=> $data['branch_id'],
								"profit_loss_amount"	=> $profit_loss_amount,
								"month_period"			=> $data['month_period'],
								"year_period"			=> $data['year_period']

							);

							$chechk_profit_loss = $this->AcctRecalculateEOM_model->getCheckProfitLoss($data_profit_loss);

							if($chechk_profit_loss->num_rows() == 0){
								if($this->AcctRecalculateEOM_model->insertAcctProfitLoss($data_profit_loss)){
									
									$msg = "<div class='alert alert-success alert-dismissable'>
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Proses SHU Sukses
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('AcctProfitLossReportNew');
								} else {
									
									$msg = "<div class='alert alert-danger alert-dismissable'>
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Proses SHU Gagal
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('AcctProfitLossReportNew');
								}
							} else {
								if($this->AcctRecalculateEOM_model->deleteAcctProfitLoss($data_profit_loss)){
									if($this->AcctRecalculateEOM_model->insertAcctProfitLoss($data_profit_loss)){
										
										$msg = "<div class='alert alert-success alert-dismissable'>
												<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
													Proses SHU Sukses
												</div> ";
										$this->session->set_userdata('message',$msg);
										redirect('AcctProfitLossReportNew');
									} else {
										
										$msg = "<div class='alert alert-danger alert-dismissable'>
												<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
													Proses SHU Gagal
												</div> ";
										$this->session->set_userdata('message',$msg);
										redirect('AcctProfitLossReportNew');
									}
								} else {
									
									$msg = "<div class='alert alert-danger alert-dismissable'>
											<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
												Proses SHU Gagal
											</div> ";
									$this->session->set_userdata('message',$msg);
									redirect('AcctProfitLossReportNew');
								}
							}
						} else {
							
							$msg = "<div class='alert alert-danger alert-dismissable'>
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
										Proses SHU Gagal
									</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('AcctProfitLossReportNew');
						}

					} else {
						
						$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
									Proses SHU Gagal
								</div> ";
						$this->session->set_userdata('message',$msg);
						redirect('AcctProfitLossReportNew');
					}
				} else {
					
					$msg = "<div class='alert alert-danger alert-dismissable'>
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Proses SHU Gagal
							</div> ";
					$this->session->set_userdata('message',$msg);
					redirect('AcctProfitLossReportNew');
				}

			} else {
			
				$msg = "<div class='alert alert-danger alert-dismissable'>
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							Proses SHU Gagal
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('AcctProfitLossReportNew');
			}
		}
	}
?>
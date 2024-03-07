<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class Dashboard extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Dashboard_model');
			$this->load->model('MainPage_model');
			//$this->load->model('Connection_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->library('configuration');
			// $this->load->database('default');
		}
		
		public function index(){
			$auth 		= $this->session->userdata('auth');
			if(!empty($auth)){
				$max_date 	= date('t');
				$month 		= date('m');
				$year 		= date('Y');

				for ($i=1; $i <= $max_date ; $i++) { 
					if($i < 10){
						$i= '0'.$i;
					}
					$date = $year.'-'.$month.'-'.$i;
					$total_pencairan 	= $this->Dashboard_model->getAcctCreditsAccount($date);
					$total_credits	 	= $this->Dashboard_model->getAcctCreditsPayment_outstanding($date);
					$total_payment		= $this->Dashboard_model->getCreditsPayment($date);
					$total_akun_os		= $this->Dashboard_model->getHitungAkunOutstanding($date);
					$total_akun 		= $this->Dashboard_model->getHitungAccount($date);


					$total_credits_payment_amount = $this->Dashboard_model->getCreditsPaymentAmount($date);
					$total_credits_account_amount = $this->Dashboard_model->getCreditsAccountAmount($date);
					//print_r($total_payment); exit;
					$total_outstanding = $total_credits_account_amount - $total_credits_payment_amount ;

					if(empty($total_pencairan)){
						$total_pencairan 		= '0';
						
					}
					if(empty($total_outstanding)){
						$total_outstanding = '0';
					}
					if(empty($total_akun)){
					 	$total_akun = '0';
					}
					if(empty($total_akun_os)){
					 	$total_akun_os = '0';
					}

					$data_pencairan[$i]['year'] 			= $i;
					$data_pencairan[$i]['income']			= $total_pencairan;
					$data_pencairan[$i]['expenses']	 		= $total_outstanding;
					$data_pencairan[$i]['jumlah_akun']		= $total_akun;
					$data_pencairan[$i]['jumlah_akun_os']	= $total_akun_os;
				}
			//	print_r($total_pencairan); exit;
				$json_pencairan_month_to_date 	= json_encode($data_pencairan);
			
			
		function getWeek($week, $year) {
		  $dto = new DateTime();
		  $result['start']	 = $dto->setISODate($year, $week, 0)->format('Y-m-d');
		  $result['end']	 = $dto->setISODate($year, $week, 6)->format('Y-m-d');
 
		  return $result;
		}
				$bulanini = date('m');
				$akhirbulan = date('t');
				
			//	$minggu = $akhirbulan / 7;
				$now 	= date('Y-m-d');
				$signupdate=date('Y-m-01');
				$signupweek=date("W",strtotime($signupdate));
				$year=date("Y",strtotime($signupdate));
				$currentweek = date("W");

				$currentweek = $signupweek + 4;
                	
				$no=0;

                for($i=$signupweek;$i<=$currentweek;$i++) 
                {
                	$no++;
                	$result=getWeek($i,$year);
				   
				    $date = array (
					  	'start'		=> $result['start'],
					  	'end'		=> $result['end'],
					  );

				   // print_r($date);

				    $preference 	= $this->Dashboard_model->getPreferenceCollectibility();
            		$creditsaccount = $this->Dashboard_model->getCreditsAccount();

				    $total1 = 0;
				    $total2	= 0;
				    $total3	= 0;
				    $total4	= 0;
                	foreach ($creditsaccount as $key => $val) {
                		
						$date1 = date_create($date['end']);
						$date2 = date_create($val['credits_account_payment_date']);

						$interval    = $date1->diff($date2);
		    			$tunggakan   = $interval->days;

		    			foreach ($preference as $k => $v) {
							if($tunggakan >= $v['collectibility_bottom'] && $tunggakan <= $v['collectibility_top']){
								$collectibility = $v['collectibility_id'];
							} 
						}

					
						if($collectibility == 1){
							$total1 = $total1 + $val['credits_account_last_balance'];
						} else if($collectibility == 2){
							$total2 = $total2 + $val['credits_account_last_balance'];
						} else if($collectibility == 3){
							$total3 = $total3 + $val['credits_account_last_balance'];
						} else if($collectibility == 4){
							$total4 = $total4 + $val['credits_account_last_balance'];
						}
					
					}
	

                	if(empty($total1)){
						$total1 		= '0';
					}if(empty($total2)){
						$total2 		= '0';
					}if(empty($total3)){
						$total3			= '0';
					}if(empty($total4)){
						$total4 		= '0';
					}

					
                	$data_kolektibilitas[$i]['minggu']		= 'Minggu ke '.$no;
                	foreach ($preference as $k => $v) {
						if($v['collectibility_id'] == 1){
							$data_kolektibilitas[$i]['total1']		= $total1;
						}else if($v['collectibility_id'] == 2){
                			$data_kolektibilitas[$i]['total2']		= $total2;
                		}else if($v['collectibility_id'] == 3){
                			$data_kolektibilitas[$i]['total3']		= $total3;
                		}else if($v['collectibility_id'] == 4){
                			$data_kolektibilitas[$i]['total4']		= $total4;
                		}
                	}

                	//print_r("<BR>");
                }

                // exit;

                // print_r($data_kolektibilitas);exit;
				 

				$kolektibilitas 	= json_encode($data_kolektibilitas);
				
                $data['main_view']['dayname']							= $this->configuration->DayName();
				$data['main_view']['json_pencairan_month_to_date']		= $data_pencairan;
				$data['main_view']['kolektibilitas']					= $data_kolektibilitas;
				$data['main_view']['monthname']							= $this->configuration->Month();
			}

			
			$data['main_view']['content']							= 'Dashboard/Dashboard_view';
			$this->load->view('MainPage_view',$data);	
		}

		public function test(){
			$max_date 	= date('t');
				$month 		= date('m');
				$year 		= date('Y');

			for ($i=1; $i <= $max_date ; $i++) { 
				if($i < 10){
					$i= '0'.$i;
				}
				$date = $year.'-'.$month.'-'.$i;
				$total_credits	 	= $this->Dashboard_model->getAcctCreditsPayment_outstanding($date);
				$total_payment		= $this->Dashboard_model->getCreditsPayment($date);
				$total_pencairan 	= $this->Dashboard_model->getAcctCreditsAccount($date);

				$total_credits_payment_amount = $this->Dashboard_model->getCreditsPaymentAmount($date);
				$total_credits_account_amount = $this->Dashboard_model->getCreditsAccountAmount($date);
				//print_r($total_payment); exit;
				$total_outstanding = $total_credits_account_amount - $total_credits_payment_amount ;



				print_r('Hari '.$date);
				print_r("<BR>");
				print_r("<BR>");
				print_r('Pencairan '.$total_pencairan);
				
				print_r("<BR>");
				print_r("<BR>");
				print_r('Outstanding Payment '.$total_credits_payment_amount);
				print_r("<BR>");
				print_r("<BR>");
				print_r('Outstanding Credits '.$total_credits_account_amount);
				print_r("<BR>");
				print_r("<BR>");

			}
			exit;
		}

	}
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsPrintSavingsMonitor extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsPrintSavingsMonitor_model');
			$this->load->model('AcctSavingsAccount_model');
			$this->load->model('CoreMember_model');
			$this->load->helper('sistem');
			$this->load->helper('url');
			$this->load->database('default');
			$this->load->library('configuration');
			$this->load->library('fungsi');
			$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		}
		
		public function index(){
		}

		public function MonitorSavingsMutation(){
			$sesi	= 	$this->session->userdata('filter-acctsavingsmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			// print_r($sesi);exit;

			$savings_account_id = $this->uri->segment(3);
			if($savings_account_id == ''){
				$savings_account_id = $sesi['savings_account_id'];
			}

			$data['main_view']['acctsavingsaccount']			= $this->AcctSavingsPrintSavingsMonitor_model->getAcctSavingsAccount_Detail($savings_account_id);
			$data['main_view']['acctsavingsaccountdetail']		= $this->AcctSavingsPrintSavingsMonitor_model->getAcctSavingsAccountDetail($savings_account_id, $sesi['start_date'], $sesi['end_date']);		

			$data['main_view']['content']				= 'AcctSavingsPrintSavingsMonitor/ListAcctSavingsPrintSavingsMonitor_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date"			=> tgltodb($this->input->post('start_date',true)),
				"end_date"				=> tgltodb($this->input->post('end_date',true)),
				"savings_account_id"	=> $this->input->post('savings_account_id', true),
			);

			$this->session->set_userdata('filter-acctsavingsmonitor',$data);
			redirect('savings-print-savings-monitor/monitor-savings-mutation');
		}

		public function getListAcctSavingsAccount(){
			$auth = $this->session->userdata('auth');
			$list = $this->AcctSavingsAccount_model->get_datatables($auth['branch_id']);
	        $data = array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'savings-print-savings-monitor/monitor-savings-mutation/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all($auth['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered($auth['branch_id']),
	                        "data" => $data,
	                );
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

		public function reset_search(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-acctsavingsmonitor');
			redirect('savings-print-savings-monitor/monitor-savings-mutation');
		}

		public function processPrinting(){
			$auth = $this->session->userdata('auth');
			$sesi	= 	$this->session->userdata('filter-acctsavingsmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			// print_r($sesi);exit;

			$savings_account_id = $this->input->post('savings_account_id', true);
			$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

			$acctsavingsaccount 		= $this->AcctSavingsPrintSavingsMonitor_model->getAcctSavingsAccount_Detail($savings_account_id);
			$acctsavingsaccountdetail	= $this->AcctSavingsPrintSavingsMonitor_model->getAcctSavingsAccountDetail($savings_account_id, $sesi['start_date'], $sesi['end_date']);


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 7, 7, 7); 

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
			<br/>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\">
			    <tr>
			        <td width=\"100%\"><div style=\"text-align: center; font-size:14px; font-weight:bold\">KARTU MONITOR TABUNGAN</div></td>
			    </tr>
			</table>";

			$pdf->writeHTML($tbl, true, false, false, false, '');
			

			$tbl1 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			        <td width=\"15%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">NO. REK</div></td>
			        <td width=\"35%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">: ".$acctsavingsaccount['savings_account_no']."</div></td>
			        <td width=\"15%\" rowspan=\"2\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">Alamat</div></td>
			        <td width=\"35%\" rowspan=\"2\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">: ".$acctsavingsaccount['member_address']."</div></td>
			    </tr>
			    <tr>
			        <td width=\"15%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">NAMA</div></td>
			        <td width=\"35%\"><div style=\"text-align: left; font-size:12px; font-weight:bold\">: ".$acctsavingsaccount['member_name']."</div></td>
			    </tr>
			</table>";

			$tbl2 = "
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
			    <tr>
			    	<td width=\"3%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">No</div></td>
			        <td width=\"10%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Tgl Mutasi</div></td>
			        <td width=\"6%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Sandi</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Debit</div></td>
			        <td width=\"15%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Kredit</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Saldo</div></td>
			        <td width=\"20%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Keterangan</div></td>
			        <td width=\"10%\"><div style=\"text-align: center;border-bottom: 1px solid black;border-top: 1px solid black\">Val</div></td>
			    </tr>
			";

			$no=1;

			foreach ($acctsavingsaccountdetail as $key => $val) {
				$tbl3 .= "
					<tr>
				    	<td width=\"3%\"><div style=\"text-align: left;\">$no</div></td>
				        <td width=\"10%\"><div style=\"text-align: center;\">".tgltoview($val['today_transaction_date'])."</div></td>
				        <td width=\"6%\"><div style=\"text-align: center;\">".$val['mutation_code']."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['mutation_out'], 2)."</div></td>
				        <td width=\"15%\"><div style=\"text-align: right;\">".number_format($val['mutation_in'], 2)."</div></td>
				        <td width=\"20%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)."</div></td>
				        <td width=\"20%\"><div style=\"text-align: left;\">".$val['description']."</div></td>
				        <td width=\"10%\"><div style=\"text-align: center;\">".$val['operated_name']."</div></td>
				    </tr>
				";
				$no++;
			}

			$tbl4 = "</table>";

			$pdf->writeHTML($tbl1.$tbl2.$tbl3.$tbl4, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Kwitansi.pdf';
			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function SyncronizeData(){
			$auth = $this->session->userdata('auth');
			$sesi =	$this->session->userdata('filter-acctsavingsmonitor');
			
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			
			// if($sesi['savings_account_id']  == ''){
			// 	$savings_account_id = $sesi['savings_account_id'];
			// }
			
			if(!empty($sesi['savings_account_id'])){
				$datalog = array (
					'savings_syncronize_log_date' 		=> date('Y-m-d'),
					'savings_syncronize_log_start_date'	=> $sesi['start_date'],
					'savings_syncronize_log_end_date'	=> $sesi['end_date'],
					'savings_account_id'				=> $sesi['savings_account_id'],
					'branch_id'							=> $auth['branch_id'],
					'created_id'						=> $auth['user_id'],
					'created_on'						=> date('Y-m-d H:i:s'),
				);

				

				if($this->AcctSavingsPrintSavingsMonitor_model->insertAcctSavingsSyncronizeLog($datalog)){
					$opening_balance 			= $this->AcctSavingsPrintSavingsMonitor_model->getOpeningBalance($datalog['savings_account_id'], $datalog['savings_syncronize_log_start_date']);

					if(!is_array($opening_balance)){
						$opening_date 			= $this->AcctSavingsPrintSavingsMonitor_model->getLastDate($datalog['savings_account_id'], $datalog['savings_syncronize_log_start_date']);
						$opening_balance 		= $this->AcctSavingsPrintSavingsMonitor_model->getLastBalance($datalog['savings_account_id'], $opening_date);
					}

					$acctsavingsaccountdetail 	= $this->AcctSavingsPrintSavingsMonitor_model->getAcctSavingsAccountDetail($datalog['savings_account_id'], $datalog['savings_syncronize_log_start_date'], $datalog['savings_syncronize_log_end_date']);

					foreach ($acctsavingsaccountdetail as $key => $val) {
						$last_balance = ($opening_balance + $val['mutation_in']) - $val['mutation_out'];

						$newdata = array (
							'savings_account_detail_id'		=> $val['savings_account_detail_id'],
							'savings_account_id'			=> $val['savings_account_id'],
							'opening_balance'				=> $opening_balance,
							'last_balance'					=> $last_balance,
						);

						$opening_balance = $last_balance;

						if($this->AcctSavingsPrintSavingsMonitor_model->updateAcctSavingsAccountDetail($newdata)){
							$msg = "<div class='alert alert-success alert-dismissable'>  
									<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
											Syncronize Data Sukses
										</div> ";
							$this->session->set_userdata('message',$msg);
							continue;
						} else {
							$msg = "<div class='alert alert-danger alert-dismissable'>
								<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>	               
									Syncronize Data Gagal
								</div> ";
							$this->session->set_userdata('message',$msg);
							redirect('savings-print-savings-monitor/monitor-savings-mutation');
							break;
						}

						print_r($newdata);
						print_r("<BR>");
					}
					// exit;
					redirect('savings-print-savings-monitor/monitor-savings-mutation');

				} else {
					$msg = "<div class='alert alert-danger alert-dismissable'>  
							<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
								Syncronize Data Gagal
							</div> ";
					$sesi = $this->session->userdata('unique');
					redirect('savings-print-savings-monitor/monitor-savings-mutation');
				}

			} else {
				$msg = "<div class='alert alert-danger alert-dismissable'>  
						<button type='button' class='close' data-dismiss='alert' aria-hidden='true'></button>					
							No. Rekening Simpanan Masih Kosong
						</div> ";
				$this->session->set_userdata('message',$msg);
				redirect('savings-print-savings-monitor/monitor-savings-mutation');
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
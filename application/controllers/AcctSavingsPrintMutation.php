<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class AcctSavingsPrintMutation extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('Connection_model');
			$this->load->model('MainPage_model');
			$this->load->model('AcctSavingsPrintMutation_model');
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

		public function getAcctSavingsAccount(){
			$data['main_view']['corebranch']			= create_double($this->AcctSavingsPrintMutation_model->getCoreBranch(),'branch_id','branch_name');
			$data['main_view']['content']				= 'AcctSavingsPrintMutation/ListAcctSavingsPrintBook_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filterbook(){
			$data = array (
				"branch_id" 	=> $this->input->post('branch_id',true),
			);

			$this->session->set_userdata('filter-printbook',$data);
			redirect('savings-print-mutation/get-savings-mutation');
		}
		public function reset_search_book(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-printbook');
			redirect('savings-print-mutation/get-savings-mutation');
		}

		public function getListAcctSavingsAccountBook(){
			$auth 		= $this->session->userdata('auth');
			$sesi		= $this->session->userdata('filter-printbook');
			if(!is_array($sesi)){
				$sesi['branch_id']	= '';
			} 
			$list 		= $this->AcctSavingsAccount_model->get_datatables_mutation($sesi['branch_id']);
	        $data 		= array();
	        $no 		= $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'savings-print-mutation/process-print-cover-book/'.$savingsaccount->savings_account_id.'" class="btn btn-info btn-xs" role="button"><span class="glyphicon glyphicon-print"></span> Cetak Cover</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all_mutation($sesi['branch_id']),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered_mutation($sesi['branch_id']),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}

		public function processPrintCoverBook(){
			$savings_account_id 		= $this->uri->segment(3);
			$acctsavingsaccount			= $this->AcctSavingsAccount_model->getAcctSavingsAccount_Detail($savings_account_id);
			$preferencecompany 			= $this->CoreMember_model->getPreferenceCompany();

			$branch_name				= $this->AcctSavingsPrintMutation_model->getCoreBranchName($acctsavingsaccount['branch_id']);

			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(7, 2, 7, 7); 

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

			// -----------------------------------------------------------------------------$base_url = base_url();
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

			$tbl1 = "
			<br>
			<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">
				<tr style=\"line-height: 18px;\">
			        
			        <td width=\"16%\"><div style=\"text-align: left;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: left;\">".$acctsavingsaccount['member_name']."</div></td>
			        <td width=\"17%\"><div style=\"text-align: left;\"></div></td>			        			
			        <td width=\"40%\"><div style=\"text-align: left;\">".$branch_name."</div></td>
			   		
			    </tr>
				<tr style=\"line-height: 19px;\">			    
			        
			        <td width=\"16%\"><div style=\"text-align: left;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: left;\">".$acctsavingsaccount['member_address']."</div></td>
			        <td width=\"17%\"><div style=\"text-align: left;\"></div></td>			        			
			        <td width=\"40%\"><div style=\"text-align: left;\">".$acctsavingsaccount['member_identity_no']."</div></td>
			   		
			    </tr>
				<tr style=\"line-height: 19px;\">
			    			        
			        <td width=\"16%\"><div style=\"text-align: left;\"></div></td>
			        <td width=\"30%\"><div style=\"text-align: left;\">".$acctsavingsaccount['savings_account_no']."</div></td>
			        <td width=\"17%\"><div style=\"text-align: left;\"></div></td>			        			
			        <td width=\"40%\"><div style=\"text-align: left;\">".tgltoview($acctsavingsaccount['savings_account_date'])."</div></td>
			    </tr>
			    
			      				
			</table>";


			$pdf->writeHTML($tbl.$tbl1, true, false, false, false, '');


			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cover Buku '.$acctsavingsaccount['member_name'].'.pdf';
			$js ='';
			// force print dialog
			$js .= 'print(true);';

			// set javascript
			$pdf->IncludeJS($js);

			$pdf->Output($filename, 'I');

			//============================================================+
			// END OF FILE
			//============================================================+
		}

		public function function_elements_add(){
			$unique 	= $this->session->userdata('unique');
			$name 		= $this->input->post('name',true);
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('savings_account_last_number-'.$unique['unique']);
			$sessions[$name] = $value;
			$this->session->set_userdata('savings_account_last_number-'.$unique['unique'],$sessions);
		}

		public function MonitorSavingsMutation(){
			$unique 	= $this->session->userdata('unique');
			$sesi		= $this->session->userdata('filter-acctsavingsmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			$this->session->unset_userdata('datamutation-'.$unique['unique']);
			// print_r($sesi);exit;

			$savings_account_id = $this->uri->segment(3);
			if($savings_account_id == ''){
				$savings_account_id = $sesi['savings_account_id'];
			}

			$data['main_view']['acctsavingsaccount']			= $this->AcctSavingsPrintMutation_model->getAcctSavingsAccount_Detail($savings_account_id);
			$data['main_view']['acctsavingsaccountdetail']		= $this->AcctSavingsPrintMutation_model->getAcctSavingsAccountDetail($savings_account_id, $sesi['start_date'], $sesi['end_date']);		

			$data['main_view']['content']				= 'AcctSavingsPrintMutation/ListAcctSavingsPrintMutation_view';
			$this->load->view('MainPage_view',$data);
		}

		public function filter(){
			$data = array (
				"start_date"					=> tgltodb($this->input->post('start_date',true)),
				"end_date"						=> tgltodb($this->input->post('end_date',true)),
				"savings_account_id"			=> $this->input->post('savings_account_id', true),
			);

			$this->session->set_userdata('filter-acctsavingsmonitor',$data);
			redirect('savings-print-mutation/monitor-savings-mutation/'.$data['savings_account_id']);
		}

		public function getListAcctSavingsAccount(){
			$auth 		= $this->session->userdata('auth');
			$branch_id 	= '';
			$list 		= $this->AcctSavingsAccount_model->get_datatables_mutation($branch_id);
	        $data 		= array();
	        $no = $_POST['start'];
	        foreach ($list as $savingsaccount) {
	            $no++;
	            $row = array();
	            $row[] = $no;
	            $row[] = $savingsaccount->savings_account_no;
	            $row[] = $savingsaccount->member_name;
	            $row[] = $savingsaccount->member_address;
	            $row[] = '<a href="'.base_url().'savings-print-mutation/monitor-savings-mutation/'.$savingsaccount->savings_account_id.'" class="btn btn-info" role="button"><span class="glyphicon glyphicon-ok"></span> Select</a>';
	            $data[] = $row;
	        }

	        // print_r($list);exit;
	 
	        $output = array(
	                        "draw" => $_POST['draw'],
	                        "recordsTotal" => $this->AcctSavingsAccount_model->count_all_mutation($branch_id),
	                        "recordsFiltered" => $this->AcctSavingsAccount_model->count_filtered_mutation($branch_id),
	                        "data" => $data,
	                );
	        //output to json format
	        echo json_encode($output);
		}


		public function reset_search(){
			$unique 	= $this->session->userdata('unique');
			$sessions	= $this->session->unset_userdata('filter-acctsavingsmonitor');
			redirect('savings-print-mutation/monitor-savings-mutation');
		}

		public function processPrinting(){
			$auth 		= $this->session->userdata('auth');
			$unique 	= $this->session->userdata('unique');
			$sesi		= 	$this->session->userdata('filter-acctsavingsmonitor');
			if(!is_array($sesi)){
				$sesi['start_date']		= date('Y-m-d');
				$sesi['end_date']		= date('Y-m-d');
				$sesi['savings_account_id'] 	= '';
			}

			$datamutation = $this->session->userdata('datamutation-'.$unique['unique']);

			$status 						= $this->uri->segment(3);
			$savings_account_id 			= $this->uri->segment(4);
			$savings_account_last_number 	= $this->uri->segment(5);


			if(empty($datamutation)){
				$acctsavingsaccountdetail		= $this->AcctSavingsPrintMutation_model->getAcctSavingsAccountDetail($savings_account_id, $sesi['start_date'], $sesi['end_date']);

				if(empty($savings_account_last_number) || $savings_account_last_number == 0){
					$no = 1;
				} else {
					$no = $savings_account_last_number + 1;
				}

				foreach ($acctsavingsaccountdetail as $key => $val) {
					if($no == 14 ){
						$no = 1;
						// $this->processPrinting();
					} else {
						$no = $no;
					}

					if($val['mutation_in'] == 0){
						$mutation_in 	= '';
						$mutation_out 	= number_format($val['mutation_out'], 2);
					}

					if($val['mutation_out'] == 0){
						$mutation_in 	= number_format($val['mutation_in'], 2);
						$mutation_out 	= '';
					}


					$data[] = array (
						'no'						=> $no,
						'savings_account_detail_id' => $val['savings_account_detail_id'],
						'savings_account_id'		=> $val['savings_account_id'],
						'transaction_date'			=> $val['today_transaction_date'],
						'transaction_code'			=> $val['mutation_code'],
						'transaction_in'			=> $mutation_in,
						'transaction_out'			=> $mutation_out,
						'last_balance'				=> $val['last_balance'],
						'operated_name'				=> $val['operated_name'],	
						'status'					=> $val['savings_print_status'],
					);

					
					$no++;
					
				}

				$this->session->set_userdata('datamutation-'.$unique['unique'],$data);

			}

			

			$datamutation = $this->session->userdata('datamutation-'.$unique['unique']);

			if($status == 'print'){
				foreach ($datamutation as $k => $v) {
					$update_data = array(
						'savings_account_detail_id'		=> $v['savings_account_detail_id'],
						'savings_account_id'			=> $v['savings_account_id'],
						'savings_print_status'			=> 1,
						'savings_account_last_number'	=> $v['no'],
					);

					$this->AcctSavingsPrintMutation_model->updatePrintMutationStatus($update_data);
				}
			}


			require_once('tcpdf/config/tcpdf_config.php');
			require_once('tcpdf/tcpdf.php');
			// create new PDF document
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			$pdf->SetPrintHeader(false);
			$pdf->SetPrintFooter(false);

			$pdf->SetMargins(5, 24, 7, 7);

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

			$pdf->SetFont('helvetica', '', 9);

			// -----------------------------------------------------------------------------
			$base_url = base_url();
			$img = "<img src=\"".$base_url."assets/layouts/layout/img/".$preferencecompany['logo_koperasi']."\" alt=\"\" width=\"700%\" height=\"300%\"/>";

			$tbl0 = "
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


			$tbl = "<table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" width=\"100%\">";
			if($savings_account_last_number > 0){
				for ($i=1; $i <= $savings_account_last_number ; $i++) { 
					if($i == 7){
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

					} else {
						$tbl1 .= "
						<tr>
					    	<td></td>
					    </tr>";
					}
					
				}
			} 


			foreach ($datamutation as $key => $val) {
				$tbl1 .= "
						<tr>
					    	<td width=\"4%\"><div style=\"text-align: left;\">".$val['no'].".</div></td>
					        <td width=\"11%\"><div style=\"text-align: center;\">".date('d-m-y',strtotime(($val['transaction_date'])))."</div></td>
					        <td width=\"9%\"><div style=\"text-align: center;\">".$val['transaction_code']."</div></td>
					        <td width=\"18%\"><div style=\"text-align: right;\">".$val['transaction_out']." &nbsp;</div></td>
					        <td width=\"20%\"><div style=\"text-align: right;\">".$val['transaction_in']." &nbsp;</div></td>
					        <td width=\"23%\"><div style=\"text-align: right;\">".number_format($val['last_balance'], 2)." &nbsp;</div></td>
					        <td width=\"20%\"><div style=\"text-align: center;\">".substr($val['operated_name'],0,5)."</div></td>
					    </tr>
					";

					if($val['no'] == 7){
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
						";
					}

					if($val['no'] == 14){
						$tbl1 .= "
							<tr>
						    	<td></td>
						    </tr>

						";
					}
			}

			$tbl2 = "</table>";

			$pdf->writeHTML($page.$tbl.$tbl1.$tbl2, true, false, false, false, '');

			

			// if($last_no == 30){
			// 	$this->processPrinting();
			// } 

			ob_clean();

			// -----------------------------------------------------------------------------
			
			//Close and output PDF document
			$filename = 'Cetak Mutasi.pdf';

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

		public function function_state_add(){
			$unique 	= $this->session->userdata('unique');
			$value 		= $this->input->post('value',true);
			$sessions	= $this->session->userdata('addacctsavingsaccount-'.$unique['unique']);
			$sessions['active_tab'] = $value;
			$this->session->set_userdata('addacctsavingsaccount-'.$unique['unique'],$sessions);
		}	
		
	}
?>
<?php
	defined('BASEPATH') or exit('No direct script access allowed');
	Class CobaIp extends CI_Controller{
		 var $API ="";
		public function __construct(){
			parent::__construct();
			 $this->API="bd8446eaa574ab00ab72249f8b06a759";
			 $this->load->library('session');
		     $this->load->library('curl');
		     $this->load->helper('form');
		     $this->load->helper('url');
			
		}

    function index(){

    	
        $data['datappob'] = json_decode($this->curl->simple_get($this->API));



        $this->load->view('CobaIp_View',$data);
  	  }

  	  function UploadData(){

  	      	$auth 				= $this->session->userdata('auth');
			$unique 			= $this->session->userdata('unique');
			
				// print_r($schedulestudentscheduleitem);
				// exit;


			$apiID				= "CIPTA";
			$api 			 	= "bd8446eaa574ab00ab72249f8b06a759";
			$host 				= "http://117.20.55.219:1212/".$api."/".$apiID."";
			
			//$host 			=  "http://117.102.64.238:1212/yourIP.php";

			//$host					= "myip.dnsomatic.com";


			$url = $host;

			
			$data = array('{
				"func" : mitra_info"
				}');


			// $data = array("mitra_info");
			//print_r($url);exit;
			/*print_r("invtitemunit_add ");
			print_r($data['invtitemunit_add']);
			exit;*/

			$result = json_encode($data);

			$ch = curl_init($url);

			//print_r($url);exit;                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');                                                                  
			curl_setopt($ch, CURLOPT_POSTFIELDS, $result);

			curl_setopt($ch, CURLOPT_URL, $url);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                   
			    'Content-Type: application/json')                                                                       
			);   

			$data_execute = curl_exec($ch);

			//print_r($data_execute);exit;

	      	if (curl_errno($ch))
	        {
	        	print "Error: " . curl_error($ch);
	        }
	        else
	        {
	        	$transaction = json_decode($data_execute, TRUE);
	        	curl_close($ch);
	        	var_dump($transaction);
	      	}
		}
	

		

      	// print_r("url");
      	// print_r($url);

      	// append the header putting the secret key and hash

      	// $request_headers = array('func=ppMitraInfo');
      	// $result = json_encode($data);
      	// $request_headers[] = 'Authorization: Bearer ' . $url;
      	// $ch = curl_init();
      	// curl_setopt($ch, CURLOPT_URL, $url);
      	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      	// curl_setopt($ch, CURLOPT_TIMEOUT, 60);
      	// //curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
      	// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      	// $data = curl_exec($ch);

      	/*print_r("sent_data ");
      	print_r($sent_data);*/
      	// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
	}


		
		
?>
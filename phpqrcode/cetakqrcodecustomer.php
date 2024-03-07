<html>
<style>
@media print {
   #main * { display:none; }
   // printarea * { float: left; }
}
</style>
<script>
// $( document ).ready(function() {
    // window.print();
// });
</script>
<body onload="window.print()">
	<head>
		<title>QRPrint-<?php echo implode(explode(".",microtime(true)));?></title>
	</head>
  <!--<p>This should NOT be shown in Print Preview</p>-->
  <!--<div id="main">-->
   <!--<p>This should NOT be shown in Print Preview</p>-->
	<div id="main">
	<?php
		error_reporting(0);
		$files = glob('temp/*'); // get all file names
		foreach($files as $file){ // iterate files
			// print_r($files);
		  if(is_file($file)){
			  unlink($file); // delete file
		  }
		}
		//set it to writable location, a place for temp generated PNG files
		$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;
		
		//html PNG location prefix
		$PNG_WEB_DIR = 'temp/';

		include "qrlib.php";    
		
		//ofcourse we need rights to create temp dir
		if (!file_exists($PNG_TEMP_DIR))
			mkdir($PNG_TEMP_DIR);
		
		
		$filename = $PNG_TEMP_DIR.'test.png';
		//processing form input
		//remember to sanitize user input in real-life solution !!!
		$errorCorrectionLevel = 'L';
		if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
			$errorCorrectionLevel = $_REQUEST['level'];    

		$matrixPointSize = 10;
		if (isset($_REQUEST['size']))
			$matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);
		
		/* if (isset($_REQUEST['data'])) { 
		
			//it's very important!
			if (trim($_REQUEST['data']) == '')
				die('data cannot be empty! <a href="?">back</a>');
				
			// user data
			$filename = $PNG_TEMP_DIR.'test'.md5($_REQUEST['data'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
			QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
			
		} else {    
		
			//default data
			echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';    
			QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
			
		}     */
		$cypher = "tak ada data";
		if(isset($_POST)){
			if (isset($_POST['level']) && in_array($_POST['level'], array('L','M','Q','H')))
				$errorCorrectionLevel = $_POST['level'];    

			$matrixPointSize = 10;
			if (isset($_POST['size']))
				$matrixPointSize = min(max((int)$_POST['size'], 1), 10);
			
			// $dataarray = $_POST['dataarray'];
			$dataarray = json_decode($_POST['dataarray'],true);
			// print_r($dataarray);exit;
			
			foreach($dataarray as $k=>$v){
				$state = $_POST['state_'.$v['customer_id']];
				if($state=="1"){
					$filename = $PNG_TEMP_DIR.'test'.md5($v['customer_id'].'|'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
					QRcode::png($v['customer_id'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
					$datarisk = array(
						'namafile'	=>	$filename,
						'customer_name'	=>	$v['customer_name'],
					);
					$filenames[$k] = $datarisk;
					$cypher="";
				}
			}
			
		}else{
			echo 'tak ada data';
		}
		echo $cypher;
		// echo $_REQUEST['imagecount'];
		// echo $_REQUEST['cols'];
	?>
	</div>
	<div id="printarea">
	<?php
		//display generated file
		// echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" /> <hr/>';  
		if(!empty($filenames)){
			foreach($filenames as $k2=>$v2){
				echo '
					<table style="float: left;">
						<tr>
							<td style="text-align:center;"><img src="'.$PNG_WEB_DIR.basename($v2['namafile']).'" /></td>
						</tr>
				';
				if($_POST['printtext']=='yes'){
					echo'
							<tr>
								<td style="text-align:center;">'.$v2['customer_name'].'</td>
							</tr>
					';					
				}
				echo'
					</table>
					';
			}
		}
		// if(!is_numeric($rows) || !is_numeric($cols)){
			// echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />';  
		// }else{
			// echo"<table>";
			// for($i=1;$i<=$rows;$i++){
				// echo"<tr>";
				// for($j=1;$j<=$cols;$j++){
					// echo '<td><img src="'.$PNG_WEB_DIR.basename($filename).'"></td>';
				// }
				// echo"</tr>";
			// }
			// echo"</table>";
		// }
	?>
	</div>
</body>
</html>
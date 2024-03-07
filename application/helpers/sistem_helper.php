<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('create_double')){
	function create_double($data,$par1,$par2){
		$output['']='--- Choose One ---';
		foreach($data as $key=>$val){
			$output[$val[$par1]]=$val[$par2];
		}
		return $output;
	}
}

if ( ! function_exists('create_double_branch')){
	function create_double_branch($data,$par1,$par2){
		foreach($data as $key=>$val){
			$output[$val[$par1]]=$val[$par2];
		}
		return $output;
	}
}

if ( ! function_exists('array_double')){
	function array_double($data,$par1,$par2){
		foreach($data as $key=>$val){
			$output[$val[$par1]]=$val[$par2];
		}
		return $output;
	}
}

if (! function_exists('konversi_uang')){
	function konversi_uang($a){
		if(isset($a)){
			if($a==''){$a=0;}
			$p 			= strlen($a);
			$hasil 		= number_format($a,2); 
			return "Rp. ".$hasil;
		}
	}
}

if (! function_exists('nominal')){
	function nominal($a){
		if(isset($a)){
			if($a==''){$a=0;}
			return number_format($a);
		}
	}
}

if (! function_exists('Parse_Data')){
	function Parse_Data($data,$p1,$p2){
		$data=" ".$data;
		$hasil="";
		$awal=strpos($data,$p1);
		if($awal!=""){
			$akhir=strpos(strstr($data,$p1),$p2);
			if($akhir!=""){
				$hasil=substr($data,$awal+strlen($p1),$akhir-strlen($p1));
			}
		}
		return $hasil;	
	}
}

if (! function_exists('handling_input')){
	function handling_input($operator){
		$filter = abs(mysql_real_escape_string(stripslashes(strip_tags(htmlspecialchars($operator,ENT_QUOTES)))));
		return $filter;
	}
}

if (! function_exists('singkat')){
	function singkat($value){
		$result="";
		$pecah = explode(" ",$value);
		foreach($pecah as $key => $val){
			if ($key<3){$result=$result.$val." ";}
			else {$result=$result.strtoupper(substr($val,0,1));}
		}
		return $result;
	}
}

if (! function_exists('singkat2')){
	function singkat2($value){
		$result="";
		$pecah = explode(" ",$value);
		foreach($pecah as $key => $val){
			if ($key<2){$result=$result.$val." ";}
			else {$result=$result.strtoupper(substr($val,0,1));}
		}
		return $result;
	}
}

if( ! function_exists('Enkripsi')){
	function Enkripsi($string, $key) {
	  $result = '';
	  for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)+ord($keychar));
		$result.=$char;
	  }
	  return base64_encode($result);
	}
}

if( ! function_exists('Dekripsi')){
	function Dekripsi($string, $key) {
	  $result = '';
	  $string = base64_decode($string);
	  for($i=0; $i<strlen($string); $i++) {
		$char = substr($string, $i, 1);
		$keychar = substr($key, ($i % strlen($key))-1, 1);
		$char = chr(ord($char)-ord($keychar));
		$result.=$char;
	  }
	  return $result;
	}
}

// if(! function_exists('get_unique')){
	// function get_unique(){
		// $wmi = new COM('winmgmts://');
		// $NIC = $wmi->ExecQuery("SELECT * FROM Win32_NetworkAdapter  ");
		// foreach($NIC as $obj){
			// if($obj->NetConnectionID=='Local Area Connection'){
				// $MAC = $obj->MACAddress;
			// }
		// }
		// return $MAC;
	// }
// }
if(! function_exists('get_unique')){
function get_unique(){
return gethostbyname($_SERVER['HTTP_HOST']);
}
}

if(! function_exists('getKey')){
	function getKey(){
		$posisition = str_replace('\'', '/', realpath(dirname(__FILE__))) . '/';
		$root		= str_replace('\'', '/', realpath($posisition . '../../')) . '/';
		
		$filename	= $root."parameter.par";
		$fp  		= fopen($filename, 'r');
		$content 	= fread($fp, filesize($filename));
		fclose($fp);
		
		$posisi 	= strpos($content, "?");
		$separate	= strpos($content,";");
		$target		= substr($content,$posisi+1,($separate-$posisi)-1);
		
		return $target;
	}
}

if (! function_exists('tgltodb')){
	function tgltodb($data){
		$hasil=Date('Y-m-d', strtotime($data));
		return $hasil;	
	}
}

if (! function_exists('tgltoview')){
	function tgltoview($data){
		if($data==""||$data=="0000-00-00"){
		return "-";	
		}else{
		$hasil=Date('d-m-Y', strtotime($data));
		return $hasil;	
		}
	}
}

if(! function_exists('get_root_path')){
	function get_root_path(){
		$posisition = str_replace('\'', '/', realpath(dirname(__FILE__))) . '/';
		$root		= str_replace('\'', '/', realpath($posisition . '../../')) . '/';
		return $root;
	}

	if(! function_exists('encode_to_json')){
		function encode_to_json($array){

	        // determine type
	        if(is_numeric(key($array))) {

	            // indexed (list)
	            $output = '[';
	            for($i = 0, $last = (sizeof($array) - 1); isset($array[$i]); ++$i) {
	                if(is_array($array[$i])) $output .= encode_to_json($array[$i]);
					else if(is_string($array[$i])) $output .= '"'.$array[$i].'"';
	                else  $output .= ($array[$i]); //_val
	                if($i !== $last) $output .= ',';
	            }
	            $output .= ']';

	        } else {

	            // associative (object)
	            $output = '{';
	            $last = sizeof($array) - 1;
	            $i = 0;
	            foreach($array as $key => $value) {
	                $output .= $key.':';
	                if(is_array($value)) $output .= encode_to_json($value);
	                else if(is_numeric($value) && !is_array($value)) $output .= $value;
	                else  $output .= '"'.$value.'"';
	                if($i !== $last) $output .= ',';
	                ++$i;
	            }
	            $output .= '}';
	        }
			return $output;
		}
	}

	function numtotxt($num) {
		$tdiv 	= array("","","ratus ","ribu ", "ratus ", "juta ", "ratus ","miliar ");
		$divs 	= array( 0,0,0,0,0,0,0);
		$pos 	= 0; // index into tdiv;
		// make num a string, and reverse it, because we run through it backwards
		// bikin num ke string dan dibalik, karena kita baca dari arah balik
		$num 	= strval(strrev(number_format($num, 2, '.',''))); 
		$answer = ""; // mulai dari sini
		while (strlen($num)) {
			if ( strlen($num) == 1 || ($pos >2 && $pos % 2 == 1))  {
				$answer = doone(substr($num, 0, 1)) . $answer;
				$num 	= substr($num,1);
			} else {
				$answer = dotwo(substr($num, 0, 2)) . $answer;
				$num 	= substr($num,2);
				if ($pos < 2)
					$pos++;
			}

			if (substr($num, 0, 1) == '.') {
				if (! strlen($answer)){
					$answer = "";
				}

				$answer = "" . $answer . "";
				$num 	= substr($num,1);
				// kasih tanda "nol" jika tidak ada
				if (strlen($num) == 1 && $num == '0') {
					$answer = "" . $answer;
					$num 	= substr($num,1);
				}
			}
		    // add separator
		    if ($pos >= 2 && strlen($num)) {
				if (substr($num, 0, 1) != 0  || (strlen($num) >1 && substr($num,1,1) != 0
					&& $pos %2 == 1)  ) {
					// check for missed millions and thousands when doing hundreds
					// cek kalau ada yg lepas pada juta, ribu dan ratus
					if ( $pos == 4 || $pos == 6 ) {
						if ($divs[$pos -1] == 0)
							$answer = $tdiv[$pos -1 ] . $answer;
					}
					// standard
					$divs[$pos] = 1;
					$answer 	= $tdiv[$pos++] . $answer;
				} else {
					$pos++;
				}
			}
	    }
	    return strtoupper($answer.'rupiah');
	}

	function numtotxtnorp($num) {
		$tdiv 	= array("","","ratus ","ribu ", "ratus ", "juta ", "ratus ","miliar ");
		$divs 	= array( 0,0,0,0,0,0,0);
		$pos 	= 0;
		$num 	= strval(strrev(number_format($num, 2, '.',''))); 
		$answer = "";
		while (strlen($num)) {
			if ( strlen($num) == 1 || ($pos >2 && $pos % 2 == 1))  {
				$answer = doone(substr($num, 0, 1)) . $answer;
				$num 	= substr($num,1);
			} else {
				$answer = dotwo(substr($num, 0, 2)) . $answer;
				$num 	= substr($num,2);
				if ($pos < 2)
					$pos++;
			}

			if (substr($num, 0, 1) == '.') {
				if (! strlen($answer)){
					$answer = "";
				}

				$answer = "" . $answer . "";
				$num 	= substr($num,1);
				if (strlen($num) == 1 && $num == '0') {
					$answer = "" . $answer;
					$num 	= substr($num,1);
				}
			}
		    // add separator
		    if ($pos >= 2 && strlen($num)) {
				if (substr($num, 0, 1) != 0  || (strlen($num) >1 && substr($num,1,1) != 0
					&& $pos %2 == 1)  ) {
					if ( $pos == 4 || $pos == 6 ) {
						if ($divs[$pos -1] == 0)
							$answer = $answer;
					}
					// standard
					$divs[$pos] = 1;
					$answer 	= $answer;
				} else {
					$pos++;
				}
			}
	    }
	    return strtolower($answer);
	}

	function doone2($onestr) {
	    $tsingle = array("","satu ","dua ","tiga ","empat ","lima ",
		"enam ","tujuh ","delapan ","sembilan ");
	      return strtoupper($tsingle[$onestr]);
	}	
	 
	function doone($onestr) {
	    $tsingle = array("","se","dua ","tiga ","empat ","lima ", "enam ","tujuh ","delapan ","sembilan ");
	      return strtoupper($tsingle[$onestr]);
	}	

	function dotwo($twostr) {
	    $tdouble = array("","puluh ","dua puluh ","tiga puluh ","empat puluh ","lima puluh ", "enam puluh ","tujuh puluh ","delapan puluh ","sembilan puluh ");
	    $teen = array("sepuluh ","sebelas ","dua belas ","tiga belas ","empat belas ","lima belas ", "enam belas ","tujuh belas ","delapan belas ","sembilan belas ");
	    if ( substr($twostr,1,1) == '0') {
			$ret = doone2(substr($twostr,0,1));
	    } else if (substr($twostr,1,1) == '1') {
			$ret = $teen[substr($twostr,0,1)];
	    } else {
			$ret = $tdouble[substr($twostr,1,1)] . doone2(substr($twostr,0,1));
	    }
	    return strtoupper($ret);
	}

	function sentenceCase($string) { 
	    $sentences = preg_split('/([.?!]+)/', $string, -1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE); 
	    $newString = ''; 
	    foreach ($sentences as $key => $sentence) { 
	        $newString .= ($key & 1) == 0? 
	            ucfirst(strtolower(trim($sentence))) : 
	            $sentence.' '; 
	    } 
	    return trim($newString); 
	}
}
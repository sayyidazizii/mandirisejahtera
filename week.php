<?php


$bulanini = date('m');
$akhirbulan = date('t');

$minggu = $akhirbulan / 7;

$signupdate=date('Y-m-01');
$signupweek=date("W",strtotime($signupdate));
$year=date("Y",strtotime($signupdate));
$currentweek = date("W");

// print_r($signupweek);exit;
$currentweek = $signupweek + 4;
for($i=$signupweek;$i<=$currentweek;$i++) {
    $result=getWeek($i,$year);
    // echo "Week:".$i." Start date:".$result['start']." End date:".$result['end']."<br>";

    $date[$i] = array (
	  	'start'		=> $result['start'],
	  	'end'		=> $result['end'],
	  );
}

print_r($date);

function getWeek($week, $year) {
  $dto = new DateTime();
  $result['start'] = $dto->setISODate($year, $week, 0)->format('Y-m-d');
  $result['end'] = $dto->setISODate($year, $week, 6)->format('Y-m-d');

  
  return $result;
}

?>
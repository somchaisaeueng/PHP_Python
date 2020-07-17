<?php
// ****************************************************************************
include"../Configuration.php"; 
// ****************************************************************************
include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
// ****************************************************************************
$TodayFormatDt = date('Y-m-d');
// ****************************************************************************

exit();

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

$begin = new DateTime('2018-03-01');
$end = new DateTime('2018-03-31');

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

foreach ($period as $dt) {
    $weekend = 0;
    $weekday = 0;
    echo "<br>";
    $result = $dt->format("Y-m-d");
    $weekend = isWeekend($result);
    if($weekend=="") { $weekend = "0"; $weekday = "1"; }
    
    $sql = "insert into roadmoto_dynamiccalendar (date, weekend, weekday) values('$result','$weekend','$weekday')"; 
      
    mysql_query($con, $sql) or die(mysqli_error());


    
}


?>
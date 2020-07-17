<?php
// ****************************************************************************
include"../Configuration.php"; 
// ****************************************************************************
include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
$TodayFormatDt = date('Y-m-d');
// ****************************************************************************

function isWeekend($date) {
    return (date('N', strtotime($date)) >= 6);
}

$Calendar_All = mysqli_query($con,"SELECT * FROM roadmoto_dynamiccalendar WHERE advanced='-1'");
  while($CalendarIn = mysqli_fetch_array($Calendar_All))
    {
     $Book_Weekday  = 0;
     $Book_Weekend  = 0;
     $Book_Holiday  = 0;
     $Book_Event    = 0;
     $Book_Popular  = 0;
     $Book_Advanced = 0;
     $Book_UR       = 0;

     $Book_ID         = $CalendarIn['ID'];
     $Book_Date       = $CalendarIn['date'];
     $Book_Weekend    = $CalendarIn['weekend'];
     $Book_Weekday    = $CalendarIn['weekday'];
     $Book_Holiday    = $CalendarIn['holiday'];
     $Book_Event      = $CalendarIn['event'];
     $Book_Popular    = $CalendarIn['popular'];
     $Book_Advanced   = $CalendarIn['advanced'];
     $Book_UR         = $CalendarIn['utilization'];
     $Book_Location   = $CalendarIn['location'];
     $Book_Class      = $CalendarIn['class'];
     $Book_Surcharge  = $CalendarIn['surcharge'];
     
     $sql = "insert into roadmoto_historicalcalendar (calendar_id, date, weekend, weekday, holiday, event, popular, advanced, utilization, location, class, surcharge) values('$Book_ID','$Book_Date', '$Book_Weekend', '$Book_Weekday', '$Book_Holiday', '$Book_Event', '$Book_Popular', '$Book_Advanced', '$Book_UR', '$Book_Location', '$Book_Class', '$Book_Surcharge')"; 
     
     mysqli_query($con, $sql) or die(mysqli_error());
     $result = mysqli_query($con,"DELETE FROM roadmoto_dynamiccalendar
                        WHERE ID='$Book_ID'");     
    }
?>
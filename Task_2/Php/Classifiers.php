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

$Calendar_All = mysqli_query($con,"SELECT * FROM roadmoto_dynamiccalendar");
  while($CalendarIn = mysqli_fetch_array($Calendar_All))
    {
     $LearningClass = "";
     
     $Holiday  = 0;
     $Event    = 0;
     $Popular  = 0;
     $Advanced = 0;
     
     $Book_Date      = $CalendarIn['date'];
     $Book_ID        = $CalendarIn['ID'];
     
        $Learning_All = mysqli_query($con,"SELECT * FROM roadmoto_dynamiclearning WHERE date='$Book_Date'");

        while($LearningIn = mysqli_fetch_array($Learning_All))
        {
            $LearningClass   = $LearningIn['classification'];
            if($LearningClass=="EVENT")
            { $Event = "1"; }
            if($LearningClass=="HOLIDAY")
            { $Holiday = "1"; }
            if($LearningClass=="POPULAR")
            { $Popular = "1"; }            
        }
        
        $Calendar_Today  = strtotime($TodayFormatDt);
        $Calendar_Select = strtotime($Book_Date);
        $Advanced        = $Calendar_Select - $Calendar_Today;
        $Advanced        = round($Advanced / (60 * 60 * 24));  

        $result = mysqli_query($con,"UPDATE roadmoto_dynamiccalendar
                            SET holiday='$Holiday' WHERE ID='$Book_ID'");     
        $result = mysqli_query($con,"UPDATE roadmoto_dynamiccalendar
                            SET event='$Event' WHERE ID='$Book_ID'");         
        $result = mysqli_query($con,"UPDATE roadmoto_dynamiccalendar
                            SET popular='$Popular' WHERE ID='$Book_ID'"); 
        $result = mysqli_query($con,"UPDATE roadmoto_dynamiccalendar
                            SET advanced='$Advanced' WHERE ID='$Book_ID'");   
    }
?>
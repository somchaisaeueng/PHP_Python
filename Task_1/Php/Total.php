<?php
// ****************************************************************************
include"Configuration.php"; 
// ****************************************************************************
// include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
// ****************************************************************************

$Bookings_All = mysqli_query($con,"SELECT * FROM roadmoto_booking WHERE completed='False'");
    while($BookingsIn = mysqli_fetch_array($Bookings_All))
    {
          $Book_Identif      = $BookingsIn['ID'];
          
          $Book_RentalDays   = (int)$BookingsIn['rental_days'];
          $Book_RentalRate   = floatval($BookingsIn['rental_rate']);
    
          $Book_LateDays     = (int)$BookingsIn['late_days'];
          $Book_LateRate     = floatval($BookingsIn['late_surcharge']);
    
          $Book_Protection   = $BookingsIn['protection'];
          $Book_Upgrade      = $BookingsIn['upgrade'];
          $Book_ProtectDays  = (int)$BookingsIn['protection_days'];
          $Book_UpgradeDays  = (int)$BookingsIn['upgrade_days'];
          $Book_ProtectRate  = floatval($BookingsIn['protection_rate']);
          $Book_UpgradeRate  = floatval($BookingsIn['upgrade_rate']);
    
          $Rental_Total      = (float)($Book_RentalDays * $Book_RentalRate);
          $Rental_Total      = round($Rental_Total, 2, PHP_ROUND_HALF_UP);
          
          $Late_Total        = (float)($Book_LateDays * $Book_LateRate);
          $Late_Total        = round($Late_Total, 2, PHP_ROUND_HALF_UP);
    
          if($Book_Protection=="1")
          {
          $Protect_Total     = (float)($Book_ProtectDays * $Book_ProtectRate);
          $Protect_Total     = round($Protect_Total, 2, PHP_ROUND_HALF_UP);
          }
          else
          {
          $Protect_Total     = 0.00;
          }
          
          if($Book_Upgrade=="1")
          {
          $Upgrade_Total     = (float)($Book_UpgradeDays * $Book_UpgradeRate);
          $Upgrade_Total     = round($Upgrade_Total, 2, PHP_ROUND_HALF_UP);
          }
          else
          {
          $Upgrade_Total    = 0.00;
          }
        var_dump($Upgrade_Total);
          
        $result = mysqli_query($con,"UPDATE roadmoto_booking 
        SET rental_total='$Rental_Total' WHERE 
        ID='$Book_Identif'"); 
        
        $result = mysqli_query($con,"UPDATE roadmoto_booking 
        SET late_surcharge_total='$Late_Total' WHERE 
        ID='$Book_Identif'");       
        
        $result = mysqli_query($con,"UPDATE roadmoto_booking 
        SET protection_total='$Protect_Total' WHERE 
        ID='$Book_Identif'");              
        
        $result = mysqli_query($con,"UPDATE roadmoto_booking 
        SET upgrade_total='$Upgrade_Total' WHERE 
        ID='$Book_Identif'");                     
    }
?>
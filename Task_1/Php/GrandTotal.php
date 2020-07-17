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
        
        $Book_Identif          = $BookingsIn['ID'];
                  
        $Book_RentalTotal      = floatval($BookingsIn['rental_total']);
        $Book_BaseSurcharge    = floatval($BookingsIn['rental_surcharge']);
        $Book_SurchargeTotal   = floatval($BookingsIn['late_surcharge_total']);
        $Book_ProtectTotal     = floatval($BookingsIn['protection_total']);
        $Book_UpgradeTotal     = floatval($BookingsIn['upgrade_total']);
        $Book_LatePickChg      = floatval($BookingsIn['late_pickupcharge']);
        $Book_LateDropChg      = floatval($BookingsIn['late_dropoffcharge']);
        $Book_IncidentTotal    = floatval($BookingsIn['incident_total']);
        $Book_UpsellTotal      = floatval($BookingsIn['upsell_total']);
        $Book_SafetyFee        = floatval($BookingsIn['safetyfee_total']);
        $Book_ConcessionFee    = floatval($BookingsIn['concessionfee']);
        $Book_EnvironmenalFee  = floatval($BookingsIn['environmentalfee']);
        $Book_DiscountTotal    = floatval($BookingsIn['discount_total']);
    
        $Grand_Total = (float)
          $Book_RentalTotal 
        + $Book_BaseSurcharge
        + $Book_SurchargeTotal 
        + $Book_ProtectTotal
        + $Book_UpgradeTotal
        + $Book_LatePickChg
        + $Book_LateDropChg
        + $Book_IncidentTotal
        + $Book_UpsellTotal
        + $Book_SafetyFee
        + $Book_ConcessionFee
        + $Book_EnvironmenalFee
        - $Book_DiscountTotal;
    
        $Grand_Total      = round($Grand_Total, 2, PHP_ROUND_HALF_UP);
        var_dump($Grand_Total);
    
        $result = mysqli_query($con,"UPDATE roadmoto_booking 
                SET grand_total='$Grand_Total' WHERE 
                ID='$Book_Identif'"); 
        }
?>
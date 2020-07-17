<?php
// ****************************************************************************
include"Configuration.php"; 
// ****************************************************************************
// include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
// ****************************************************************************

    global $Concession_Rate_Base;
    global $Concession_Rate_Disc;
    global $Environmental_Rate_Base;
    global $Environmental_Rate_Daily;
    global $Environmental_Rate_DailyMax;
    
    $AssetRates    = array();
    $FacilityRates = array();
    
    $Concession_Rate_Base          = 0.03;
    $Concession_Rate_Disc          = 0.025;
    // Environmental Variable Rate On File Based On Location
    $Environmental_Rate_Base       = 0.90;     
    $Environmental_Rate_Daily      = 3.00;   
    $Environmental_Rate_DailyMax   = 40.00; 
    
    $Assets_All = mysqli_query($con,"SELECT * FROM roadmoto_assets WHERE current_status='Active'");
    while($AssetsIn = mysqli_fetch_array($Assets_All))
    {
        $Asset_Identif      = $AssetsIn['ID'];
        $Asset_Rate         = $AssetsIn['environmental_rate'];
        $AssetRates[$Asset_Identif] = $Asset_Rate;
    }
    
    
    $Facilities_All = mysqli_query($con,"SELECT * FROM roadmoto_facilities WHERE Status='Active'");
    while($FacilityIn = mysqli_fetch_array($Facilities_All))
    {
        $Facility_Identif      = $FacilityIn['ID'];
        $Facility_Rate         = $FacilityIn['concession_rate'];
        $FacilityRates[$Facility_Identif] = $Facility_Rate;
    }            
    
    $Bookings_All = mysqli_query($con,"SELECT * FROM roadmoto_booking WHERE completed='False'");
    while($BookingsIn = mysqli_fetch_array($Bookings_All))
    {
        // Finalize Rates
        $Concession_Rate           = 0.00;
        $Environmental_Rate        = 0.00;     
        
        // Reset Temporary Variables
        $Concession_Base_Total     = 0.00;
        $Concession_Disc_Total     = 0.00; 
        $Concession_Surc_Total     = 0.00; // Surcharged Per Facility
        
        // Reset Temporary Variables
        $Environmental_Base_Total  = 0.00; 
        $Environmental_Daily_Total = 0.00;
        $Environmental_Surc_Total  = 0.00; // Surcharged Per Asset
        
        $Book_Identif         = $BookingsIn['ID'];
        $Book_AssetIdentif    = $BookingsIn['asset_id'];
        $Book_FacilityIdentif = $BookingsIn['location_id'];
        
        $Book_RentalDays      = (int)$BookingsIn['rental_days']; 
        $Book_RentalTotal     = (float)$BookingsIn['rental_total'];
        $Book_RentalDiscount  = (float)$BookingsIn['discount_total'];
        
        // FULL BASE
        $Concession_Base_Total = ($Concession_Rate_Base * $Book_RentalTotal);  
        $Concession_Base_Total = round($Concession_Base_Total, 2, PHP_ROUND_HALF_UP);
        
        // DISCOUNT BASE
        $Concession_Disc_Total = ($Concession_Rate_Disc * ($Book_RentalTotal-$Book_RentalDiscount));  
        $Concession_Disc_Total = round($Concession_Disc_Total, 2, PHP_ROUND_HALF_UP);
        
        // VARIABLE DISCOUNTED BASE
        $Concession_Surc_Total = ($FacilityRates[$Book_FacilityIdentif] * ($Book_RentalTotal-$Book_RentalDiscount));  
        $Concession_Surc_Total = round($Concession_Surc_Total, 2, PHP_ROUND_HALF_UP);
        
        $Concession_Rate = ($Concession_Base_Total + $Concession_Disc_Total + $Concession_Surc_Total);
        
        // FULL BASE
        $Environmental_Base_Total = ($Environmental_Rate_Base);
        $Environmental_Base_Total = round($Environmental_Base_Total, 2, PHP_ROUND_HALF_UP);
        printf($Environmental_Base_Total);
        
        // DAILY DISCOUNTED BASE
        $Environmental_Daily_Total = ($Environmental_Rate_Daily * $Book_RentalDays);
        if($Environmental_Daily_Total>=$Environmental_Rate_DailyMax)
        { $Environmental_Daily_Total=$Environmental_Rate_DailyMax; }
        $Environmental_Daily_Total = round($Environmental_Daily_Total, 2, PHP_ROUND_HALF_UP);
        
        // VARIABLE DISCOUNTED BASE
        $Environmental_Surc_Total = ($AssetRates[$Book_AssetIdentif] * ($Book_RentalTotal-$Book_RentalDiscount));  
        $Environmental_Surc_Total = round($Environmental_Surc_Total, 2, PHP_ROUND_HALF_UP);
        
        $Environmental_Rate = ($Environmental_Base_Total + $Environmental_Daily_Total + $Environmental_Surc_Total);
        $result = mysqli_query($con,"UPDATE roadmoto_booking SET concessionfee='$Concession_Rate' WHERE ID='$Book_Identif'");   
        $result = mysqli_query($con,"UPDATE roadmoto_booking SET environmentalfee='$Environmental_Rate' WHERE ID='$Book_Identif'");  
        
    } 
       
    // $con->close();

?>
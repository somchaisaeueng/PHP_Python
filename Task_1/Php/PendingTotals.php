<?php
// ****************************************************************************
include"Configuration.php"; 
// ****************************************************************************
// include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
// ****************************************************************************

$Bookd_CurrentDate = date("Y-m-d");
$Bookings_All = mysqli_query($con,"SELECT * FROM roadmoto_booking WHERE completed!='True' and authorization='Authorized' AND total_onfile='0'");
    while($BookingsIn = mysqli_fetch_array($Bookings_All))
    {   
          $Estimated_Total   = 0.00;
          $Bookd_Identif     = $BookingsIn['ID'];
          $Bookd_Custome     = $BookingsIn['customer_id'];
          $Bookd_GrandTotal  = $BookingsIn['grand_total'];
          $Bookd_PickupDate  = $BookingsIn['pickup_date'];
          $Estimated_Total   = $Bookd_GrandTotal;
          $Current_Date      = new DateTime("$Bookd_CurrentDate");
          $Future_Pickup     = new DateTime($Bookd_PickupDate);
          $Days              = $Future_Pickup->diff($Current_Date)->format("%a");
          
          // The rental is within the 72 hour cancellation window
          if(($Days<=3) && ($Days>=0))
          {
              $Total_Found = "False";
              $Deposits_All = mysqli_query($con,"SELECT * FROM roadmoto_payments WHERE bookingid='$Bookd_Identif' AND transactiontag='estimatedtotal' LIMIT 1");
              while($DepositIn = mysqli_fetch_array($Deposits_All))
              {   
                  $Total_Found = "True";
                  $Deps_Identif  = $DepositIn['ID'];
              }
              var_dump($Deps_Identif);
              
              if($Total_Found=="False")
              {
                  // Your going to want to change this to set itself based on the pricing table which will be used for all things from now on. 
                  $sql = "insert into roadmoto_payments (transactiontag, transactionflag, transactiontype, transactionstatus, total, bookingid, customerid, origin) 
                  values('estimatedtotal', '1', 'charge', 'pending', '$Estimated_Total', '$Bookd_Identif', '$Bookd_Custome', 'Stripe')"; 
                  mysqli_query($con, $sql) or die(mysqli_error());
              }   
          }
    }
?>
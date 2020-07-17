<?php
// ****************************************************************************
include"Configuration.php"; 
// ****************************************************************************
// include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
// ****************************************************************************

$Bookings_All = mysqli_query($con,"SELECT * FROM roadmoto_booking WHERE completed!='True' AND deposit_onfile='0'");
    while($BookingsIn = mysqli_fetch_array($Bookings_All))
    {   
          $Bookd_Terms      = "";   
          $Bookd_Identif    = $BookingsIn['ID'];
          $Bookd_Custome    = $BookingsIn['customer_id'];

          $Deposit_Found = "False";
          $Deposits_All = mysqli_query($con,"SELECT * FROM roadmoto_payments WHERE bookingid='$Bookd_Identif' AND transactiontag='deposit' LIMIT 1");
          while($DepositIn = mysqli_fetch_array($Deposits_All))
          {   
              $Deposit_Found = "True";
              $Deps_Identif  = $DepositIn['ID'];
          }
          var_dump($Deps_Identif);
          if($Deposit_Found=="False")
          {
              
              // Your going to want to change this to set itself based on the pricing table which will be used for all things from now on. 
              $sql = "insert into roadmoto_payments (transactiontag, transactionflag, transactiontype, transactionstatus, total, bookingid, customerid, origin) 
              values('deposit', '1', 'charge', 'pending', '200.00', '$Bookd_Identif', '$Bookd_Custome', 'Stripe')"; 
              mysqli_query($con, $sql) or die(mysqli_error());
          }
    }
?>
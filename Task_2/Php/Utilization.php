<?php
// ****************************************************************************
include"../Configuration.php"; 
// ****************************************************************************
include"../BlockAccess.php"; 
// ****************************************************************************
date_default_timezone_set('America/Los_Angeles');
// ****************************************************************************

$Current_Month     = date("m");
$Current_Year      = date("Y");  
$Year_Check_Start  = strtotime(''.$Current_Year.'-'.$Current_Month.'');
$Year_Check_End    = strtotime("+1 year", $Year_Check_Start);

while($Year_Check_Start < $Year_Check_End)
{
    echo date('Y-m', $Year_Check_Start) . '<br>';
?>
        <table class="table table-bordered">
                <tbody>
                        <tr>
                        <?php	
                            $Assets_Active_Found = "0";
                            $Assets_Active = mysqli_query($con,"SELECT * FROM roadmoto_assets WHERE current_status='Active'");
                            while($Assets_Found = mysqli_fetch_array($Assets_Active))
                            { $Assets_Active_Found++; }
                            ?>
                            <?php
                            $Global_Events   = array();
                            $Global_Calendar = array();
                            $Monthly_Usage   = array();

                            $cal = array();
                            $day = array(); 
                            $Format = 0;
                            
                            $Current_Day       = date("d");
                            $Current_Month     = date("m");
                            $Current_Year      = date("Y");       
                            
                            if(!$Forwardtrack_Days) // Check to see if set
                            {                            
                            $Backtrack_Days    = 0;
                            }
                            
                            if(!$Forwardtrack_Days) // Check to see if set
                            {
                            $Forwardtrack_Days = 0;
                            }
                            
                            $Start_Day         = "1";
                            $Start_Month       = date("m", $Year_Check_Start);
                            $Start_Year        = date("Y", $Year_Check_Start);  
                            if($Start_Day - $Backtrack_Days >=0) 
                                 { 
                                    $Start_Day   = $Start_Day - $Backtrack_Days;
                                 }
                            else { $Start_Day   = "01";     }   
                            $Send_Initialize   = $Start_Year."-".$Start_Month."-".$Start_Day."";

                            $Exit_Day          = date("t", $Year_Check_Start);
                            $Exit_Month        = date("m", $Year_Check_Start);
                            $Exit_Year         = date("Y", $Year_Check_Start);                   
                            $Send_Exit         = $Exit_Year."-".$Exit_Month."-".$Exit_Day."";
                            
                            $begin = new DateTime($Send_Initialize);
                            $end   = new DateTime($Send_Exit); 
                            
                            // Final end date
                            $end = $end->modify( '+1 day' ); 
                            $end = $end->modify( '+'.$Forwardtrack_Days.' day' ); //$Forwardtrack_Days
                            
                            $interval  = new DateInterval('P1D');
                            $daterange = new DatePeriod($begin, $interval ,$end);

                            foreach($daterange as $date){
                                 $Calendar_Date = $date->format("Y-m-d");
                                 $Calendar_NDay = $date->format("D");
                                 $Calendar_Day  = $date->format("d");
                                 $Calendar_Mon  = $date->format("m");
                                 $Calendar_Year = $date->format("Y");

                      $Global_Calendar[$Calendar_Date]["DayName"] 
                      = "$Calendar_NDay";
                      
                      $Global_Calendar[$Calendar_Date]["Day"] 
                      = "$Calendar_Day";

                      $Global_Calendar[$Calendar_Date]["Month"] 
                      = "$Calendar_Mon";
                      
                      $Global_Calendar[$Calendar_Date]["Year"]
                      = "$Calendar_Year";

                      $Global_Calendar[$Calendar_Date]["Date"]
                      = "$Calendar_Date";                      
                            }
                            ?>
                <?php	
                
                 $Search = mysqli_query($con,"SELECT * FROM roadmoto_booking WHERE (((pickup_date LIKE '$Start_Year-$Start_Month%') OR (pickup_date LIKE '$Year_Check_Last%')) AND cancelled='False') ORDER BY asset_type, asset_id");
    
                while($Booking_Found = mysqli_fetch_array($Search))
                { 
                      $Bookd_PickupO    = $Booking_Found['pickup_date'];
                      $Bookd_Pickup     = strtotime("$Bookd_PickupO");
                      $Bookd_ReturnO    = $Booking_Found['return_date'];
                      $Bookd_Return     = strtotime("$Bookd_ReturnO");
                      $Bookd_Locate     = $Booking_Found['location_id'];
                      $Bookd_Deposit    = $Booking_Found['deposit_status'];
                      $Bookd_Contract   = $Booking_Found['contract_onfile'];
                      $Bookd_DepTotal   = $Booking_Found['deposit_total'];
                      $Bookd_CompPick   = $Booking_Found['pickup_form'];
                      $Bookd_CompDrop   = $Booking_Found['return_form'];
                      $Bookd_Returned   = $Booking_Found['returned'];
                      $Bookd_LateDay    = $Booking_Found['late_days'];
                      $Bookd_Automate   = $Booking_Found['automated'];
                      $Bookd_Overbook   = $Booking_Found['overbooked'];
                      $Bookd_Cancel     = $Booking_Found['cancelled'];
                      $Bookd_Protect    = $Booking_Found['protection'];
                      $Bookd_Upgrade    = $Booking_Found['upgrade'];
                      $Bookd_Type       = $Booking_Found['asset_type'];
                      $Bookd_AsstID     = $Booking_Found['asset_id'];
                      $Bookd_Cust       = $Booking_Found['customer_id'];
                      $Bookd_ID         = $Booking_Found['ID'];
                      $Bookd_PDFFile    = $Booking_Found['receipt_file'];
                      $Bookd_Receipt    = $Booking_Found['receipt_form'];
                      $Bookd_Invoice    = $Booking_Found['invoice_sent'];
                      $Bookd_Confirm    = $Booking_Found['confirmation_sent'];
                      $Bookd_Remind     = $Booking_Found['reminder_sent'];

                    $Customers_All = mysqli_query($con,"SELECT * FROM roadmoto_customers WHERE ID='$Bookd_Cust' LIMIT 1");
                        while($CustmersIn = mysqli_fetch_array($Customers_All))
                        {
                            $Custm_Legal         = $CustmersIn['legal_name'];
                        }
                        
                      if($Bookd_Type=="Enclosed") { $color= "primary"; }
                      if($Bookd_Type=="Flatbed") { $color= "info"; } 
                      if($Bookd_LateDay>=1) { $color= "danger"; }
                      if($Bookd_Cancel=="True") { $color= "warning"; }
                      if($Bookd_AsstID=="") { $Bookd_AsstID = "-"; }
                      $Custm_Legal = substr($Custm_Legal, 0, 12);
                      
                      $payment="";
                      if(($Bookd_DepTotal>50.00) || ($Bookd_DepTotal>50))
                      {     
                            $payment="$"; 
                            $payment_color="success";  
                      }
                      else
                      { 
                            $payment="$"; 
                            $payment_color="default"; 
                            $color="danger"; 
                      }

                      $start    = new DateTime($Bookd_PickupO);
                      $ending   = new DateTime($Bookd_ReturnO);
                      $ending   ->modify('+1 day');
                      if($Bookd_LateDay>=1) { 
                          $ending   ->modify('+'.$Bookd_LateDay.' day'); }

                      $interval = new DateInterval('P1D');
                      $daterange = new DatePeriod($start, $interval,$ending);
                      
                      foreach($daterange as $bookdate){
                      $Calendar_Date = $bookdate->format("Y-m-d"); 

                      $LastDay = "False";
                      if($Bookd_ReturnO==$Calendar_Date)
                      {
                          $LastDay = "True";
                      }
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Type"] = "$Bookd_Type";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Name"] = "$Custm_Legal";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Date"] = "$Calendar_Date";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Asset"] = "$Bookd_AsstID";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Location"] = "$Bookd_Locate";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Color"] = "$color";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["LastDay"] = $LastDay;
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Payment"] = "$payment";
                      $Global_Events[$Calendar_Date][$Bookd_ID]["Payment_Color"] = "$payment_color";             
                      $Global_Events[$Calendar_Date]["Daily_Maximum"] = $Assets_Active_Found;                      
                      }
                }
                ?>                            
                    <?php foreach ($Global_Calendar as &$value) { 
                    $Calendar_Count = 0;
                    $Text_Color_Format = "Black";
                    $Calendar_Date  = $value["Date"];
                    $Calendar_Month = $Global_Calendar[$Calendar_Date]["Month"];
                    $Format++;
                    ?>
                    <td bgcolor=""><span style="float: right;"><?php echo $value["DayName"]; ?></font>&nbsp;<font style="font-weight: bold;" color="<?php echo $Text_Color_Format; ?>"><?php echo $value["Day"]; ?></b></font></span><br>
                    <?php foreach ($Global_Events as $key => $event) {
                          $Daily_Maximum = $Assets_Active_Found; // Maximum to show per day left. 
                        
                          $Security_DoubleBook = array();
                          foreach ($event as $key => $booking)  {

                               $Event_Date     = $booking["Date"];
                               $Event_Type     = $booking["Type"];
                               $Event_Color    = $booking["Color"];
                               $Event_Name     = $booking["Name"];
                               $Event_Asset    = $booking["Asset"];
                               $Event_Location = $booking["Location"];
                               $Event_Payment  = $booking["Payment"];
                               $Event_PayColor = $booking["Payment_Color"];
                               $Event_LastDay  = $booking["LastDay"];
                           ?>
                           <?php if($Event_Date==$Calendar_Date) { 
                           $Calendar_Count++; 
                           $Global_Events[$Calendar_Date]["Daily_Maximum"]--;
                            ?>
                            <?php } ?>
                            <?php } 
                            
                             $Last_Date = $Calendar_Date;
                            } // Looping Through Bookings
                            $Temp_Maximum = $Global_Events[$Calendar_Date]['Daily_Maximum'];
                            $Temp_Color  = "success";
                            if($Temp_Maximum<=0) {  $Temp_Color  = "danger"; }
                            if($Temp_Maximum==1) {  $Temp_Color  = "warning"; }
                            if($Temp_Maximum==2) {  $Temp_Color  = "warning"; }
                            if($Temp_Maximum>=3) {  $Temp_Color  = "success"; }
                            
                            if($Calendar_Count=="0") 
                            { 
                                $Utilization = 0;
                            }
                            else
                            {
                                $Utilization = round(((abs($Temp_Maximum -$Assets_Active_Found) / $Assets_Active_Found) * 100),0);
                                if($Utilization>100) { $Utilization = "100"; }
                            }
                             
                            //echo "UR: ".$Utilization;
                            //echo "<br>DATE: " . $Calendar_Date;  

                             $result = mysqli_query($con,"UPDATE roadmoto_dynamiccalendar
                            SET utilization='$Utilization' WHERE date='$Calendar_Date'");   
 ?> 
                            </td>
                    <?php if ($Format == 3) { $Format="0"; echo "</tr><tr>"; }
                    } ?>
                                </tbody>
                              </table>
<?php     
$Year_Check_Last  = $Year_Check_Start;
$Year_Check_Start = strtotime("+1 month", $Year_Check_Start);
} ?>
#!/usr/bin/env python3
import mysql.connector
import dbConnection
from datetime import datetime

conn = dbConnection.connect()

try:       

    Bookd_CurrentDate = datetime.today().strftime('%Y-%m-%d')
    mycursor = conn.cursor(dictionary = True)
    sql_query = ("SELECT * FROM roadmoto_booking WHERE completed!= %s AND authorization= %s AND total_onfile= %s")
    select_value1 = "True"
    select_value2 = "Authorized"
    select_value3 = "0"
    mycursor.execute(sql_query, (select_value1, select_value2, select_value3))
    Bookings_All = mycursor.fetchall()

    for booking in Bookings_All:
        
        Estimated_Total       = 0.00
        Bookd_Identif         = booking['ID']
        Bookd_Custome         = booking['customer_id']
        Bookd_GrandTotal      = booking['grand_total']
        Bookd_PickupDate      = booking['pickup_date']
        Current_Date          = datetime.strptime(Bookd_CurrentDate, '%Y-%m-%d')
        Future_Pickup         = datetime.strptime(Bookd_PickupDate, '%Y-%m-%d')
        Days                  = Current_Date - Future_Pickup
        
        
        if Days.days <= 3 & Days.days >= 0:
            
            sql_query = ("SELECT * FROM roadmoto_payments WHERE bookingid = %s AND transactiontag= %s LIMIT 1")
            select_value1 = Bookd_Identif
            select_value2 = "estimatedtotal"

            mycursor.execute(sql_query, (select_value1, select_value2))
            Deposits_All = mycursor.fetchall()

            Total_Found = "False"

            for DepositIn in Deposits_All:
                Total_Found = "True"
                Deps_Identif = DepositIn['ID']
                
            if Total_Found == "False":
                sql = "INSERT INTO roadmoto_payments (transactiontag, transactionflag, transactiontype, transactionstatus, total, bookingid, customerid, origin) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
                val = ("estimatedtotal", 1, "charge", "pending", Bookd_GrandTotal, Bookd_Identif, Bookd_Custome, "Stripe")
                mycursor.execute(sql, val)
                conn.commit()

except mysql.connector.Error as err:
    print(err)

finally:
    conn.close()




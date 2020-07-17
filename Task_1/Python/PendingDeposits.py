#!/usr/bin/env python3
import mysql.connector
import dbConnection

conn = dbConnection.connect()

try:
    mycursor = conn.cursor(dictionary = True)
    sql_query = ("SELECT * FROM roadmoto_booking WHERE completed = %s")
    select_value = "False"
    mycursor.execute(sql_query, (select_value,))
    Bookings_All = mycursor.fetchall()

    for BookingsIn in Bookings_All:       
        Bookd_Terms          = ""

        Bookd_Identif       = BookingsIn['ID']
        Bookd_Custome       = BookingsIn['customer_id']

        sql_query = ("SELECT * FROM roadmoto_payments WHERE bookingid = %s AND transactiontag= %s LIMIT 1")
             
        select_value1       = Bookd_Identif
        select_value2       = "deposit"
        mycursor.execute(sql_query, (select_value1, select_value2))
        Deposits_All        = mycursor.fetchall()

        Deposit_Found       = "False"   
        
        for DepositIn in Deposits_All:
            Deposit_Found = "True"
            Deps_Identif  = DepositIn['ID']

        if Deposit_Found == "False":
            sql = "INSERT INTO roadmoto_payments (transactiontag, transactionflag, transactiontype, transactionstatus, total, bookingid, customerid, origin) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)"
            val = ("deposit", 1, "charge", "pending", 200.00, Bookd_Identif, Bookd_Custome, "Stripe")
            mycursor.execute(sql, val)
            conn.commit()

except mysql.connector.Error as err:
    print(err)

finally:
    conn.close()




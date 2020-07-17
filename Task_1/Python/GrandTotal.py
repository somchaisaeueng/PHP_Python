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

    for booking in Bookings_All:
        
        Book_Identif          = booking['ID']
        Book_RentalTotal      = booking['rental_total']
        Book_SurchageTotal    = booking['rental_surcharge']
        Book_BaseSurcharge    = booking['late_surcharge_total']
        Book_ProtectTotal     = booking['protection_total']
        Book_UpgradeTotal     = booking['upgrade_total']
        Book_LaterPickChg     = booking['late_pickupcharge']
        Book_LateDropChg      = booking['late_dropoffcharge']
        Book_IncidentTotal    = booking['incident_total']
        Book_UpsellTotal      = booking['upsell_total']
        Book_SafetyFee        = booking['safetyfee_total']
        Book_ConcessionFee    = booking['concessionfee']
        Book_EnvironmentalFee = booking['environmentalfee']
        Book_DiscountTotal    = booking['discount_total']

        Grand_Total = Book_RentalTotal + Book_BaseSurcharge + Book_SurchageTotal + Book_ProtectTotal + \
            Book_UpgradeTotal + Book_LaterPickChg + Book_LateDropChg + Book_IncidentTotal + Book_UpsellTotal +  \
            Book_SafetyFee + Book_ConcessionFee + Book_EnvironmentalFee + Book_DiscountTotal

        Grand_Total = round(Grand_Total, 2)

        sql = "UPDATE roadmoto_booking SET grand_total = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Grand_Total, Book_Identif)) 
        conn.commit()

except mysql.connector.Error as err:
    print(err)

finally:
    conn.close()






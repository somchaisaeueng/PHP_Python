#!/usr/bin/env python3
import mysql.connector
import dbConnection

conn = dbConnection.connect()

AssetRates    = {}
FacilityRates = {}

Concession_Rate_Base              = 0.03
Concession_Rate_Disc              = 0.025

Environmental_Rate_Base           = 0.09
Environmental_Rate_Daily          = 3.00
Environmental_Rate_DailyMax       = 40.00

try:
    mycursor = conn.cursor(dictionary = True)
    sql_query = ("SELECT * FROM roadmoto_assets")
    mycursor.execute(sql_query)
    Assets_All = mycursor.fetchall()

    for AssetsIn in Assets_All:       
        Asset_Identif               = AssetsIn['asset_number']
        Asset_Rate                  = AssetsIn['environmental_rate']
        AssetRates[Asset_Identif]   = Asset_Rate

    sql_query = ("SELECT * FROM roadmoto_facilities WHERE Status= %s")
    select_value = "Active"
    mycursor.execute(sql_query, (select_value,))
    Facilities_All = mycursor.fetchall()

    for FacilityIn in Facilities_All: 

        Facility_Identif                    = FacilityIn['ID']
        Facility_Rate                       = FacilityIn['concession_rate']
        FacilityRates[Facility_Identif]     = Facility_Rate
    
    sql_query = ("SELECT * FROM roadmoto_booking WHERE completed= %s AND asset_id != %s")
    select_value = "False"
    select_value1 = ""
    mycursor.execute(sql_query, (select_value, select_value1))
    Bookings_All = mycursor.fetchall()

    for BookingsIn in Bookings_All:
        Concession_Rate                     = 0.00
        Concession_Base_Total               = 0.00

        Concession_Base_Total               = 0.00
        Concession_Disc_Total               = 0.00
        Concession_Surc_Total               = 0.00

        Environmental_Base_Total            = 0.00
        Environmental_Daily_Total           = 0.00
        Environmental_Surc_Total            = 0.00

        Book_Identif                        = BookingsIn['ID']
        Book_AssetIdentif                   = BookingsIn['asset_id']
        Book_FacilityIdentif                = BookingsIn['location_id']
        
        Book_RentalDays                     = BookingsIn['rental_days']
        Book_RentalTotal                    = BookingsIn['rental_total']
        Book_RentalDiscount                 = BookingsIn['discount_total']
        # FULL BASE
        Concession_Base_Total               = Concession_Rate_Base * Book_RentalTotal
        Concession_Base_Total               = round(Concession_Disc_Total, 2)
        
        Concession_Disc_Total               = Concession_Rate_Disc * (Book_RentalTotal - Book_RentalDiscount)
        Concession_Disc_Total               = round(Concession_Disc_Total, 2)        
        
        Concession_Surc_Total               = FacilityRates[Book_FacilityIdentif] * (Book_RentalTotal - Book_RentalDiscount)
        Concession_Surc_Total               = round(Concession_Surc_Total, 2)

        Concession_Rate                     = Concession_Base_Total + Concession_Disc_Total + Concession_Surc_Total

        Environmental_Base_Total            = Environmental_Rate_Base
        Environmental_Base_Total            = round(Environmental_Base_Total, 2)

        Environmental_Daily_Total           = Environmental_Rate_Daily * Book_RentalDays

        if Environmental_Daily_Total >= Environmental_Rate_DailyMax:
            Environmental_Daily_Total = Environmental_Rate_DailyMax

        Environmental_Daily_Total           = round(Environmental_Daily_Total, 2)

        Environmental_Surc_Total            = AssetRates[Book_AssetIdentif] *  (Book_RentalTotal - Book_RentalDiscount)
        Environmental_Surc_Total            = round(Environmental_Surc_Total, 2)

        Environmental_Rate                  = Environmental_Base_Total + Environmental_Daily_Total + Environmental_Surc_Total 

        sql = "UPDATE roadmoto_booking SET concessionfee = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Concession_Rate, Book_Identif)) 
        conn.commit()

        sql = "UPDATE roadmoto_booking SET environmentalfee = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Environmental_Rate, Book_Identif)) 
        conn.commit()

except mysql.connector.Error as err:
    print(err)

finally:
    conn.close()




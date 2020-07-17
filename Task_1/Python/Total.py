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

        Book_RentalDays       = booking['rental_days']
        Book_RentalRate       = booking['rental_rate']

        Book_LateDays         = booking['late_days']
        Book_LateRate         = booking['late_surcharge']

        Book_Protection       = booking['protection']
        Book_Upgrade          = booking['upgrade']
        Book_ProtectDays      = booking['protection_days']
        Book_UpgradeDays      = booking['upgrade_days']
        Book_ProtectRate      = booking['protection_rate']
        Book_UpgradeRate      = booking['upgrade_rate']

        Rental_Total          = float(Book_RentalDays) * float(Book_RentalRate)
        Rental_Total          = round(Rental_Total, 2)

        Late_Total            = Book_LateDays * Book_LateRate
        Late_Total            = round(Late_Total, 2) 

        if Book_Protection == "1":
            Protect_Total     = Book_ProtectDays * Book_ProtectRate
            Protect_Total     = round(Protect_Total, 2)
        elif Book_Protection !="1":
            Protect_Total     = 0.00

        if Book_Upgrade == "1":
            Upgrade_Total     = Book_UpgradeDays * Book_UpgradeRate
            Protect_Total     = round(Upgrade_Total, 2)
        elif Book_Protection !="1": 
            Upgrade_Total     = 0.00
        
        sql = "UPDATE roadmoto_booking SET rental_total = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Rental_Total, Book_Identif)) 
        conn.commit()

        sql = "UPDATE roadmoto_booking SET late_surcharge_total = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Late_Total, Book_Identif)) 
        conn.commit()

        sql = "UPDATE roadmoto_booking SET upgrade_total = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Upgrade_Total, Book_Identif)) 
        conn.commit()

except mysql.connector.Error as err:
    print(err)        

finally:
    conn.close()



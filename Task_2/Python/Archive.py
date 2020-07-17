#!/usr/bin/env python3
import mysql.connector
import dbConnection
import datetime

TodayFormatDt                 = datetime.datetime.now()
def Weekend(TodayFormatDt):
    weekno = TodayFormatDt.weekday()
    if weekno < 5:
        print ("weekday")
    else:
        print ("weekend")
# Weekend(TodayFormatDt)
conn = dbConnection.connect()

try:
    mycursor = conn.cursor(dictionary = True)
    sql_query = ("SELECT * FROM roadmoto_dynamiccalendar WHERE advanced= %s")
    select_value = 5
    mycursor.execute(sql_query, (select_value,))
    Calendar_All = mycursor.fetchall()
    print (Calendar_All)
    for CalendarIn in Calendar_All:       
        Book_Weekday          = 0
        Book_Weekend          = 0
        Book_Holiday          = 0
        Book_Event            = 0
        Book_Popular          = 0
        Book_Advanced         = 0
        Book_UR               = 0

        Book_ID               = CalendarIn['ID']
        Book_Date             = CalendarIn['date']

        Book_Weekend          = CalendarIn['weekend']
        Book_Weekday          = CalendarIn['weekday']

        Book_Holiday          = CalendarIn['holiday']
        Book_Event            = CalendarIn['event']
        Book_Popular          = CalendarIn['popular']
        Book_Advanced         = CalendarIn['advanced']
        Book_UR               = CalendarIn['utilization']
        Book_Location         = CalendarIn['location']
        Book_Class            = CalendarIn['class']
        Book_Surcharge        = CalendarIn['surcharge']

        print(Book_Weekday)

        sql = "INSERT INTO roadmoto_historicalcalendar (calendar_id, date, weekend, weekday, holiday, event, popular, advanced, utilization, location, class, surcharge) VALUES (%s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s)"
        val = (Book_ID, Book_Date, Book_Weekend, Book_Weekday, Book_Holiday, Book_Event, Book_Popular, Book_Advanced, Book_UR, Book_Location, Book_Class, Book_Surcharge)
        mycursor.execute(sql, val)
        conn.commit()

        sql = "DELETE FROM roadmoto_dynamiccalendar WHERE ID = %s"
        val = (Book_ID, )
        mycursor.execute(sql, val)
        conn.commit()
        

except mysql.connector.Error as err:
    print(err)        

finally:
    conn.close()



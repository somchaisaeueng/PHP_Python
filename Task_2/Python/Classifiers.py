import mysql.connector
import dbConnection
from datetime import datetime

TodayFormatDt                  = datetime.today().strftime('%Y-%m-%d')
def isWeekend(date):
    weekno = date.weekday()
    if weekno > 4:
        return ("1")
    else:
        return ("")

conn = dbConnection.connect()

try:
    mycursor = conn.cursor(dictionary = True)
    mycursor.execute("SELECT * FROM roadmoto_dynamiccalendar")
    Calendar_All = mycursor.fetchall()
    
    for CalendarIn in Calendar_All: 

        LearningClass         = ""
        Holiday               = 0
        Event                 = 0
        Popular               = 0
        Advanced              = 0

        Book_Date             = CalendarIn['date']
        Book_ID               = CalendarIn['ID']

        # print(Book_Date)

        sql_query = ("SELECT * FROM roadmoto_dynamiclearning WHERE date= %s")
        select_value = Book_Date
        mycursor.execute(sql_query, (select_value,))
        Learning_All = mycursor.fetchall()
        # print(CalendarIn)
        # print(Learning_All)

        for LearningIn in Learning_All:
            LearningClass     = LearningIn['classification']
            if LearningClass == "EVENT":
                Event         = 1

            if LearningClass == "HOLIDAY":
                Holiday       = 1
                
            if LearningClass == "POPULAR":
                Popular       = 1
                
        Calendar_Today        = datetime.strptime(TodayFormatDt, '%Y-%m-%d')
        Calendar_Select       = datetime.strptime(Book_Date, '%Y-%m-%d')

        Advanced              = Calendar_Select - Calendar_Today

        sql = "UPDATE roadmoto_dynamiccalendar SET holiday = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Holiday, Book_ID)) 
        conn.commit()

        sql = "UPDATE roadmoto_dynamiccalendar SET event = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Event, Book_ID)) 
        conn.commit()

        sql = "UPDATE roadmoto_dynamiccalendar SET popular = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Popular, Book_ID)) 
        conn.commit()

        sql = "UPDATE roadmoto_dynamiccalendar SET advanced = %s WHERE ID = %s"        
        result = mycursor.execute(sql, (Advanced.days, Book_ID)) 
        conn.commit()

except mysql.connector.Error as err:
    print(err)        

finally:
    conn.close()



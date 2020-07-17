#!/usr/bin/env python3
import mysql.connector
import dbConnection
import datetime

TodayFormatDt                      = datetime.date.today()

print(TodayFormatDt)
def isWeekend(date):
    weekno = date.weekday()
    if weekno > 4:
        return ("1")
    else:
        return ("")
print(isWeekend(TodayFormatDt))
# Weekend(TodayFormatDt)
conn = dbConnection.connect()

try:
    mycursor = conn.cursor(dictionary = True)
    begin                          = datetime.datetime.strptime("2040-07-01", "%Y-%m-%d")
    
    
    print(begin)
    end                            = datetime.datetime.strptime("2050-07-31", "%Y-%m-%d")
    period                         = [begin + datetime.timedelta(days=x) for x in range(0, (end-begin).days+1)]
    
    for dt in period:       
        weekend                    = 0
        weekday                    = 0
        result                     = dt
        weekend                    = isWeekend(result)
        if weekend == "":
            weekend                = 0
            weekday                = 1

        sql = "INSERT INTO roadmoto_dynamiccalendar (date, weekend, weekday) VALUES (%s, %s, %s)"
        val = (result, weekend, weekday)
        mycursor.execute(sql, val)
        conn.commit()

except mysql.connector.Error as err:
    print(err)        

finally:
    conn.close()



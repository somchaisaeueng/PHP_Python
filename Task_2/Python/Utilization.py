import mysql.connector
import dbConnection
from datetime import datetime
from datetime import timedelta
from datetime import date

Current_Month                    = datetime.today().strftime('%m')
Current_Year                     = datetime.today().strftime('%Y')
Next_Year                        = date.today().year + 1
# Next_Year                        =datetime.datetime.today() + datetime.timedelta(days=1)

Year_Check_Start                 = datetime.today().strftime('%Y-%m')
begin                            = datetime.strptime(Year_Check_Start, "%Y-%m")
Year_end                         = datetime.now() + timedelta(days=365)
Year_Check_end                   = Year_end.strftime('%Y-%m') 
end                            = datetime.strptime(Year_Check_end, "%Y-%m")


  
# try:
# mycursor = conn.cursor(dictionary = True)
period                           = [begin + timedelta(days=x) for x in range(0, (end-begin).days+1)]
print(period)












# except mysql.connector.Error as err:
#     print(err)        

# finally:
#     conn.close()
      






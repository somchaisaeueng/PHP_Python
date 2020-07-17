import configparser
import mysql.connector

config = configparser.ConfigParser()
config.read('config.ini')

def connect():
    try: 
        return mysql.connector.connect(
            host = config['mysqlDB']['host'],
            user = config['mysqlDB']['user'],
            password = config['mysqlDB']['pass'],
            database = config['mysqlDB']['db'])
    except mysql.connector.Error as err:
        if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
            print("Something is wrong with your user name or password")
        elif err.errno == errorcode.ER_BAD_DB_ERROR:
            print("Database does not exist")
        else:
            print(err)
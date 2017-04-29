#!/usr/bin/python
__author__ = 'Edzima'

from mysql.connector import MySQLConnection, Error
from db_config import read_db_config


def connect():
    db_config = read_db_config()

    try:
        print("Connecting to DB....")
        conn = MySQLConnection(**read_db_config())


        if conn.is_connected():
            print("Conneection established.")
        else:
            print("Cinnection failed")
    except Error as error:
        print(error)
    finally:
        conn.close()
        print"Connection closed" \

if __name__ == '__main__':
    connect()



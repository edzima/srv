#!/usr/bin/python
__author__ = 'edzi'
import mysql.connector
from db_config import read_db_config

class DataBase:
    conn=mysql.connector.Connect
    cursor =''
    testID=0

    def connect(self):
        try:
            print("Connecting to DB....")

            self.conn = mysql.connector.connect(**read_db_config())
            if  self.conn.is_connected():
                self.cursor  = self.conn.cursor()
                print("Conneection established.")
            else:
                print("Cinnection failed")
        except mysql.connector.Error as error:
            print(error)

    def __addItem(self,query,data):
        try:
            self.cursor.execute(query,data)
            self.conn.commit()
        except mysql.connector.Error as error:
            print (error)

    def addVibration(self,data):
        data += (self.testID,)
        query = ("INSERT INTO vibration "
                 "(time, sensor_id, peakForce, test_id) "
                 "VALUES (NOW(2), %s, %s,%s)")
        self.__addItem(query,data)

    def addGPS(self,data):
        data += (self.testID,)
        query = ("INSERT INTO gps "
                "(time,  longitude, latitude,altitude,speed,test_id)"
                 "VALUES (NOW(2), %s,%s,%s,%s,%s)")
        self.__addItem(query, data)

    def addAccAndGyro(self, data):
        data += (self.testID,)
        query = ("INSERT INTO test_acc_and_gyro "
                "(time, x,y,z,gx,gz,gy,test_id)"
                 "VALUES (%s,%s,%s,%s,%s,%s,%s,%s)")
        self.__addItem(query, data)

    def addManyAccAndGyro(self, data):
        query = ("INSERT INTO test_acc_and_gyro "
        "(time, x,y,z,gx,gz,gy,test_id)"
        "VALUES (%s,%s,%s,%s,%s,%s,%s,%s)")
        try:
            self.cursor.executemany(query,data)
            self.conn.commit()
        except mysql.connector.Error as error:
            print (error)


    def setTestID(self,testID):
        self.testID = testID

    def __init__(self):
        self.connect()


    def __del__(self):

        if self.conn:
            if self.cursor:
                self.cursor.close()
            self.conn.close()
            print 'Connection close'

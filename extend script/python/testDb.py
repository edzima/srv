#! /usr/bin/python
# __author__ = 'edzi'


import db
import GPS
import MPU6050
import time
import thread


#initial data to accelerometer



gps = GPS.GPS()
if gps.isConnected:
    dataBase = db.DataBase()
    gpsData = GPS.GPSData

    while True:

        gpsData = gps.readGPS()
        if gpsData:
            print gpsData.speed
            #dataBase.addGPS(( gpsData.longitude, gpsData.latitude,gpsData.altitude,gpsData.speed, 1))






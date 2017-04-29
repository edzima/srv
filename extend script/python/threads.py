#!/usr/bin/env python
# -*- coding: UTF-8 -*-
__author__ = 'edzi'

import threading
import time
import MPU6050
import GPS
import db
import sys
import json
import os

import TCP

import socket
import sys

from thread import *

vib =[]

f = open("/app/storage/json/gps.json","w")
f.close()

class AccThread(threading.Thread):
    def __init__(self, sensor, testID):
        self.sensor = sensor
        threading.Thread.__init__(self)
        self.dataBase = db.DataBase()
        self.testID = testID
        self.f = "/app/storage/json/acc.json"
        self.acc = []

    def run(self):
        #init value Accelerometer
        initIn = self.sensor.readData()
        #round 2 points decimal
        initIn.round2()
        self.dataBase.setTestID(int(sys.argv[1]))
        #thread  data to JSON on server
        saveJson = JsonSave(self.f,self.acc)
        saveJson.start()
        while 1:
            try:
                time.sleep(0.08)
                data = self.sensor.readData()
                data.delta(initIn)
                data.round2()
                ct = time.time()*1000
                #add to array data
                #self.acc.append([ct,data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz])
                #self.dataBase.addAccAndGyro((ct,data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz))
                #print("x: {0}, y: {1}, z:{2}".format(data.Gx,data.Gy, data.Gz))
                self.acc.append([ct,data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz,self.testID])
                if len(self.acc)%10==0:
                    self.dataBase.addManyAccAndGyro(self.acc[-10:])
            except Exception, accE:
                print "Accelerometer error", accE

class GpsThread(threading.Thread):
    def __init__(self):
        self.gps = GPS.GPS()
        self.gpsData = GPS.GPSData
        self.dataBase = db.DataBase()
        self.gpsAr = []
        self.f = "/app/storage/json/gps.json"
        threading.Thread.__init__(self)
    def run(self):
        #self.gps.turnOn()
        self.dataBase.setTestID(int(sys.argv[1]))
        saveJson = JsonSave(self.f,self.gpsAr)
        saveJson.start()
        while True:
            if self.gps.isConnected:
                self.gpsData = GPS.GPSData
                self.gpsData = self.gps.readGPS()
                if self.gpsData:
                    cT = time.time()*1000
                    self.gpsAr.append([cT,self.gpsData.speed])
                    self.dataBase.addGPS(( self.gpsData.longitude, self.gpsData.latitude,self.gpsData.altitude,self.gpsData.speed))
                else:
                    print"gps not find satelite"
            else:
                print "gps not turn on"
class VibrationIn(threading.Thread):
    def __init__(self, sensor):
        self.inSensor = sensor
        self.dataBase = db.DataBase()
        threading.Thread.__init__(self)
    def run(self):
        self.dataBase.setTestID(int(sys.argv[1]))
        while True:
            try:
                time.sleep(0.08)
                inForce=self.inSensor.detectVibration("IN")
                if inForce:
                    #curTime = time.time()*1000
                    #vIn = {'sensor_id': 1, 'time' : curTime, 'peakForce' : inForce}
                    #vib.append([curTime,inForce])
                    #vib.append(vIn)
                    self.dataBase.addVibration((1,inForce))
            except Exception, e1:
                print "Vibration error", e1

class JsonSave(threading.Thread):
    def __init__(self, fileName, arrToSave):
        self.status = True
        self.fN = fileName
        self.l = arrToSave
        threading.Thread.__init__(self)

    def run(self):
        while self.status:
            out_file = open(self.fN,"w")
            try:
                # (the 'indent=4' is optional, but makes it more readable)
                json.dump(self.l,out_file)
            finally:
                out_file.close()
                time.sleep(0.7)




# firt argument is testID
if sys.argv[1].isdigit()>0:
    try:

        inSensor = MPU6050.MPU6050()
        inSensor.setAddress(0x69)
        inSensor.setSampleRate(1000)
        inSensor.setGResolution(16)
        inSensor.setup()

        #connect Database
        acc = AccThread(inSensor, sys.argv[1])
        acc.start()
        gps = GpsThread()
        gps.start()
        v = VibrationIn(inSensor)
        v.start()

        TCPserver = TCP.ThreadedServer('192.168.22.1',5000,sys.argv[1])
        TCPserver.listen()

    finally:
        #s.close()
        print 'end'

else:
    print "null test id"

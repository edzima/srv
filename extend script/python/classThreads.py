import threading
import json
import db
import time

class AccReadAndSaveDB(threading.Thread):
    def __init__(self, sensor, testID):
        self.sensor = sensor
        self.testID = testID
        threading.Thread.__init__(self)
        self.dataBase = db.DataBase()
        self.f = "/app/storage/json/acc.json"
        self.acc = []

    def run(self):
        initIn = self.sensor.readData()
        initIn.round2()
        self.dataBase.setTestID(self.testID)
        #thread  data to JSON on server
        saveJson = JsonSave(self.f,self.acc)
        saveJson.start()
        print("AccThread start")
        while 1:
            try:
                time.sleep(0.08)
                data = self.sensor.readData()
                data.delta(initIn)
                data.round2()
                ct = time.time()*1000
                self.acc.append([ct,data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz,self.testID])
                if len(self.acc)%10==0:
                    self.dataBase.addManyAccAndGyro(self.acc[-10:])
            except Exception, accE:
                print "Accelerometer error", accE


class OneAccThread(threading.Thread):
    def __init__(self, sensor, testID):
        self.sensor = sensor
        self.testID = testID
        threading.Thread.__init__(self)
        self.dataBase = db.DataBase()
        self.f = "/app/storage/json/acc.json"
        self.acc = []
        self.count = count

    def run(self):
        initIn = self.sensor.readData()
        initIn.round2()
        self.dataBase.setTestID(self.testID)
        #thread  data to JSON on server
        saveJson = JsonSave(self.f,self.acc)
        saveJson.start()
        print("OneAccThread start")
        while 1:
            try:
                time.sleep(0.08)
                data = self.sensor.readData()
                data.delta(initIn)
                data.round2()
                ct = time.time()*1000
                #add to array data
                self.acc.append([ct,data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz,self.testID])
                self.dataBase.addAccAndGyro((data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz))
                #print("x: {0}, y: {1}, z:{2}".format(data.Gx,data.Gy, data.Gz))
            except Exception, accE:
                print "Accelerometer error", accE

class GpsThread(threading.Thread):
    def __init__(self):
        self.gps = GPS.GPS()
        self.gpsData = GPS.GPSData
        self.dataBase = db.DataBase()
        self.gpsAr = []
        threading.Thread.__init__(self)
    def run(self):
        self.dataBase.setTestID(int(sys.argv[1]))

        while True:
            if self.gps.isConnected:
                self.gpsData = GPS.GPSData
                self.gpsData = self.gps.readGPS()
                if self.gpsData:
                    cT = time.time()*1000
                    self.gpsAr.append([cT,self.gpsData.speed, self.gpsData.latitude, self.gpsData.longitude])
                    self.dataBase.addGPS(( self.gpsData.longitude, self.gpsData.latitude,self.gpsData.altitude,self.gpsData.speed))
                    out_file = open("/app/storage/json/gps.json","w")
                    try:
                        # (the 'indent=4' is optional, but makes it more readable)
                        json.dump(self.gpsAr,out_file)
                    finally:
                        out_file.close()
                else:
                    print"gps not find satelite"
            else:
                print "gps not turn on"


class JsonSave(threading.Thread):
    def __init__(self, fileName, arrToSave):
        self.status = True
        self.fN = fileName
        self.l = arrToSave
        threading.Thread.__init__(self)

    def run(self):
        while self.status:
            try:
                out_file = open(self.fN,"w")
                # (the 'indent=4' is optional, but makes it more readable)
                json.dump(self.l,out_file)
            finally:
                out_file.close()
                time.sleep(0.7)

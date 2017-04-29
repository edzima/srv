#!/usr/bin/env python
# -*- coding: UTF-8 -*-
__author__ = 'edzi'

import time
import MPU6050
import json
import threading


count =200

class saveToFileThread(threading.Thread):
    def __init__(self, fileName, list):
        self.status = True
        self.fN = fileName
        self.l = list
        threading.Thread.__init__(self)

    def run(self):
        while self.status:
            saveToFile(self.fN,self.l)
            time.sleep(0.9)

inSensor = MPU6050.MPU6050()
inSensor.setAddress(0x68)
inSensor.setSampleRate(1000)
inSensor.setGResolution(16)
inSensor.setup()
i=0
ar = []

def saveToFile(fileName, list):
    out_file = open(fileName,"w")
    try:
        # (the 'indent=4' is optional, but makes it more readable)
        json.dump(list,out_file)
    finally:
        out_file.close()
#thread save
ar=[]
i=0
start = time.time()
#add = saveToFileThread("func.json",ar)
#add.start()
while i < count:
    time.sleep(0.1)
    #data = inSensor.readData()
    curTime = time.time()*1000
    ar.append([i, curTime])
    saveToFile("/app/storage/tmp/app.json",ar)
    i += 1
end = time.time()-start
print 'Time execute with thread save script: ', str(end)
add.status=False


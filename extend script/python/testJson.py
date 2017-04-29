#! /usr/bin/python
import MPU6050
import time
import db
import threading
from datetime import datetime
import json


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
                time.sleep(0.5)


inSensor = MPU6050.MPU6050()
initIn = inSensor.readData()


numberOfTrails =  100

acc2 = '/app/storage/json/acc2.json'
acc = []
count = 0
start = time.time()
while count<numberOfTrails:
	count = count + 1
	try:
		time.sleep(0.1)
		data = inSensor.readData()
		data.round2()
		dt = datetime.now()
		dt.microsecond
		acc.append([str(dt),data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz,62])
	except Exception, accE:
		print "Accelerometer error", accE

end = time.time() - start
print "Script time without thread: ", str(end)


acc =[]
accJson = JsonSave("/app/storage/json/acc.json",acc)
accJson.start()

count = 0
start = time.time()
while count<numberOfTrails:
	count = count + 1
	try:
		time.sleep(0.1)
		data = inSensor.readData()
		data.round2()
		dt = datetime.now()
		dt.microsecond
		acc.append([str(dt),data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz,62])
		#dataBase.addAccAndGyro((123,data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz))
		#print("x: {0}, y: {1}, z:{2}".format(data.Gx,data.Gy, data.Gz))
	except Exception, accE:
		print "Accelerometer error", accE

#dataBase.addManyAccAndGyro(acc)
end = time.time() - start
print "Script time with thread JSON save: ", str(end)

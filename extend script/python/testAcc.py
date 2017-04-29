import MPU6050
import time
import db
import json
import threading
    
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


inSensor = MPU6050.MPU6050()
initIn = inSensor.readData()

initIn = inSensor.readData()

print("Init Value \n x: {0}, y: {1}, z:{2}".format(initIn.Gx,initIn.Gy, initIn.Gz))

#inForce=inSensor.detectVibration("IN")
count = 0
start = time.time()
#dataBase = db.DataBase()
#dataBase.setTestID(62)
while True:
	try:
		time.sleep(1)
		data = inSensor.readData()
		data.delta(initIn)
		data.round2()
		inForce=inSensor.detectVibration("IN")
		if inForce:
		#dataBase.addAccAndGyro((data.Gx,data.Gy,data.Gz,data.Gyrox, data.Gyroy, data.Gyroz))
			print str(inForce)
		print("x: {0}, y: {1}, z:{2}".format(data.Gx,data.Gy, data.Gz))
	except Exception, accE:
		print "Accelerometer error", accE

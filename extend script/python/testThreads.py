import classThreads
import MPU6050
import time
import db
#init Accelerometer Sensor
inSensor = MPU6050.MPU6050()
inSensor.setAddress(0x69)
inSensor.setSampleRate(1000)
inSensor.setGResolution(16)
inSensor.setup()

testID = 62
acc = classThreads.ManyAccThread(inSensor,testID,2000)

acc.start()
acc.join()

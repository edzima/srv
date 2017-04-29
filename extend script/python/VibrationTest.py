#!/usr/bin/python

import MPU6050
import math

import threading


class vibThread(threading.Thread):
    def __init__(self, sensor, sensor_name):
        self.sensor = sensor
        self.sensor_name = sensor_name
        threading.Thread.__init__(self)
    def run(self):
        print "start watku:"
        detectVibration(self.sensor,self.sensor_name)


def detectVibration(sensor, name):
    Threshold = 0.2
    ShakeFlag = False
    while True:
        while (sensor.readStatus() & 1)==0 :
            pass
        #read initial reading and put in I class
        I = sensor.readData()
        PeakForce = 0

        for loop in range(20):
            #wait until new data available
            while (sensor.readStatus() & 1)==0 :
                pass

            # read the accelerometer
            C = sensor.readData()
            #calculate new force
            CurrentForce = C.force(I)

            if CurrentForce > PeakForce :
                PeakForce = CurrentForce

        if PeakForce > Threshold :
            if not(ShakeFlag) :
                ShakeFlag = True
                print ("{0} vibration detected: {1} G".format(name,PeakForce))

        else:

            ShakeFlag= False

def vibration():
    while True:
        detectVibration(mpu6050,"IN")
        detectVibration(mpu6050OUT,"OUT")

mpu6050 = MPU6050.MPU6050()
address = 0x69
mpu6050.setAddress(address)
mpu6050.setup()
mpu6050.setSampleRate(1000)
mpu6050.setGResolution(16)


mpu6050OUT = MPU6050.MPU6050()
mpu6050OUT.setAddress(0x68)
mpu6050OUT.setup()
mpu6050OUT.setSampleRate(1000)
mpu6050OUT.setGResolution(16)
#sometimes I need to reset twice


vibIn = vibThread(mpu6050,"IN")
vibOut = vibThread(mpu6050OUT,"OUT")

vibIn.start()
vibOut.start()

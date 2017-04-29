#!/usr/bin/python

import MPU6050
import math

import numpy
import time

mpu6050OUT = MPU6050.MPU6050()
mpu6050IN = MPU6050.MPU6050()

mpu6050OUT.setAddress(0x68)
mpu6050IN.setAddress(0x69)

mpu6050OUT.setup()
mpu6050OUT.setSampleRate(1000)
mpu6050OUT.setGResolution(16)
#sometimes I need to reset twice
I = mpu6050OUT.readData()

mpu6050IN.setup()
mpu6050IN.setSampleRate(1000)
mpu6050IN.setGResolution(16)
#sometimes I need to reset twice
I = mpu6050IN.readData()


Threshold = 0.2
ShakeFlag = False

count = 0
countIn = 0
sumOUT=[]
sumIN=[]
t0 = time.time()

while True:

  #wait until new data available
  while (mpu6050OUT.readStatus() & 1)==0:
    pass

  #read initial reading and put in I class
  I = mpu6050OUT.readData()

    #wait until new data available
  while (mpu6050IN.readStatus() & 1)==0:
    pass

  #read initial reading and put in I class
  Iin = mpu6050IN.readData()


  PeakForce = 0
  PeakForceIn =0

  for loop in range(20):

     #wait until new data available
     while (mpu6050OUT.readStatus() & 1)==0 & (mpu6050IN.readStatus() & 1)==0 :
       pass

     # read the accelerometer
     C = mpu6050OUT.readData()
     Cin = mpu6050IN.readData()

     #calculate new force
     CurrentForce = math.sqrt( (C.Gx - I.Gx) * ( C.Gx - I.Gx) + 
                               (C.Gy - I.Gy) * ( C.Gy - I.Gy) +
                               (C.Gz - I.Gz) * ( C.Gz - I.Gz))
          #calculate new force
     CurrentForceIn = math.sqrt( (Cin.Gx - Iin.Gx) * ( Cin.Gx - Iin.Gx) +
                               (Cin.Gy - Iin.Gy) * ( Cin.Gy - Iin.Gy) +
                               (Cin.Gz - Iin.Gz) * ( Cin.Gz - Iin.Gz))

     if CurrentForce > PeakForce :
        PeakForce = CurrentForce
     if CurrentForceIn > PeakForce :
        PeakForceIn = CurrentForceIn

   
  if PeakForce > Threshold :
    if not(ShakeFlag) :
        ShakeFlag = True
        count += 1
        sumOUT.append(PeakForce)
        print count,'. OUT Vibration detected ', PeakForce, "G", ' srednia: ', numpy.mean(sumOUT), 'czas: ', time.time()- t0

  if PeakForceIn > Threshold :
    if not(ShakeFlag) :
        ShakeFlag = True
        countIn += 1
        sumIN.append(PeakForce)
        print countIn,'. IN Vibration detected ', PeakForceIn, "G", ' srednia: ', numpy.mean(sumIN), 'czas: ', time.time()- t0


  else:
    ShakeFlag= False


#!/usr/bin/python
import serial
import pynmea2
import time
import RPi.GPIO as GPIO


class GPSData:

  def __init__(self):
     self.longitude=0
     self.latitude=0
     self.altitude=0
     self.speed=0

class GPS:
    pinCheckTurn = 18
    pinPowerSwitch = 23
    ZNANA = 3

    def turnOn(self):
        GPIO.setmode(GPIO.BCM)
        GPIO.setwarnings(False)
        GPIO.setup(self.pinCheckTurn, GPIO.IN)
        GPIO.setup(self.pinPowerSwitch, GPIO.OUT)
        if GPIO.input(self.pinCheckTurn):
            print("GPS is already on")
        else:
            GPIO.output(self.pinPowerSwitch, GPIO.HIGH)
            time.sleep(2)
            GPIO.output(self.pinPowerSwitch, GPIO.LOW)

    def turnOf(self):
        if not GPIO.input(self.pinCheckTurn)==0:
            GPIO.output(self.pinPowerSwitch, GPIO.HIGH)
            time.sleep(2)
            GPIO.output(self.pinPowerSwitch, GPIO.LOW)




    def isReady(self):
        try:
            self.ser.flushInput() #flush input buffer, discarding all its contents
            self.ser.flushOutput() #flush output buffer, aborting current output
            self.ser.write('AT+CGNSPWR=1\r\n')
            time.sleep(0.5) #give the serial port sometime to receive the data
            numOfLines=0
            while numOfLines<5:
                response = self.ser.readline()
                numOfLines = numOfLines + 1
                if response.find('OK')==0:
                    print 'GPS TURN ON'
                    self.ser.write('AT+CGNSTST=1\r\n')
                    time.sleep(0.5) #give the serial port sometime to receive the data
                    numOfLines=0
                    while numOfLines<5:
                         if response.find('OK')==0:
                            print 'SEND GPS DATA TURN ON'
                            self.isConnected=True
                            return True
                         numOfLines= numOfLines+1
                    break
            if self.isConnected:
                print "gps not connected"
                return False

        except Exception, e:
            print 'Serial error ', e
            return False



    def readGPS(self):
        if self.isConnected:
            while True:
                GpsData = GPSData
                sentence = self.ser.readline()
                if sentence.find('GGA') > 0:
                    GGA = pynmea2.parse(sentence)
                    GpsData.longitude= GGA.longitude
                    GpsData.latitude= GGA.latitude
                    GpsData.altitude= GGA.altitude
                    #print "{time}: {lat},{lon}, {altitude}".format(time=GGA.timestamp,lat=GGA.latitude,lon=GGA.longitude, altitude=GGA.altitude)
                if sentence.find('VTG') >0:
                    VTG = pynmea2.parse(sentence)
                    GPSData.speed=VTG.spd_over_grnd_kmph

                    if GPSData.longitude>0: #GPS FIND POSITION
                        print "{lat},{lon}, {altitude}, v= {speed} ".format(lat=GpsData.latitude,lon=GpsData.longitude, altitude=GpsData.altitude, speed=GpsData.speed)
                        return GpsData
                    else:
                        print("GPS NOT FIND SATELITE")
                        return False
        else:
            print "GPS NOT CONNECTED"
            self.isReady()
            return False

    def __del__(self):
        self.turnOf()


    def __init__(self):
        self.ser = serial.Serial("/dev/ttyAMA0", 9600, timeout=0.5)
        self.isConnected = False
        self.turnOn()
        time.sleep(1)
        #Double check so as not to turn off the GPS * if suitable
        self.isConnected = self.isReady()
        #self.isConnected = self.isReady()
        #self.readGPS()

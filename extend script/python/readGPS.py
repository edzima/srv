import serial
import pynmea2
import time
import db

ser = serial.Serial("/dev/ttyAMA0", 9600, timeout=0.5)

#data to add GPS to DB
dataList= [0,0,0,0,1]

#boolean GPS is TURN ON AND SEND DATA
GPSisReady=0

try:
    ser.flushInput() #flush input buffer, discarding all its contents
    ser.flushOutput() #flush output buffer, aborting current output
    ser.write('AT+CGNSPWR=1\r\n')
    time.sleep(0.5) #give the serial port sometime to receive the data
    numOfLines=0
    while 1:
        response = ser.readline()
        if response.find('OK')==0:
            print 'GPS TURN ON'
            ser.write('AT+CGNSTST=1\r\n')
            time.sleep(0.5) #give the serial port sometime to receive the data
            while True:
                 if response.find('OK')==0:
                    print 'SEND GPS DATA TURN ON'
                    GPSisReady=1
                    break
            break
        numOfLines = numOfLines + 1
        if numOfLines>5: #5 null line => GPS not connected or SIM808 isn't turn
            break

    if GPSisReady:
        dataBase = db.DataBase() #init connect to DB
        while True:
            sentence = ser.readline()
            if sentence.find('GGA') > 0:
                GGA = pynmea2.parse(sentence)
                dataList[0]= GGA.longitude
                dataList[1]= GGA.latitude
                dataList[2]= GGA.altitude
                #print "{time}: {lat},{lon}, {altitude}".format(time=GGA.timestamp,lat=GGA.latitude,lon=GGA.longitude, altitude=GGA.altitude)
            if sentence.find('VTG') >0:
                VTG = pynmea2.parse(sentence)
                dataList[3]=VTG.spd_over_grnd_kmph

                if dataList[0]>0: #GPS FIND POSITION
                    print dataList
                    dataBase.addGPS(dataList)
                else:
                    print("GPS NOT FIND SATELITE")

except Exception, e:
    print 'Serial error ', e
finally:
    if GPSisReady:
        ser.write('AT+CGNSTST=0\r\n') #turn off send data to UART
        time.sleep(0.5) #give the serial port sometime to receive the data
        while True:
            response = ser.readline()
            if response.find('OK')==0:
                print 'SEND GPS DATA TURN OFF'
                ser.write('AT+CGNSPWR=0\r\n')
                time.sleep(0.5) #give the serial port sometime to receive the data
                while True:
                     if response.find('OK')==0:
                        print 'GPS TURN OFF'
                        break
                break
    else:
        print("GPS NOT CONNECTED")
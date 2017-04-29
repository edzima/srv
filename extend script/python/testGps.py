import GPS
import RPi.GPIO as GPIO
import time

gps = GPS.GPS()
#gps.turnOn()
GPSdata = GPS.GPSData()

print("start GPS read")

while True:
    gps.readGPS()

#!/usr/bin/python
import serial, time
#initialization and open the port
#possible timeout values:
#	1. None: wait forever, block call
#	2. 0: non-blocking mode, return immediately
#	3. x, x is bigger than 0, float allowed, timeout block call

ser = serial.Serial()
ser.port = "/dev/ttyAMA0"
ser.baoudrate = 115200
ser.bytesize = serial.EIGHTBITS # number of bits per bytes
ser.partity = serial.PARITY_NONE # set parity check: no parity
ser.stopbits = serial.STOPBITS_ONE # number of stop bist
#ser.timeout = None #block read
ser.timeout = 0 #non-block read
#ser.timeout = 2 #timeout block read
ser.xonxoff = False #disable software flow control
ser.rtscts = False #disable hardware (RTS/CTS) flow control
ser.dsrdtr = False #disable hardware (DSR/DTR) flow control

try:
    ser.open()
except Exception, e:
    print "Error open serial port: " +str(e)
    exit()
if ser.isOpen():
    try:
        ser.flushInput() #flush input buffer, discarding all its contents
        ser.flushOutput() #flush output buffer, aborting current output
        #write data
        while True:
            com = raw_input('Podaj poleceniee: \n')
            ser.write(com + '\r\n')
            print("write data: Twoje:! "+com)
            time.sleep(0.5) #give the serial port sometime to receive the data
            numOfLines = 0
            while True:
                response = ser.readline()
                print("read data: " + response)
                numOfLines = numOfLines +1
                if(numOfLines >= 5):
                    break
        ser.close()
    except Exception, e1:
        print "error cummmunicatiing...: " + str(e1)
else:
    print "cannot open serial port"

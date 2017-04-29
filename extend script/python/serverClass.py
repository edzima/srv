#!/usr/bin/env python

import socket
import sys
import threading


class ClientThread(threading.Thread):
    def __init__(self,host,port):

        self.s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        print 'Socket created'
        try:
            self.s.bind((host, port))
        except socket.error , msg:
            print 'Bind failed. Error Code : ' + str(msg[0]) + ' Message ' + msg[1]
            sys.exit()

        print 'Socket bind complete'

        self.s.listen(10)
        print 'Socket now listening'

        threading.Thread.__init__(self)
    def run(self):
        conn, addr = self.s.accept()
        while True:
            #Receiving from client
            data = conn.recv(24)
            print data
            if not data:
                break
        conn.close()

HOST = '192.168.22.1'
PORT = 5000
client = ClientThread(HOST,PORT)
client.start()

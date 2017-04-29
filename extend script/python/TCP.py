import socket
import threading
import db



class ThreadedServer(object):
    def __init__(self, host, port, testID):
        self.host = host
        self.port = port
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        self.sock.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
        self.sock.bind((self.host, self.port))
        self.dataBase = db.DataBase()
        self.dataBase.setTestID(testID)

    def listen(self):
        self.sock.listen(5)
        while True:
            client, address = self.sock.accept()
            client.settimeout(60)
            threading.Thread(target = self.listenToClient,args = (client,address)).start()

    def listenToClient(self, client, address):
        size = 15
        while True:
            try:
                data = client.recv(size)
                if data:
                    # Set the response to echo back the recieved data
                    print data

                    self.dataBase.addVibration((2,data))
                    return True
                else:
                    raise error('Client disconnected')
            except:
                client.close()
                return False

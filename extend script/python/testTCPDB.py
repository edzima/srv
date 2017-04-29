import TCP

server = TCP.ThreadedServer('192.168.22.1',5000,62)
server.listen()

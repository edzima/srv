require("wifiConnect")
require("MPU")

payload = function()
  --local t = tempStatus()

  return "POST /proxy HTTP/1.1\r\n"
    .. "Host: localhost\r\n"
    .. "User-Agent: foo/7.43.0\r\n"
    .. "Accept: */*\r\n"
    .. "Content-Type: application/json\r\n"
    .. "Content-Length: 14\r\n\r\n"
    .. force
    
end

sendData = function()
  force = detectVibration()
  if force then
  sk = net.createConnection(net.TCP, 0)
  

  sk:on("connection", function()
        sk:send(payload)
  end)


  sk:on("sent", function()
    sk:close()
  end)

  sk:connect(conf.server.port, conf.server.ip)
  end
end

function vibrationSendTCP()
    while true do
        --force = detectVibration()
        --if force then
            print("Force: " .. force)
            sendData()
        --end
        tmr.delay(50)
    end
end
conf = {
  server = { port=5000, ip='192.168.22.1' },
}

function tcp()
    tmr.alarm(1, 300, 1, sendData)
end

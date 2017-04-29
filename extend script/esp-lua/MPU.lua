local MPU6050Data = {Ax =0, Ay=0, Az=0, Temperature =0, Gx=0,Gy=0,Gz=0}
dev_addr = 0x68 --104
bus = 0
sda, scl = 4, 3
accelerationFactor = 2/32768
gyroFactor= 500/32768

function init_I2C()
  i2c.setup(bus, sda, scl, i2c.SLOW)
end

function init_MPU(reg,val)  --(107) 0x6B / 0
   write_reg_MPU(reg,val)
end

function write_reg_MPU(reg,val)
  i2c.start(bus)
  i2c.address(bus, dev_addr, i2c.TRANSMITTER)
  i2c.write(bus, reg)
  i2c.write(bus, val)
  i2c.stop(bus)
end



function readData()
  i2c.start(bus)
  i2c.address(bus, dev_addr, i2c.TRANSMITTER)
  i2c.write(bus, 59)
  i2c.stop(bus)
  i2c.start(bus)
  i2c.address(bus, dev_addr, i2c.RECEIVER)
  GData=i2c.read(bus, 14)
  i2c.stop(bus)
  return convertData(GData)
end

function convertData(c)

  AccData = MPU6050Data
  AccData.Ax=bit.lshift(string.byte(c, 1), 8) + string.byte(c, 2)
  AccData.Ay=bit.lshift(string.byte(c, 3), 8) + string.byte(c, 4)
  AccData.Az=bit.lshift(string.byte(c, 5), 8) + string.byte(c, 6)
  AccData.Gx=bit.lshift(string.byte(c, 9), 8) + string.byte(c, 10)
  AccData.Gy=bit.lshift(string.byte(c, 11), 8) + string.byte(c, 12)
  AccData.Gz=bit.lshift(string.byte(c, 13), 8) + string.byte(c, 14)
  AccData.Temperature=bit.lshift(string.byte(c, 7), 8) + string.byte(c, 8)

  if (AccData.Ax > 0x7FFF) then
    AccData.Ax = AccData.Ax - 0x10000;
  end
  if (AccData.Ay > 0x7FFF) then
    AccData.Ay = AccData.Ay - 0x10000;
  end
  if (AccData.Az > 0x7FFF) then
    AccData.Az = AccData.Az - 0x10000;
  end
  if (AccData.Temperature > 0x7FFF) then
    AccData.Temperature = AccData.Temperature - 0x10000;
  end

  AccData.Ax = AccData.Ax * accelerationFactor
  AccData.Ay = AccData.Ay * accelerationFactor
  AccData.Az = AccData.Az * accelerationFactor

  AccData.Temperature = (AccData.Temperature *100 / 340) + 3653 -- /100
  AccData.Temperature = AccData.Temperature /100

  return AccData
end

function detectVibration()
  threshold = 0.2
  shakeFlag = false
  peakForce = 0

  --copy init Array, not reference
  Init = cjson.encode(readData())
  Init = cjson.decode(Init)
  for i=0,20 do
    c = readData()
    currentForce = math.sqrt( (c.Ax-Init.Ax) * (c.Ax - Init.Ax) +
                              (c.Ay-Init.Ay) * (c.Ay - Init.Ay) +
                              (c.Az-Init.Az) * (c.Az-Init.Az))
     if currentForce > peakForce then
       peakForce = currentForce
       end
  end

  --force in range of threshold

  if peakForce > threshold then
    if not shakeFlag then
      shakeFlag = true
      --print("Vibration detect: " .. peakForce)
      return peakForce
    end
  else
    ShakeFlag = false
    return false
  end

end


function read_reg_MPU(reg)
  i2c.start(bus)
  i2c.address(bus, dev_addr, i2c.TRANSMITTER)
  i2c.write(bus, reg)
  i2c.stop(bus)
  i2c.start(bus)
  i2c.address(bus, dev_addr, i2c.RECEIVER)
  c=i2c.read(bus, 1)
  i2c.stop(bus)
  --print(string.byte(c, 1))
  return c
end

function status_MPU(dev_addr)
     i2c.start(bus)
     c=i2c.address(bus, dev_addr ,i2c.TRANSMITTER)
     i2c.stop(bus)
     if c==true then
        print(" Device found at address : "..string.format("0x%X",dev_addr))
     else print("Device not found !!")
     end
end

function check_MPU(dev_addr)
   print("")
   status_MPU(0x68)
   read_reg_MPU(117) --Register 117 – Who Am I - 0x75
   if string.byte(c, 1)==104 then print(" MPU6050 Device answered OK!")
   else print("  Check Device - MPU6050 NOT available!")
        return
   end
   read_reg_MPU(107) --Register 107 – Power Management 1-0x6b
   if string.byte(c, 1)==64 then print(" MPU6050 in SLEEP Mode !")
   else print(" MPU6050 in ACTIVE Mode !")
   end
end



---test program
init_I2C()
check_MPU(0x68)
init_MPU(0x6B,0)

--read_MPU_raw()

--tmr.alarm(1, 1000, 1, function() read_MPU_raw() end)
--tmr.stop(0)
-------------

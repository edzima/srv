-- Scan for I2C devices

id=0
sda = 4
scl = 3
dev_addr = 0x68
-- init i2c

i2c.setup(id, sda,scl,i2c.SLOW)
i2c.start(id)
resCode = i2c.address(id,dev_addr,i2c.TRANSMITTER)
i2c.stop(id)
if resCode == true then 
    print("MPU")
else print ("Not MPU")
end

for i=0,127 do
    i2c.start(id)
    resCode = i2c.address(id,i,i2c.TRANSMITTER)
    i2c.stop(id)
    if resCode == true then print("We have a device on addres 0x" .. string.format("%02x",i) .. "(" .. i ..")") end
    end
    print("finish detect")

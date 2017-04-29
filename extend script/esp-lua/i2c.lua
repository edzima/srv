busid = 0  -- I2C Bus ID. Zawsze zero
sda= 4     -- numer pinu SDA (GPIO2)
scl= 3     -- numer pinu SCL (GPIO0)
addr=0x68  -- adres i2c naszego pcf
 
-- Init i2c
i2c.setup(busid,sda,scl,i2c.SLOW)

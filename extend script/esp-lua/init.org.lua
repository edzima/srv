--main.lua

--local scriptTest = require("MPU")
--scriptTest.testFunction()
require("MPU")


tmr.alarm(1, 500, 1, function() read_MPU_raw() end)

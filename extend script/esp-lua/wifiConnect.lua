--init wifi connect
cfg = {
    ip = "192.168.22.88",
    netmask = "255.255.255.0",
    gateway = "192.168.22.1"
}

wifi.setmode(wifi.STATION)
wifi.sta.setip(cfg)
wifi.sta.config("SRV", "password")
print(wifi.sta.getip())

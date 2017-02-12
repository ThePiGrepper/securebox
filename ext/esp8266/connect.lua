wifi.setmode(wifi.STATION)
--[[
START OF Functions section
##############################################
]]

--[[
Parse AP data and turn on LED if free WiFi found
]]
function fetchCarAP(t)
  local gotWifiCar = false
  for k,v in pairs(t) do
    --l = string.format("%-6s",k")
    isWifiCar = string.sub(k,0,6)
    if (isWifiCar=="juan23") then
      gotWifiCar = true
    end
  end
  if (gotWifiCar) then
    --"CanSeeTheCar!!"
	  print("$$remote=1")
    gpio.write(4, gpio.HIGH)
  else
	  print("$$remote=0")
    gpio.write(4, gpio.LOW)
  end
end

--[[
Method that executes every 5 seconds
]]
function repeatFetch()
  wifi.sta.getap(fetchCarAP)
end

--[[
END OF Functions section
##############################################
]]


tmr.alarm(0, 5000, 1, repeatFetch)
wifi.sta.getap(fetchCarAP)

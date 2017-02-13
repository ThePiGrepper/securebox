cfg =
{
    ip="192.168.4.1",
    netmask="255.255.255.0",
    gateway="192.168.4.1"
}

wifi.setmode(wifi.STATIONAP)
wifi.ap.config({ssid="origin",pwd="12345678"})

wifi.ap.setip(cfg)
--print(wifi.ap.getip()) -- Dynamic IP Address
led1 = 3
led2 = 4
--gpio.mode(led1, gpio.OUTPUT)
gpio.mode(led2, gpio.OUTPUT)

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

srv=net.createServer(net.TCP)
srv:listen(80,function(conn)
    conn:on("receive", function(client,request)
        --print("====")
        --print(request)
        local buf = "";
        local a, b, args = string.find(request, "foo=bar&(.*)");
        if (args ~= nil)then
          args = "$$"..args;
          print(args)
        end
        if(method == nil)then
            _, _, method, path = string.find(request, "([A-Z]+) (.+) HTTP");
        end
        local _GET = {}
        if (vars ~= nil)then
            for k, v in string.gmatch(vars, "(%w+)=(%w+)&*") do
                _GET[k] = v
            end
        end
        buf = buf.."<h1>Secure Box Authentication</h1>";
        --buf = buf.."<p>GPIO0 <a href=\"?pin=ON1\"><button>ON</button></a>&nbsp;<a href=\"?pin=OFF1\"><button>OFF</button></a></p>";
        --buf = buf.."<p>GPIO2 <a href=\"?pin=ON2\"><button>ON</button></a>&nbsp;<a href=\"?pin=OFF2\"><button>OFF</button></a></p>";
        buf = buf.."<p><form action=\"\" method=\"post\">";
        buf = buf.."<table><tr>"
        buf = buf.."<td><button name=\"foo\" value=\"bar\">Open</button></td>";
        buf = buf.."</tr><tr>"
        buf = buf.."<td>Password:</td><td><input type=\"password\" name=\"pass\"></td>"
        buf = buf.."</tr></table>"
        buf = buf.."</form></p>"
        local _on,_off = "",""
        --if(_GET.pin == "ON1")then
        --      gpio.write(led2, gpio.HIGH);
        --elseif(_GET.pin == "OFF1")then
        --      gpio.write(led2, gpio.LOW);
        --elseif(_GET.pin == "ON2")then
        --      gpio.write(led2, gpio.HIGH);
        --elseif(_GET.pin == "OFF2")then
        --      gpio.write(led2, gpio.LOW);
        --end
        client:send(buf);
        client:close();
        collectgarbage();
    end)
end)

tmr.alarm(0, 5000, 1, repeatFetch)
wifi.sta.getap(fetchCarAP)

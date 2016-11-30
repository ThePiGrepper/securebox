cfg =
{
    ip="192.168.1.1",
    netmask="255.255.255.0",
    gateway="192.168.1.1"
}

wifi.setmode(wifi.SOFTAP)
wifi.ap.config({ssid="origin",pwd="12345678"})

wifi.ap.setip(cfg)
--print(wifi.ap.getip()) -- Dynamic IP Address
led1 = 3
led2 = 4
--gpio.mode(led1, gpio.OUTPUT)
gpio.mode(led2, gpio.OUTPUT)
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
        buf = buf.."<h1>Secure Box Setup</h1>";
        --buf = buf.."<p>GPIO0 <a href=\"?pin=ON1\"><button>ON</button></a>&nbsp;<a href=\"?pin=OFF1\"><button>OFF</button></a></p>";
        --buf = buf.."<p>GPIO2 <a href=\"?pin=ON2\"><button>ON</button></a>&nbsp;<a href=\"?pin=OFF2\"><button>OFF</button></a></p>";
        buf = buf.."<p><form action=\"\" method=\"post\">";
        buf = buf.."<table><tr>"
        buf = buf.."<td><button name=\"foo\" value=\"bar\">Setup</button></td>";
        buf = buf.."</tr><tr>"
        buf = buf.."<td>UID:</td><td><input type=\"text\" name=\"uid\"></td>"
        buf = buf.."</tr><tr>"
        buf = buf.."<td>CoordX:</td><td><input type=\"text\" name=\"coordx\"></td>"
        buf = buf.."</tr><tr>"
        buf = buf.."<td>CoordY:</td><td><input type=\"text\" name=\"coordy\"></td>"
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

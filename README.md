# Manage Unifi AP radio schedules

I want to turn off my wifi at night to eliminate unnecessary RF transmission.  This isn't supported by Unifi out of the box, so this repo implements the excellent but slightly cumbersome approach in [this Unifi community post](https://community.ui.com/questions/DISABLE-ENABLE-your-AP-with-time-scheduler-Turn-off-radio-transmitter/3c32439b-a731-4de9-9130-d82d68f409c0) to turn devices on and off via the API from a cron task.

In the end, this was easier to do via the fairly mature [UniFi-API-Client](https://github.com/Art-of-WiFi/UniFi-API-client) project.  I spent a lot of time googling to remember how to get stuff done in PHP.

## Stuff I Learned
* I had to create a new local admin user to use the API.  I'll need to play with the permissions later to figure out what the minimum privilege is.  You can do this from the UI web config under `Admins`.

* The API is different for UDMP than other controllers.  [The difference is described in detail here](https://ubntwiki.com/products/software/unifi-controller/api).  Specifically:

    > NOTE: There are two critical differences between Unifi controllers and the UDM Pro's API:
    > 
    > The login endpoint is /api/auth/login
    > All API endpoints need to be prefixed with /proxy/network (e.g. https://192.168.0.1/proxy/network/api/s/default/self)

* Looking at prior art in  and https://github.com/robinsmidsrod/unifi-controller-cli were really helpful to figuring out how this works.

* For `Unifi-API-Client`, I found that I was able to get away with leaving `$controllerversion` unset for my use case.


## Raw

### Magic curl commands that do stuff so I don't forget it

Login
```
➜ curl -X POST --data '{"username": "$UNIFI_USER", "password": "$UNIFI_PASS"}' --header 'Content-Type: application/json' -b cookie.txt https://192.168.1.1:443/api/auth/login --insecure --cookie-jar cookie.txt
```

Get Device Details
```
➜ curl --cookie cookie.txt https://192.168.1.1:443/proxy/network/api/s/default/stat/device/68:d7:9a:76:6c:b3 --insecure | jq
```
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


Disable AP
```
#!/bin/bash

#Put here all of the AP IDs
# DeviceID="${DeviceID} xxxxxxxxxxxxxxxxxxxx" DeviceID="${DeviceID} zzzzzzzzzzzzzzzzzzzz"
#user name of admin account on controller username=user
#password of admin account on controller password=pass
#controller IP address and port baseurl=https://192.168.1.2:8443
#leave as default site=default cookie=/tmp/cookie

curl_cmd="curl -v --silent --cookie ${cookie} --cookie-jar ${cookie} --insecure"
${curl_cmd} --data "{\"username\":\"$username\", \"password\":\"$password\"}" $baseurl/api/login
for device in ${DeviceID}; do
    ${curl_cmd} -X PUT -H "Content-Type: application/json" --data "json={\"disabled\":true}" $baseurl/api/s/$site/rest/device/${device}
done
${curl_cmd} $baseurl/logout
```

Discover APs
```
#!/bin/bash

#Your Access Point MAC address below MacAddress="${MacAddress} xx:xx:xx:xx:xx:xx"
#user name of admin account on controller username=user
#password of admin account on controller password=pass
#leave as default


# Pass in UNIFI_USER as an env varible
# Pass in UNIFI_PASSWORD as an env variable
# Pass in mac address on the command line


UNIFI_USER=$UNIFI_USERNAME
UNIFI_PASS=$UNIFI_PASSWORD
UNIFI_BASE_URL="https://192.168.1.1:443"
UNIFI_SITE="default"

UNIFI_MAC="68:d7:9a:76:6c:b3"


NOW=`date`
COOKIE=`md5 -q -s "$DATE"`


# Login to the controller
CURL_CMD="curl -X POST --verbose --cookie /tmp/unifi-cookie-$COOKIE --cookie-jar /tmp/unifi-cookie-$COOKIE --insecure "
${CURL_CMD} --data "{\"username\":\"$UNIFI_USER\", \"password\":\"$UNIFI_PASS\"}" $UNIFI_BASE_URL/api/auth/login


exit

# Discover APs
for mac in ${UNIFI_MAC}; do
    echo
    echo "************************* Processing MAC $mac"
    echo

    ${CURL_CMD} $UNIFI_BASE_URL/api/s/$UNIFI_SITE/stat/device/${mac}
done

# Close the controller session
${CURL_CMD} $UNIFI_BASE_URL/logout
```

Enable AP
```
#!/bin/bash
#Put here all of the AP IDs DeviceID="${DeviceID} xxxxxxxxxxxxxxxxxxxxxxx"DeviceID="${DeviceID} zzzzzzzzzzzzzzzzzzzzzz"
#user name of admin account on controller username=user
#password of admin account on controller password=pass
#controller IP address and port baseurl=https://192.168.1.2:8443
#leave as default site=default cookie=/tmp/cookie

curl_cmd="curl -v --silent --cookie ${cookie} --cookie-jar ${cookie} --insecure"
${curl_cmd} --data "{\"username\":\"$username\", \"password\":\"$password\"}" $baseurl/api/login
for device in ${DeviceID}; do
${curl_cmd} -X PUT -H "Content-Type: application/json" --data "json={\"disabled\":false}" $baseurl/api/s/$site/rest/device/${device}

done
${curl_cmd} $baseurl/logout

```
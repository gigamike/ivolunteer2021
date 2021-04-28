#!/bin/bash

# https://dev.maxmind.com/geoip/geoip2/geolite2/
# They are updated on every 3rd of the month

# 0 1 3 * * cd /var/www/kirby/public_html/geoip;./update.sh;
# chmod a+x update.sh

echo "Delete GeoLite2-City.tar.gz"
unlink /var/www/kirby/public_html/geoip/GeoLite2-City.tar.gz;
echo "Delete GeoLite2-ASN.tar.gz"
unlink /var/www/kirby/public_html/geoip/GeoLite2-City.mmdb;

echo "Downloading GeoLite2-City.tar.gz";
wget http://geolite.maxmind.com/download/geoip/database/GeoLite2-City.tar.gz;

echo "Uncompress GeoLite2-City.tar.gz"
tar xvzf /var/www/kirby/public_html/geoip/GeoLite2-City.tar.gz;

lastMonth=$(date -d "last month" '+%Y%m%d');
echo "$lastMonth";

echo "move GeoLite2-City.mmdb"
mv "/var/www/kirby/public_html/geoip/GeoLite2-City_$lastMonth/GeoLite2-City.mmdb" /var/www/kirby/public_html/geoip/

echo "Delete GeoLiteCity.dat"
unlink GeoLite2-City.tar.gz;

echo "Delete GeoIPASNum.dat"
rm -r -f "/var/www/kirby/public_html/geoip/GeoLite2-City_$lastMonth";

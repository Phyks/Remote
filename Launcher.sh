#!/bin/bash

# @nom : Launcher.sh
# @auteurs : Phyks (webmaster@phyks.me) and CCC (contact@cphyc.me)
# @description : Script to launch Remote Control
# See humans.txt file for more info

# Copyright (c) 2013 Phyks and CCC
# This software is licensed under the zlib/libpng License.

#Mute sound
echo "Mute sound"
amixer set Master mute > /dev/null

#Allow apache to access to display
echo "Allowing Apache to connect to the display..."
xhost + 

#Launch apache
if ! systemctl status httpd > /dev/null; then
	echo "Starting Apache... (may need your password)"
	sudo systemctl start httpd
	sleep 1
fi

echo "You can now connect to the following address using your browser : "
if [ -z "$1" ] 
then
	if ifconfig wlan0 | grep "inet " > /dev/null; then
		echo http://$(ifconfig wlan0 | grep 'inet ' | cut -d: -f2 | awk '{print $2}')/Remote/
	else
		echo http://$(ifconfig eth0 | grep 'inet ' | cut -d: -f2 | awk '{print $2}')/Remote/
	fi
else
	echo "(Using interface $1)"
	if ifconfig $1 | grep "inet " > /dev/null; then
		echo http://$(ifconfig $1 | grep 'inet ' | cut -d: -f2 | awk '{print $2}')/Remote/
	else
		echo "The selected interface is not available. FATAL ERROR."
		exit 1
	fi
fi

#Pause
read -p "Press [Enter] key to quit..."
echo ""

echo "Now exiting..."
#Delete the tmp image
if test -s "tmp/tmp.png"; then
	echo "Deleting temp files..."
	rm -f tmp/tmp.png
fi

#Restore initial xhost configuration
echo "Restoring initial configuration"
xhost - 

#Unmute sound
echo "Unmute sound"
amixer set Master unmute > /dev/null

exit 0

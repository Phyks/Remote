#!/bin/bash

# @nom : Remote.sh
# @auteurs : Phyks (webmaster@phyks.me) and CCC (contact@cphyc.me)
# @description : Script to handle left and right move
# See humans.txt file for more info

# Copyright (c) 2013 Phyks and CCC
# This software is licensed under the zlib/libpng License.

export DISPLAY=:0
WID=$2
/usr/bin/xdotool windowfocus $WID
/usr/bin/xdotool key $1

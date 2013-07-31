Remote
======

Remote allows you to remotely control any presentation (such as pdf files, diaporamas, impress.js presentations or any kind of slide based diaporama as long as it can be controlled by the arrows keys) with any device with a browser. It provides you an image of your screen, displayed on your (pocket) device and you can go through the slides and even enter some basic advanced commands.

This app has been written by Phyks (phyks@phyks.me) and cphyc (contact@cphyc.me).


## Pre-requisites

To run this app, you just need

- A webserver (Apache is used by default, but you can change it in Launcher.sh)
- PHP
- A Linux system because Windows turns easy things into bullshit and brainfuck :p


## Usage

Connect your main device and the device to use Remote with to the same network (wired or wireless). Then, just launch Launcher.sh on your main computer (the one connected to the videoprojector) and follow the guide :) 

It will configure everything properly and clean it after use. It will display the address to type on your other device (using by default wlan0 interface or eth0 if wlan0 is not available for the connection with the other device). You can force which interface you want to use by launching "Launcher.sh interface".

You can type a custom command in the specified field. For example, to simulate a specific key press, just type : `./remote.sh key $window` where `remote.sh` is the path to the remote.sh script (that handles key press simulation), `key` is the key you want to simulate and `$window` is a Remote-specific variable to act on the current window (just write `$window` to act on the current window).

You can use some variables and parameters :

* `$window` will be replaced by the window currently selected
* `--verbose` will output the output of the command (always finish the command with --verbose)

### Note
This version mutes sound when launched to avoid extra sounds played by Gnome when you use the arrows to navigate through your presentation. Just comment (#) the corresponding lines in Launcher.sh to avoid this behavior.


## Troobleshooting

* You might see this error message :
		"An error occured. Screenshot is not available.
		Have you opened the presentation viewer ?"

If this happens and you **shouldn't** see it, then try to chmod the tmp folder within the Remote folder (Apache can't write inside by default). A "chmod 777 tmp" should work fine but you can also set it a little bit more fine-grained (the only restriction is that Apache **must** be able to write in this folder).

* If your browser can't find the address, maybe you renamed the "Remote" folder. Then, you can change manually the address opened in `Launcher.sh` to match your configuration.

## License

Please, send us an email (phyks@phyks.me and contact@cphyc.me) if you use or modify this program, just to let us know if this program is useful to anybody or how did you improve it :) You can also send us an email to tell us how lame it is ! :)

### TLDR; 
We don't give a damn to anything you can do using this code. It would just be nice to
quote where the original code comes from.


--------------------------------------------------------------------------------
"THE BEER-WARE LICENSE" (Revision 42) :

    Phyks (phyks@phyks.me) and cphyc (contact@cphyc.me) wrote this file. As long as you 
    retain this notice you can do whatever you want with this stuff (and you can also do 
    whatever you want with this stuff without retaining it, but that's not cool...). If 
    we meet some day, and you think this stuff is worth it, you can buy us a beer in return.
                                                                    
                                                                    	Phyks and cphyc
---------------------------------------------------------------------------------

## Final note
There're some advanced features that are not well documented. This README goes through the basic usage of Remote. Check the code to see what you can do with the advanced command field and each part of the script. You should be able to quite control the whole computer (and not only the slideshow) with a little experience (but I agree this isn't the <del>best</del> fastest way to do it).

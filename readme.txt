Cacti WMI interface script for Linux.
Ross Fawcett (claymen@parkingdenied.com), 2008-2009.

ABOUT

This script works as an interface between the Linux WMI client and Cacti to allow
information to be graphed from Windows based machines without the need for an agent
or SNMP bridge to be installed.

It was written to be as generic as possible such that the script should not need to
be altered by the end user. This means that to monitor a new WMI class you simply
need to create a new template and ensure the correct password file is used.

REQUIREMENTS

Cacti 0.8.7b (Older and newer versions require CDEF tweaks)
PHP 5 or higher (May work with PHP4 but is untested)
WMI Client 1.1.3+ (Note the newer the better, 1.1.3 breaks on some classes)
Remote RPC privelages (Your credential must be allowed to remotely run WMI queries)
Rempte RPC firewall access (And you of course need your firewall to allow RPC)

INSTALLATION

1. Copy wmi.php to your Cacti scripts directory.
2. Create /etc/cacti and /var/log/cacti/wmi.
3. Set owner to cacti and chmod 600/700 to the directories specified above.
4. Configure wmi.php and set the path to your wmic binary.
5. Create your password file in /etc/cacti as per the format below.
6. Import the templates into Cacti.

QUICK INSTALLATION

Run this.
cp wmi.php /var/www/cacti/scripts
mkdir -p /etc/cacti
mkdir -p /var/log/cacti/wmi
chown cacti:cacti /etc/cacti -R
chown cacti:cacti /var/log/cacti/wmi -R
chmod 700 /etc/cacti -R
chmod 700 /var/log/cacti/wmi -R

Then import the templates into Cacti and create your auth file.


AUTH FILE FORMAT

The auth file contains the username and password of the service account you are using
to run the WMI queries. Simply create the file as per the format below and set the
permissions on it so that no one else but the cacti user can read it.

username=<your username>
password=<your password>
domain=<your domain>

FURTHER INFORMATION

Bug tracker
http://mantis.parkingdenied.com

Forum thread
http://forums.cacti.net/viewtopic.php?p=159410

Subversion repository
http://svn.parkingdenied.com/CactiWMI

CREDITS

This project was inspired by the code and ideas that James Dastrup implemented
in his Zenoss wmi_stats.pl script. Without this I would not have have written
my original implementation based on the same idea which then evolved into the
script you have today. Many thanks James.

# CactiWMI

Cacti WMI interface script for Linux.
Ross Fawcett (claymen@parkingdenied.com), 2008-2009.

## About

This script works as an interface between the Linux WMI client and Cacti to allow information to be graphed from Windows based machines without the need for an agent or SNMP bridge to be installed.

It was written to be as generic as possible such that the script should not need to be altered by the end user. This means that to monitor a new WMI class you simply need to create a new template and ensure the correct password file is used.

## Requirements

- Cacti 0.8.7c+ (Older versions will require CDEF tweaks)
- PHP 5 or higher (May work with PHP4 but is untested)
- WMI Client 1.1.3+ (1.1.3 and older have problems with some classes. Latest confirmed working version 1.3.5)
- Remote RPC privileges. Your credential must be allowed to remotely run WMI queries.
  - Performance Log Users
  - Performance Monitor Users
- Remote RPC firewall access, and you of course need your firewall to allow RPC.

## Installation

### Setup cacti

1. Copy wmi.php to your Cacti scripts directory.

        $ cp wmi.php /var/www/cacti/scripts

2. Create directories

        $ mkdir -p /etc/cacti
        $ mkdir -p /var/log/cacti/wmi

3. Ensure cacti owns directories and permissions are secure.

        $ chown cacti:cacti /etc/cacti -R
        $ chown cacti:cacti /var/log/cacti/wmi -R
        $ chmod 700 /etc/cacti -R
        $ chmod 700 /var/log/cacti/wmi -R

4. (optional) Configure wmi.php and set the path to your wmic binary.
5. Create your password file in /etc/cacti as per the format below.
6. Import the templates into Cacti.

### Building wmic
Most distributions do not include the wmi client any longer so you will need to manually build it.

1. Grab a copy of the latest WMI client for linux

        $ svn export http://dev.zenoss.org/svn/tags/wmi-1.3.16/

2. Build wmic

        $ cd wmi-*/Samba/source
        $ ./autogen.sh
        $ make proto bin/wmic

3. Copy the wmic binary to /usr/local/bin/ or somewhere else on in your path.

### Auth File Format

The auth file contains the username and password of the service account you are using to run the WMI queries. Simply create the file as per the format below and set the permissions on it so that no one else but the cacti user can read it.

```
username=<your username>
password=<your password>
domain=<your domain>
```

## Further Information

Github URL https://github.com/neclimdul/CactiWMI

Forum thread
http://forums.cacti.net/viewtopic.php?p=159410

### Older links
Bug tracker
http://mantis.parkingdenied.com

Subversion repository
http://svn.parkingdenied.com/CactiWMI

## Credits

This project was inspired by the code and ideas that James Dastrup implemented in his Zenoss wmi_stats.pl script. Without this I would not have have written my original implementation based on the same idea which then evolved into the script you have today. Many thanks James.

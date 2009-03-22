Cacti WMI interface script for Linux.
Ross Fawcett (claymen@parkingdenied.com), 2008-2009

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

To be written.

FURTHER INFORMATION

Bug tracker
http://mantis.parkingdenied.com

Forum thread
http://forums.cacti.net/viewtopic.php?p=159410

Subversion repository
http://svn.parkingdenied.com/CactiWMI
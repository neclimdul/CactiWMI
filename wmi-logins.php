<?php
/*
 +-------------------------------------------------------------------------+
 | Copyright (C) 2008 Ross Fawcett                                         |
 |                                                                         |
 | This program is free software; you can redistribute it and/or           |
 | modify it under the terms of the GNU General Public License             |
 | as published by the Free Software Foundation; either version 3          |
 | of the License, or (at your option) any later version.                  |
 |                                                                         |
 | This program is distributed in the hope that it will be useful,         |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
 | GNU General Public License for more details.                            |
 +-------------------------------------------------------------------------+
*/


/*

This file defines the login/password pairs and the reference used by wmi.php
to access them. This is done to prevent the credentials being logged in the
Cacti log files and allow some separation.

To add another login credential add it below like follows.

$logins['reference'] = array('Domain/Username','Password');

Reference is the single word name for the username/password pair. And the two
fields in the array are the actual username (with domain if required) and the
password for that user.

One last note, by default the templates don't offer a different username per
host, you simply need to change this in the template.

*/


$logins = array();
$logins['credential'] = array('Domain/Username','Password');

?>

#!/bin/sh

# Replace your rrd file name below so that it picks the correct files.
# Duplicate the rrdtool tune options to suit your data source name you wish to strip out spikes.
# Default value should suit most situations but feel free to adjust it further.

for file in `ls | grep your_rrd_name_here`;
do
	echo Fixing $file
	echo Backing up $file
	mv $file ./old/
	echo Tuning $file
	# EDIT START
	rrdtool tune ./old/$file --maximum ClosedSessions:500000
	rrdtool tune ./old/$file --maximum OpenedSessions:500000
	# EDIT END
	echo Dumping $file
	rrdtool dump ./old/$file > hax.temp
	echo Restoring $file
	rrdtool restore -r hax.temp $file
	echo Cleaning up temp files
	rm hax.temp
	echo Fixing permissions
	chown cacti:cacti $file
	echo Done!
	echo -e '\n\n\n'
done;


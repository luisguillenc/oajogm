#!/bin/bash

LOG_FILE=/var/log/firewall-oajog.log

if [ ! -f $LOG_FILE ]; then
	echo "No se encuentra $LOG_FILE" 1>&2
	exit 1
fi

if [ $# -eq 1 ]; then
	nlineas=$1
	tail -n${nlineas} $LOG_FILE
else
	cat $LOG_FILE
fi



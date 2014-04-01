#!/bin/bash

if [ $# -lt 1 ]; then
	echo "Faltan parámetros" 1>&2
	exit 2
fi


__ScriptPath=$(readlink -f $0)
if [ -f $(dirname $__ScriptPath)/../includes/core.inc.sh ]; then
	__LgBashPath=`dirname $(dirname $__ScriptPath)`
else
	if [ -d /usr/local/lgbashlib ]; then
		__LgBashPath=/usr/local/lgbashlib
	else
		echo "No se encuentra lgbashlib!" 2>&1
		exit 1
	fi
fi

__ScriptVar=`dirname $(dirname $__ScriptPath)`/var
export cfg_logFile=$__ScriptVar/log/ovpnmgmt.log

cfg_forceRoot=0
. $__LgBashPath/includes/bootstrap.sh

__loadConfig $__ScriptEtc/oajog.cfg
__loadModule services

__loadScriptModule oajog


action="$1"

case "$action" in
	"start") __startService openvpn
			exitstatus=$?
			;;

	"stop") __stopService openvpn
			exitstatus=$?
			;;

	"status") __checkService openvpn
			exitstatus=$?
			;;
	*)
			__die "No existe la acción"
esac


exit $exitstatus


#!/bin/bash

if [ $# -lt 1 ]; then
	echo "Faltan parámetros" 1>&2
	exit 2
fi

if [ $# -eq 1 ] && [ "$1" == "disconnect" ]; then
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

. $__LgBashPath/includes/bootstrap.sh

__loadConfig $__ScriptEtc/oajog.cfg
__dieNotDef cfg_ovpnMgmtPort

__loadScriptModule oajog


action="$1"
cn_client="$2"

case "$action" in
	"list") echo -e "status\nquit" | nc -i1 localhost $cfg_ovpnMgmtPort
			exitstatus=$?
			;;

	"disconnect") echo -e "kill ${cn_client}\nquit" | nc -i1 localhost $cfg_ovpnMgmtPort
			exitstatus=$?
			;;
	*)
			__die "No existe la acción"
esac


exit $exitstatus


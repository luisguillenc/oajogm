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
export cfg_logFile=$__ScriptVar/log/iptablescfg.log

cfg_forceRoot=0
. $__LgBashPath/includes/bootstrap.sh

[ -f $__ScriptLib/iptables_failsafe.sh ] \
	|| __die "No se encuentra script de rescate iptables"

__loadConfig $__ScriptEtc/oajog.cfg
__dieNotDef cfg_oajogServiceUri

__loadScriptModule oajog

action="$1"
case "$action" in
	"reload") 
			## construyo url
			script_uri="$cfg_oajogServiceUri"`getIptablesscriptUri`
			if __defined cfg_oajogServiceSharedKey; then
				script_uri="$script_uri"`getAuthenticationParamsUri $cfg_oajogServiceSharedKey`
			fi

			## obtengo la url
			__msg "Descargando script iptables"
			curl -f $CURL_OPTS -o $__ScriptLib/iptables_last.sh "$script_uri"
			if [ $? -ne 0 ]; then
				__error "Error obteniendo script iptables, aplicando failsafe"
				sh $__ScriptLib/iptables_failsafe.sh \
					|| __die "Error cargando iptables failsafe"
				exit 1
			fi

			sh $__ScriptLib/iptables_last.sh
			if [ $? -ne 0 ]; then
				__error "Error ejecutando script iptables, aplicando failsafe"
				sh $__ScriptLib/iptables_failsafe.sh \
					|| __die "Error cargando iptables failsafe"
				exit 1
			fi

			;;

	"failsafe") 
			__msg "Cargando iptables failsafe"
				sh $__ScriptLib/iptables_failsafe.sh \
					|| __die "Error cargando iptables failsafe"
				
			;;
	*)
			__die "No existe la acción"
esac



__msg "Script iptables cargado correctamente"


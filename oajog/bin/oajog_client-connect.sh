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
export cfg_logFile=$__ScriptVar/log/client-connect.log

. $__LgBashPath/includes/bootstrap.sh

__loadConfig $__ScriptEtc/oajog.cfg
__dieNotDef cfg_oajogServiceUri

__loadScriptModule oajog


config_output_file=$1

## obtengo el cn de la variable de entorno
cn_client="$common_name"
if [ "$cn_client" == "" ]; then
	__die "Error obteniendo CN del entorno"
fi

## construyo url
check_uri="$cfg_oajogServiceUri"`getClientconfigUri $cn_client`
if __defined cfg_oajogServiceSharedKey; then
	check_uri="$check_uri"`getAuthenticationParamsUri $cfg_oajogServiceSharedKey`
fi

## obtengo configuración
__msg "Obteniendo configuración de $cn_client"
curl -f $CURL_OPTS -o $config_output_file "$check_uri" \
	|| __die "Error obteniendo configuración"

__msg "Configuración obtenida correctamente"


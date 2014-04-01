#!/bin/bash

if [ $# -lt 2 ]; then
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
export cfg_logFile=$__ScriptVar/log/tls-verify.log

. $__LgBashPath/includes/bootstrap.sh

__loadConfig $__ScriptEtc/oajog.cfg
__dieNotDef cfg_oajogServiceUri

__loadScriptModule oajog


cert_chain=$1
cert_string="$2"

## compruebo profundidad de certificado
if [ $cert_chain -gt 0 ]; then
	__dbg "Pasando $cert_chain $cert_string"
	exit 0
fi

## obtengo el cn del certificado
cn_client=`getCnFromCertString $cert_string`
if [ $? -ne 0 ] || [ "$cn_client" == "" ]; then
	__die "Error obteniendo CN de $cert_string"
fi

## construyo url
check_uri="$cfg_oajogServiceUri"`getCheckaccessUri $cn_client`
if __defined cfg_oajogServiceSharedKey; then
	check_uri="$check_uri"`getAuthenticationParamsUri $cfg_oajogServiceSharedKey`
fi

## chequeo
__msg "Chequeando usuario $cn_client"
curl -f $CURL_OPTS "$check_uri"
if [ $? -eq 0 ]; then
	__msg "Acceso permitido"
	exit 0
else
	__warn "Acceso denegado"
	exit 1
fi


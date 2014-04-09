#!/bin/bash

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

__loadConfig $__ScriptEtc/oajog.cfg
__dieNotDef cfg_oajogUriCrl

curl -f $CURL_OPTS -o $__Tmp/crl_latest.pem $cfg_oajogUriCrl
if [ $? -ne 0 ]; then
	__die "Error obteniendo certificado"
fi

mkdir -p /etc/openvpn/crl
chmod 755 /etc/openvpn/crl

[ -f $__Tmp/crl_latest.pem ] || __die "Error el crl está vacío"
cp $__Tmp/crl_latest.pem /etc/openvpn/crl/latest.pem
chmod 644 /etc/openvpn/crl/latest.pem


__msg "Crl obtenida correctamente"


#!/bin/bash


REQ_TOOLS="pwgen curl md5sum nc"
for tool in $REQ_TOOLS;
do
	which $tool > /dev/null
	if [ $? -ne 0 ]; then
		echo "No se encuentra $tool" 1>&2
		exit 2
	fi
done

# recibe una cadena tipo:
# "/C=ES/ST=Teruel/L=Teruel/O=CEFCA_Foundation/OU=oajog/CN=cliente1/emailAddress=lguillen@cefca.es"
# retorna cliente1
function getCnFromCertString() {
    __dbg "${FUNCNAME}($@)"
    [ $# -eq 1 ] || __die "${FUNCNAME}(): invalid number of parameters"

	local cert_string=$1
	local value
	local cn_client=""

	OLDIFS=$IFS
	IFS="/"
	for value in $cert_string;
	do
		echo $value | grep -q "CN="
		if [ $? -eq 0 ]; then
			cn_client=`echo $value | cut -d"=" -f2`
		fi

	done
	IFS=$OLDIFS

	echo $cn_client
}


# genera los parámetros necesarios de autenticación a partir de la shared key
function getAuthenticationParamsUri() {
    __dbg "${FUNCNAME}($@)"
    [ $# -eq 1 ] || __die "${FUNCNAME}(): invalid number of parameters"

	local shared_key="$1"
	local salt_passwd=`pwgen -1`
	local hash_passwd=`echo -n "${salt_passwd}${shared_key}" | md5sum | cut -d" " -f1`

	echo "/salt/${salt_passwd}/hash/${hash_passwd}"
}

function getCheckaccessUri() {
    __dbg "${FUNCNAME}($@)"
    [ $# -eq 1 ] || __die "${FUNCNAME}(): invalid number of parameters"

	local client_name="$1"

	echo "/index/checkaccess/name/${client_name}"
}


function getClientconfigUri() {
    __dbg "${FUNCNAME}($@)"
    [ $# -eq 1 ] || __die "${FUNCNAME}(): invalid number of parameters"

	local client_name="$1"

	echo "/index/getclientconfig/name/${client_name}"
}

function getNotifydisconnectUri() {
    __dbg "${FUNCNAME}($@)"
    [ $# -eq 1 ] || __die "${FUNCNAME}(): invalid number of parameters"

	local client_name="$1"

	echo "/index/notifydisconnect/name/${client_name}"
}

function getIptablesscriptUri() {
    __dbg "${FUNCNAME}($@)"

	echo "/index/getiptablesscript"
}


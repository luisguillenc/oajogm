#!/bin/bash


IPT=/sbin/iptables

### Tunning del kernel
# habilito forwarding de paquetes
echo 1 > /proc/sys/net/ipv4/ip_forward

# protección anti spoofing del kernel
echo 1 > /proc/sys/net/ipv4/conf/all/rp_filter


### Limpieza de reglas
# limpio todas las reglas
$IPT -F
$IPT -t nat -F
$IPT -t mangle -F

# eliminto las cadenas definidas por el usuario
$IPT -X
$IPT -t nat -X
$IPT -t mangle -X

# pongo a cero contadores
$IPT -Z

### Políticas globales
$IPT -P INPUT DROP
$IPT -P FORWARD DROP
$IPT -P OUTPUT ACCEPT


### Filtrado de entrada
# acepto todo el tráfico de loopback
$IPT -A INPUT -i lo -j ACCEPT

# acepto tráfico ICMP
$IPT -A INPUT -p icmp -j ACCEPT

# acepto tráfico ssh
$IPT -A INPUT -p tcp --dport 22 -j ACCEPT

# acepto conexiones entrantes relacionadas
$IPT -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT



<?php

class Core_Bootstrap extends Zend_Application_Module_Bootstrap {

    public function _initServiceManager() {

        // inicializo variables
        $pathsCfg = Zend_Registry::get("pathsCfg");
        $gatewayCfg = Zend_Registry::get("gatewayCfg");
        $operatorCfg = Zend_Registry::get("operatorCfg");
        
        $serviceIp = new Core_Model_IPAddress($gatewayCfg->serviceip);
        $a_vpnNet = explode("/", $gatewayCfg->vpnnet);
        $vpnNet = new Core_Model_NetworkAddress(
                    new Core_Model_IPAddress($a_vpnNet[0]),
                    new Core_Model_IPAddress($a_vpnNet[1])
                );

        $addRoutes = array();
        if($gatewayCfg->get('routeadd')) {
            foreach($gatewayCfg->routeadd as $route) {
                $a_route = explode("/", $route);
                $addRoutes[] = new Core_Model_NetworkAddress(
                        new Core_Model_IPAddress($a_route[0]),
                        new Core_Model_IPAddress($a_route[1])
                        );
            }
        }
        
        $excludeIps = array();
        if($gatewayCfg->get('excludeip')) {
            foreach($gatewayCfg->excludeip as $excludeip) {
                $excludeIps[] = new Core_Model_IPAddress($excludeip);
            }
        }

        if($gatewayCfg->get('gatewaysharedkey')) {
            $gatewaySharedKey = $gatewayCfg->gatewaysharedkey;
        } else {
            $gatewaySharedKey = "";
        }

        if($gatewayCfg->get('isslave')) {
            $disableWrites = $gatewayCfg->isslave;
        } else {
            $disableWrites = false;
        }
        
        Core_Model_IptablesCompiler::setTemplate($pathsCfg->iptablestpl);
        
        Core_Model_GatewayConfiguration::setLockFilePath($pathsCfg->gatewaylock);
        $gwConf = new Core_Model_GatewayConfiguration(
                $serviceIp, $vpnNet, $addRoutes, $gatewaySharedKey
                );
        
        $vpnIpPooler = new Core_Model_VpnIpPoolerDb($vpnNet, $excludeIps);
        $cnLogger = new Core_Model_ConnectionLoggerDb();
        
        $auditLogger = new Core_Model_AuditLoggerDb();
        Core_Model_AuditHelper::setLogger($auditLogger);

        // construyo operador
        Core_Model_GatewayOperatorLocal::setPathCommand($pathsCfg->oajogpath);
        if($operatorCfg->type == "local") {
            $operatorGw = new Core_Model_GatewayOperatorLocal();
        } else {
            $sshOpts = $operatorCfg->ssh->toArray();
            Core_Model_GatewayOperatorSsh::setPathSshKeys($pathsCfg->sshkeys);
            $operatorGw = new Core_Model_GatewayOperatorSsh($sshOpts);
        }
        
        if($disableWrites) {
            Core_Model_NetworkResourceDbRepository::disableWrites();
            Core_Model_AccessProfileDbRepository::disableWrites();
            Core_Model_AccessClientDbRepository::disableWrites();
        }
        
        //inicializamos las clases del model
        $networkResourceRepository = 
                new Core_Model_NetworkResourceDbRepository();
        $networkResourceFactory = 
                new Core_Model_NetworkResourceFactory($networkResourceRepository);

        $accessProfileRepository = 
                new Core_Model_AccessProfileDbRepository($networkResourceRepository);
        $accessProfileFactory =
                new Core_Model_AccessProfileFactory($accessProfileRepository);

        $accessClientRepository =
                new Core_Model_AccessClientDbRepository($accessProfileRepository);
        $accessClientFactory =
                new Core_Model_AccessClientFactory($accessClientRepository);
        
        // inicializamos servicios y registramos
        $networkResourceMgr = 
                new Core_Service_NetworkResourceManager(
                        $networkResourceRepository, 
                        $networkResourceFactory, 
                        $accessProfileRepository
                        );
        
        $accessProfileMgr =
                new Core_Service_AccessProfileManager(
                        $accessProfileRepository, 
                        $accessProfileFactory, 
                        $networkResourceRepository, 
                        $accessClientRepository
                        );
        
        $accessClientMgr =
                new Core_Service_AccessClientManager(
                            $accessClientRepository, 
                            $accessClientFactory,
                            $accessProfileRepository,
                            $vpnIpPooler
                        );
        
        $connectionMgr = 
                new Core_Service_ConnectionManager(
                            $gwConf,
                            $accessClientRepository,
                            $cnLogger
                        );
        
        $gatewayMgr =
                new Core_Service_GatewayManager(
                        $gwConf, 
                        $operatorGw, 
                        $cnLogger
                        );
        
        $sm = LGC_Service_Manager::getInstance();
        $sm->registerService("network_resource_manager", $networkResourceMgr);
        $sm->registerService("access_profile_manager", $accessProfileMgr);
        $sm->registerService("access_client_manager", $accessClientMgr);
        $sm->registerService("connection_manager", $connectionMgr);
        $sm->registerService("gateway_manager", $gatewayMgr);
        
        $sm->registerService("vpn_ip_pooler", $vpnIpPooler);
        $sm->registerService("gw_conf", $gwConf);

    }

}

<?php
class Vps_User_All_Model extends Vps_Model_Proxy
{
    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $clientConfig = array(
                'serverUrl' => Vps_Registry::get('config')->service->usersAll->url,
                'extraParams' => array(
                    'applicationId' => Vps_Registry::get('config')->application->id,
                    'version'       => Vps_User_Model::version()
                )
            );
            $cfg = Vps_Registry::get('config');
            if (!empty($cfg->service->usersAll->proxy->host)) {
                $clientConfig['proxy_host'] = $cfg->service->usersAll->proxy->host;
            }
            if (!empty($cfg->service->usersAll->proxy->port)) {
                $clientConfig['proxy_port'] = $cfg->service->usersAll->proxy->port;
            }
            if (!empty($cfg->service->usersAll->proxy->user)) {
                $clientConfig['proxy_user'] = $cfg->service->usersAll->proxy->user;
            }
            if (!empty($cfg->service->usersAll->proxy->pass)) {
                $clientConfig['proxy_pass'] = $cfg->service->usersAll->proxy->pass;
            }

            $client = new Vps_Srpc_Client($clientConfig);
            $config['proxyModel'] = new Vps_Model_Service(array('client' => $client));
        }
        parent::__construct($config);
    }
}

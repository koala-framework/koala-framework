<?php
final class Vps_User_Mirror extends Vps_Model_MirrorCache
{
    protected $_syncTimeField = 'last_modified';
    protected $_rowClass = 'Vps_User_MirrorRow';

    protected function _init()
    {
        $clientConfig = array(
            'serverUrl' => Vps_Registry::get('config')->service->users->url,
            'extraParams' => array(
                'applicationId' => Vps_Registry::get('config')->application->id,
                'version'       => Vps_User_Model::version()
            )
        );
        $cfg = Vps_Registry::get('config');
        if (!empty($cfg->service->users->proxy->host)) {
            $clientConfig['proxy_host'] = $cfg->service->users->proxy->host;
        }
        if (!empty($cfg->service->users->proxy->port)) {
            $clientConfig['proxy_port'] = $cfg->service->users->proxy->port;
        }
        if (!empty($cfg->service->users->proxy->user)) {
            $clientConfig['proxy_user'] = $cfg->service->users->proxy->user;
        }
        if (!empty($cfg->service->users->proxy->pass)) {
            $clientConfig['proxy_pass'] = $cfg->service->users->proxy->pass;
        }

        $client = new Vps_Srpc_Client($clientConfig);
        $this->_sourceModel = new Vps_Model_Service(array('client' => $client));
        $this->_proxyModel = new Vps_Model_Db(array('table' => 'cache_users'));

        parent::_init();
    }
}

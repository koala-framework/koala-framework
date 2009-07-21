<?php
class Vps_User_All_Model extends Vps_Model_Proxy
{
    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $client = new Vps_Srpc_Client(array(
                'serverUrl' => Vps_Registry::get('config')->service->usersAll->url,
                'extraParams' => array(
                    'applicationId' => Vps_Registry::get('config')->application->id,
                    'version'       => Vps_User_Model::version()
                )
            ));
            $config['proxyModel'] = new Vps_Model_Service(array('client' => $client));
        }
        parent::__construct($config);
    }
}

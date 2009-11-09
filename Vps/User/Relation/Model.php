<?php
class Vps_User_Relation_Model extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_User_Relation_Row';

    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $clientConfig = array(
                'serverUrl' => Vps_Registry::get('config')->service->usersRelation->url,
                'extraParams' => array(
                    'applicationId' => $this->getApplicationId(),
                    'version'       => Vps_User_Model::version()
                )
            );
            $cfg = Vps_Registry::get('config');
            if (!empty($cfg->service->usersRelation->proxy->host)) {
                $clientConfig['proxyHost'] = $cfg->service->usersRelation->proxy->host;
            }
            if (!empty($cfg->service->usersRelation->proxy->port)) {
                $clientConfig['proxyPort'] = $cfg->service->usersRelation->proxy->port;
            }
            if (!empty($cfg->service->usersRelation->proxy->user)) {
                $clientConfig['proxyUser'] = $cfg->service->usersRelation->proxy->user;
            }
            if (!empty($cfg->service->usersRelation->proxy->password)) {
                $clientConfig['proxyPassword'] = $cfg->service->usersRelation->proxy->password;
            }

            $client = new Vps_Srpc_Client($clientConfig);
            $config['proxyModel'] = new Vps_User_Relation_ServiceModel(array('client' => $client));
        }
        parent::__construct($config);
    }

    public function getApplicationId()
    {
        return Vps_Registry::get('config')->application->id;
    }
}

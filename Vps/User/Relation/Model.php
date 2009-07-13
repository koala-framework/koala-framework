<?php
class Vps_User_Relation_Model extends Vps_Model_Proxy
{
    protected $_rowClass = 'Vps_User_Relation_Row';

    public function __construct(array $config = array())
    {
        if (!isset($config['proxyModel'])) {
            $client = new Vps_Srpc_Client(array(
                'serverUrl' => Vps_Registry::get('config')->service->usersRelation->url,
                'extraParams' => array('applicationId' => $this->getApplicationId())
            ));
            $config['proxyModel'] = new Vps_User_Relation_ServiceModel(array('client' => $client));
        }
        parent::__construct($config);
    }

    public function getApplicationId()
    {
        return Vps_Registry::get('config')->application->id;
    }
}

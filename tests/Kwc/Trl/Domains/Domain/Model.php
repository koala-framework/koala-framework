<?php
class Kwc_Trl_Domains_Domain_Model extends Kwc_Root_TrlRoot_Model
{
    protected $_columns = array('id', 'domain', 'filename', 'name', 'master');
    protected $_siblingModels = array();

    public function __construct(array $values = array())
    {
        $config['data'] = array();

        $config['data'][] = array(
            'id' => 'atde',
            'domain' => 'at',
            'filename' => 'de',
            'name' => 'de',
            'master' => true,
            'visible' => true
        );
        $config['data'][] = array(
            'id' => 'aten',
            'domain' => 'at',
            'filename' => 'en',
            'name' => 'en',
            'master' => false,
            'visible' => true
        );
        $config['data'][] = array(
            'id' => 'ates',
            'domain' => 'at',
            'filename' => 'es',
            'name' => 'es',
            'master' => false,
            'visible' => true
        );

        $config['data'][] = array(
            'id' => 'huhu',
            'domain' => 'hu',
            'filename' => 'de',
            'name' => 'de',
            'master' => true,
            'visible' => true
        );
        $config['data'][] = array(
            'id' => 'huen',
            'domain' => 'hu',
            'filename' => 'en',
            'name' => 'en',
            'master' => false,
            'visible' => true
        );

        $config['data'][] = array(
            'id' => 'roro',
            'domain' => 'ro',
            'filename' => 'ro',
            'name' => 'ro',
            'master' => true,
            'visible' => true
        );
        $config['data'][] = array(
            'id' => 'roen',
            'domain' => 'ro',
            'filename' => 'en',
            'name' => 'en',
            'master' => false,
            'visible' => true
        );
        parent::__construct($config);
    }
}

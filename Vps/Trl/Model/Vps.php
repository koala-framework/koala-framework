<?php
class Vps_Trl_Model_Vps extends Vps_Trl_Model_Abstract
{
    public function __construct(array $config = array())
    {
        $modelVps = new Vps_Model_Xml(array(
            'xpath' => '/trl',
            'topNode' => 'text',
            'filepath' => VPS_PATH.'/trl.xml'
        ));

		if (!isset($config['proxyModel'])) $config['proxyModel'] = $modelVps;
		if (!isset($config['cacheSettings'])) {
		    $config['cacheSettings'] = array(
		        array(
					'index' => array('en', 'context'), //ist ok so ..
					'columns' => $this->_getTargetLanguages()
                )
             );
		}
        parent::__construct($config);
    }
}
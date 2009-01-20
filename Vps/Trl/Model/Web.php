<?php
class Vps_Trl_Model_Web extends Vps_Trl_Model_Abstract
{
    public function __construct(array $config = array())
    {
        $modelWeb = new Vps_Model_Xml(array(
            'rootNode' => 'trl',
            'xpath' => '/trl',
            'topNode' => 'text',
            'filepath' => './application/trl.xml'
        ));

		if (!isset($config['proxyModel'])) $config['proxyModel'] = $modelWeb;
		if (!isset($config['cacheSettings'])) {
		    $config['cacheSettings'] = array(
		        array(
					'index' => array($this->_getWebCodeLanguage(), 'context'), //muss noch dynamisch gemacht werden
					'columns' => $this->_getTargetLanguages()
                )
             );
		}
        parent::__construct($config);
    }
}
<?php
class Kwc_Columns_Model extends Kwc_Abstract_List_Model
{
    protected $_rowClass = 'Kwc_Columns_Row';
    protected $_componentClass;

    public function __construct($config = array())
    {
        if (isset($config['componentClass'])) $this->_componentClass = $config['componentClass'];
        parent::__construct($config);
        if (!$this->_componentClass) {
            throw new Kwf_Exception('componentClass is required');
        }
        $this->setFactoryConfig(array(
            'type' => 'Kwc_Columns_ModelFactory',
            'id' => $this->_componentClass.'.childModel',
            'componentClass' => $this->_componentClass
        ));
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}

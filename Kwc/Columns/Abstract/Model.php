<?php
class Kwc_Columns_Abstract_Model extends Kwc_Abstract_List_Model
{
    protected $_rowClass = 'Kwc_Columns_Abstract_Row';
    protected $_componentClass;

    protected $_default = array(
        'type' => '2col-50_50',
        'visible' => 1
    );

    public function __construct($config = array())
    {
        if (isset($config['componentClass'])) $this->_componentClass = $config['componentClass'];
        parent::__construct($config);
        if (!$this->_componentClass) {
            throw new Kwf_Exception('componentClass is required');
        }
        $this->setFactoryConfig(array(
            'type' => 'Kwc_Columns_Abstract_ModelFactory',
            'id' => $this->_componentClass.'.childModel',
            'componentClass' => $this->_componentClass
        ));
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }
}

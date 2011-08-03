<?php
class Vpc_Basic_Text_Model extends Vps_Model_Db_Proxy
{
    protected $_componentClass;
    private $_childComponentsModel;

    protected $_table = 'vpc_basic_text';
    protected $_rowClass = 'Vpc_Basic_Text_Row';
    protected $_dependentModels = array(
        'ChildComponents' => 'Vpc_Basic_Text_ChildComponentsModel'
    );

    public function __construct($config = array())
    {
        if (!isset($config['componentClass'])) {
            throw new Vps_Exception("componentClass is required for text-model");
        }

        if (!isset($this->_default['content'])) {
            $default = Vpc_Abstract::getSetting($config['componentClass'], 'defaultText');
            $config['default']['content'] = "<p>$default</p>";
        }
        $this->_componentClass = $config['componentClass'];
        parent::__construct($config);
    }

    protected function _init()
    {
        $this->_siblingModels = array(
            new Vps_Model_Field(array(
                'fieldName' => 'data'
            ))
        );
        parent::_init();
    }

    public function getComponentClass()
    {
        return $this->_componentClass;
    }

    protected function _createDependentModel($rule)
    {
        if ($rule == 'ChildComponents') {
            if (!isset($this->_childComponentsModel)) {
                $c = $this->_dependentModels[$rule];
                $this->_childComponentsModel = new $c(array(
                    'componentClass' => $this->_componentClass
                ));
            }
            return $this->_childComponentsModel;
        } else {
            return parent::_createDependentModel($rule);
        }
    }
}

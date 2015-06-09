<?php
class Kwc_Basic_Text_Model extends Kwf_Model_Db_Proxy
{
    protected $_componentClass;
    private $_childComponentsModel;

    protected $_table = 'kwc_basic_text';
    protected $_rowClass = 'Kwc_Basic_Text_Row';
    protected $_dependentModels = array(
        'ChildComponents' => 'Kwc_Basic_Text_ChildComponentsModel'
    );

    public function __construct($config = array())
    {
        if (!isset($config['componentClass'])) {
            throw new Kwf_Exception("componentClass is required for text-model");
        }

        if (!isset($this->_default['content'])) {
            $default = Kwc_Abstract::getSetting($config['componentClass'], 'defaultText');
            $default = Kwf_Trl::getInstance()->trlStaticExecute($default);
            $config['default']['content'] = "<p>$default</p>";
        }
        $this->_componentClass = $config['componentClass'];
        parent::__construct($config);
        $this->setFactoryConfig(array(
            'type' => 'Kwc_Basic_Text_ModelFactory',
            'id' => $this->_componentClass.'.ownModel',
            'componentClass' => $this->_componentClass
        ));
    }

    protected function _init()
    {
        $this->_siblingModels = array(
            new Kwf_Model_Field(array(
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

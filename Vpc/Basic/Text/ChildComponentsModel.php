<?php
class Vpc_Basic_Text_ChildComponentsModel extends Vps_Model_Db_Proxy
{
    protected $_componentClass;

    protected $_table = 'vpc_basic_text_components';

    protected $_referenceMap = array(
        'Component' => array(
            'refModelClass' => 'Vpc_Basic_Text_Model',
            'column' => 'component_id'
        )
    );

    public function __construct($config = array())
    {
        if (!isset($config['componentClass'])) {
            throw new Vps_Exception("componentClass is required for text-model");
        }
        $this->_componentClass = $config['componentClass'];
        parent::__construct($config);
    }

    public function getReferencedModel($ref)
    {
        if ($ref == 'Component') {
            return Vpc_Basic_Text_Component::getTextModel($this->_componentClass);
        } else {
            return parent::getReferencedModel($ref);
        }
    }
}

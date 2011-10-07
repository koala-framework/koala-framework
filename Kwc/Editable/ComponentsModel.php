<?php
class Kwc_Editable_ComponentsModel extends Kwf_Model_Data_Abstract
{
    public function __construct(array $config = array())
    {
        $data = array();
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Editable_Component');
        foreach ($components as $c) {
            $data[] = array(
                'id' => $c->dbId,
                'name' => $c->getComponent()->getNameForEdit(),
                'content_component_class' => $c->getChildComponent('-content')->componentClass,
            );
        }
        $this->_data = $data;
        parent::__construct($config);
    }
}

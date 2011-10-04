<?php
class Vpc_Editable_ComponentsModel extends Vps_Model_Data_Abstract
{
    public function __construct(array $config = array())
    {
        $data = array();
        $components = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Editable_Component');
        foreach ($components as $c) {
            $data[] = array(
                'id' => $c->dbId,
                'name' => $c->getComponent()->getName(),
                'content_component_class' => $c->getChildComponent('-content')->componentClass,
            );
        }
        $this->_data = $data;
        parent::__construct($config);
    }
}

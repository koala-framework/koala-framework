<?php
class Kwc_Mail_Editable_ComponentsModel extends Kwf_Model_Data_Abstract
{
    public function __construct(array $config = array())
    {
        $data = array();
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_Mail_Editable_Component');
        foreach ($components as $c) {
            $a = Kwc_Admin::getInstance($c->componentClass);
            $data[] = array(
                'id' => $c->dbId,
                'name' => $c->getComponent()->getName(),
                'settings_controller_url' => $a->getControllerUrl(),
                'content_component_class' => $c->getChildComponent('-content')->componentClass,
            );
        }
        $this->_data = $data;
        parent::__construct($config);
    }

}

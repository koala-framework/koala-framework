<?php
class Vpc_Mail_Editable_ComponentsModel extends Vps_Model_Data_Abstract
{
    public function __construct(array $config = array())
    {
        $data = array();
        $components = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_Mail_Editable_Component');
        foreach ($components as $c) {
            $a = Vpc_Admin::getInstance($c->componentClass);
            $data[] = array(
                'id' => $c->dbId,
                'name' => Vps_Registry::get('trl')->trlStaticExecute(Vpc_Abstract::getSetting($c->componentClass, 'componentName')),
                'settings_controller_url' => $a->getControllerUrl(),
                'content_component_class' => $c->getChildComponent('-content')->componentClass,
            );
        }
        $this->_data = $data;
        parent::__construct($config);
    }

}

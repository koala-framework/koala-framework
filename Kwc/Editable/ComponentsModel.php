<?php
class Kwc_Editable_ComponentsModel extends Kwf_Model_Data_Abstract
{
    public function __construct(array $config = array())
    {
        $data = array();
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass(array('Kwc_Editable_Component', 'Kwc_Editable_Trl_Component'));
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        foreach ($components as $c) {
            if (Kwf_Registry::get('acl')->getComponentAcl()->isAllowed($user, $c)) {
                $data[] = array(
                    'id' => $c->dbId,
                    'name' => $c->getComponent()->getNameForEdit(),
                    'content_component_class' => $c->getChildComponent('-content')->componentClass,
                );
            }
        }
        $this->_data = $data;
        parent::__construct($config);
    }
}

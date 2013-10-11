<?php
class Kwc_Mail_Editable_ComponentsModel extends Kwf_Model_Data_Abstract
{
    public function __construct(array $config = array())
    {
        $data = array();
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass(array('Kwc_Mail_Editable_Component', 'Kwc_Mail_Editable_Trl_Component'));
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        foreach ($components as $c) {
            if (Kwf_Registry::get('acl')->getComponentAcl()->isAllowed($user, $c)) {
                $a = Kwc_Admin::getInstance($c->componentClass);
                $data[] = array(
                    'id' => $c->dbId,
                    'name' => Kwf_Trl::getInstance()->trlStaticExecute($c->getComponent()->getNameForEdit()),
                    'settings_controller_url' => $a->getControllerUrl(),
                    'content_component_class' => $c->getChildComponent('-content')->componentClass,
                );
            }
        }
        $this->_data = $data;
        parent::__construct($config);
    }

}

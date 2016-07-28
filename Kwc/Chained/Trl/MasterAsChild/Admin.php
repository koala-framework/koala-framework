<?php
class Kwc_Chained_Trl_MasterAsChild_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm($config)
    {
        $form = Kwc_Admin::getInstance(Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'))
            ->getPagePropertiesForm($config);
        if ($form) {
            $form->setIdTemplate('{0}-child');
            $ret = new Kwf_Form();
            $ret->setModel(new Kwf_Model_FnF());
            $ret->setCreateMissingRow(true);
            $ret->add($form);
            return $ret;
        }
        return null;
    }

    public function componentToString(Kwf_Component_Data $data)
    {
        $admin = Kwc_Admin::getInstance($data->chained->componentClass);
        return $admin->componentToString($data->getChildComponent('-child'));
    }
}

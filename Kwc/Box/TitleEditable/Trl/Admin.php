<?php
class Kwc_Box_TitleEditable_Trl_Admin extends Kwc_Chained_Trl_MasterAsChild_Admin
{
    public function getPagePropertiesForm($config)
    {
        $c = $config['component'];
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
}

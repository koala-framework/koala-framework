<?php
class Vps_User_Form extends Vps_Form
{
    protected $_permissions = array('save', 'add');
    protected $_userDataFormName = 'Vpc_User_Edit_Form_Form';

    protected function _init()
    {
        parent::_init();
        if (!$this->getModel()) {
            $this->setTable(Zend_Registry::get('userModel'));
        }
    }

    protected function _initFields()
    {
        parent::_initFields();

        $userEditForm = $this->fields->add(new $this->_userDataFormName('user'));
        $userEditForm->setIdTemplate('{0}');

        $userDirectory = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_User_Directory_Component');
        if ($userDirectory) {
            $detailClass = Vpc_Abstract::getChildComponentClass($userDirectory->componentClass, 'detail');
            $userEditForm->addUserForms($detailClass, array('general'));
            $userEditForm->fields['firstname']->setAllowBlank(true);
            $userEditForm->fields['lastname']->setAllowBlank(true);
        } else {
            $this->fields->add(new Vpc_User_Detail_General_Form('general', null))
                        ->setIdTemplate('{0}');
        }

        $config = Zend_Registry::get('config');
        $authedUser = Vps_Registry::get('userModel')->getAuthedUser();
        if (isset($authedUser->language) && $config->languages){
            $data = array();
            foreach ($config->languages as $key => $value){
                $data[$key] = $value;
            }
            $this->fields->add(new Vps_Form_Field_Select('language', trlVps('Language')))
            ->setValues($data);
        }
    }
}

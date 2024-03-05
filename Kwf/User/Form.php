<?php
class Kwf_User_Form extends Kwf_Form
{
    protected $_permissions = array('save', 'add');
    protected $_userDataFormName = 'Kwc_User_Edit_Form_FrontendForm';

    protected $_newUserRow = null;

    protected function _init()
    {
        parent::_init();
        if (!$this->getModel()) {
            $this->setModel(Kwf_Registry::get('userModel')->getEditModel());
        }
    }

    protected function _initFields()
    {
        parent::_initFields();

        $userEditForm = $this->fields->add(new $this->_userDataFormName('user'));
        $userEditForm->setIdTemplate('{0}');

        $root =  Kwf_Component_Data_Root::getInstance();
        if ($root) $userDirectory = $root->getComponentByClass('Kwc_User_Directory_Component');
        if ($root && isset($userDirectory) && $userDirectory) {
            $detailClass = Kwc_Abstract::getChildComponentClass($userDirectory->componentClass, 'detail');
            $userEditForm->addUserForms($detailClass, array('general'));
            $userEditForm->fields['firstname']->setAllowBlank(true);
            $userEditForm->fields['lastname']->setAllowBlank(true);
        } else {
            $this->fields->add(new Kwc_User_Detail_General_Form('general', null))
                        ->setIdTemplate('{0}');
        }

        $config = Zend_Registry::get('config');
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if (isset($authedUser->language) && $config->languages){
            $data = array();
            foreach ($config->languages as $key => $value){
                $data[$key] = $value;
            }
            $this->fields->add(new Kwf_Form_Field_Select('language', trlKwf('Language')))
            ->setValues($data);
        }
    }
}

<?php
class Vps_User_Form extends Vps_Form
{
    protected $_permissions = array('save', 'add');
    protected $_userDataFormName = 'Vpc_User_Edit_Form_FrontendForm';

    protected $_newUserRow = null;

    protected function _init()
    {
        parent::_init();
        if (!$this->getModel()) {
            $this->setModel(Zend_Registry::get('userModel'));
        }
    }

    protected function _initFields()
    {
        parent::_initFields();

        $userEditForm = $this->fields->add(new $this->_userDataFormName('user'));
        $userEditForm->setIdTemplate('{0}');

        $root =  Vps_Component_Data_Root::getInstance();
        if ($root) $userDirectory = $root->getComponentByClass('Vpc_User_Directory_Component');
        if ($root && isset($userDirectory) && $userDirectory) {
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
                $data[$value] = $value;
            }
            $this->fields->add(new Vps_Form_Field_Select('language', trlVps('Language')))
            ->setValues($data);
        }
    }

    public function getRow($parentRow = null)
    {
        $id = $this->_getIdByParentRow($parentRow);
        if (($id === 0 || $id === '0' || is_null($id)) && $this->_newUserRow) {
            return $this->_newUserRow;
        } else {
            return parent::getRow($parentRow);
        }
    }

    public function processInput($parentRow, $postData = array())
    {
        $id = $this->_getIdByParentRow($parentRow);
        if ($id === 0 || $id === '0' || is_null($id)) {
            $webcodeField = $this->getByName('webcode');
            // webcode = null setzt sich von selbst wenn er gewünscht ist (config)
            if (!$webcodeField) {
                // normaler benutzer der das hakerl im backend nicht setzen darf
                $webcode = null;
            } else if ($postData[$webcodeField->getFieldName()]) {
                // hakerl darf gesetzt werden und ist auch gesetzt
                $webcode = null;
            } else {
                // hakerl darf gesetzt werden und ist nicht gesetzt
                // webcode = '' bedeutet global
                $webcode = '';
            }
            $this->_newUserRow = $this->_model->createUserRow(
                $postData[$this->getByName('email')->getFieldName()],
                $webcode
            );
        }

        return parent::processInput($parentRow, $postData);
    }
}

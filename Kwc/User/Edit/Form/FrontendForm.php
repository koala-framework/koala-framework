<?php
class Kwc_User_Edit_Form_FrontendForm extends Kwf_Form
{
    protected $_newUserRow;

    protected function _init()
    {
        parent::_init();
        $this->setModel(Kwf_Registry::get('userModel')->getEditModel());
    }

    public function getRow($parentRow = null)
    {
        if ($this->_rowIsParentRow($parentRow)) {
            return $parentRow;
        }
        $id = $this->_getIdByParentRow($parentRow);
        if (($id === 0 || $id === '0' || is_null($id)) && $this->_newUserRow) {
            return $this->_newUserRow;
        } else {
            return parent::getRow($parentRow);
        }
    }

    public function processInput($parentRow, $postData = array())
    {
        if ($this->_model instanceof Kwf_User_EditModel) {
            $id = $this->_getIdByParentRow($parentRow);
            if ($id === 0 || $id === '0' || is_null($id)) {
                $email = null;
                if ($this->getByName('email') && isset($postData[$this->getByName('email')->getFieldName()])) {
                    $email = $postData[$this->getByName('email')->getFieldName()];
                }

                $this->_newUserRow = $this->_model->createUserRow(
                    $email
                );
            }
        }

        return parent::processInput($parentRow, $postData);
    }

    public function addUserForms($detailsClass, $forms)
    {
        $generators = Kwc_Abstract::getSetting($detailsClass, 'generators');
        $classes = $generators['child']['component'];
        foreach ($classes as $component => $class) {
            if ($forms == 'all' || in_array($component, $forms)) {
                $form = Kwc_Abstract_Form::createChildComponentForm($detailsClass, '-'.$component);
                if ($form->getModel() && $form->getModel() instanceof Kwf_User_Model) {
                    $form->setIdTemplate("{0}");
                } else {
                    $form->setIdTemplate("users_{0}-$component");
                }
                if ($form) {
                    $this->add($form);
                }
            }
        }
    }
}

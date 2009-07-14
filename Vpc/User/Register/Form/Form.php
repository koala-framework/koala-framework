<?php
class Vpc_User_Register_Form_Form extends Vpc_User_Edit_Form_Form
{
    protected $_newUserRow;

    public function initFields()
    {
        parent::initFields();
        $this->_setHidden($this);
    }

    //TODO: sollte mit Vps_Collection_Iterator_Recursive einfacher funktionieren
    private function _setHidden($f)
    {
        if ($f->getHideInRegister()) {
            $this->_setHidden2($f);
        }
        foreach ($f as $i) {
            if (is_object($i)) {
                $this->_setHidden($i);
            }
        }
    }
    private function _setHidden2($f)
    {
        $f->setHidden(true);
        $f->setSave(false);
        foreach ($f as $i) {
            if (is_object($i)) {
                $this->_setHidden2($i);
            }
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
            $email = null;
            if (isset($postData[$this->getByName('email')->getFieldName()])) {
                $email = $postData[$this->getByName('email')->getFieldName()];
            }

            $this->_newUserRow = $this->_model->createUserRow(
                $email, null
            );
        }

        return parent::processInput($parentRow, $postData);
    }
}

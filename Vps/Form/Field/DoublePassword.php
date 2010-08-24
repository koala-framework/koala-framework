<?php
class Vps_Form_Field_DoublePassword extends Vps_Form_Field_Abstract
{
    protected $_passwordField1;
    protected $_passwordField2;
    public function __construct($fieldName = null, $fieldLabel = null)
    {
        $this->_passwordField1 = new Vps_Form_Field_Password($fieldName, $fieldLabel);
        $this->_passwordField1->setAllowBlank(false);
        $this->_passwordField2 = new Vps_Form_Field_Password($fieldName.'_repeat', trlVps('repeat {0}', $fieldLabel));
        $this->_passwordField2->setSave(false);
        parent::__construct(null, null);
    }

    public function __clone()
    {
        $this->_passwordField1 = clone $this->_passwordField1;
        $this->_passwordField2 = clone $this->_passwordField2;
    }

    public function hasChildren()
    {
        return true;
    }
    public function getChildren()
    {
        $ret = new Vps_Collection_FormFields();
        $ret[] = $this->_passwordField1;
        $ret[] = $this->_passwordField2;
        return $ret;
    }
    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);
        if ($postData[$this->_passwordField1->getFieldName()] !=
                            $postData[$this->_passwordField2->getFieldName()])
        {
            $ret[] = array(
                'message' => trlVps("Passwords are different. Please try again."),
                'field' => $this->_passwordField1
            );
        }
        return $ret;
    }
    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $ret = array();
        $ret['items'] = array();
        $ret['items'][] = $this->_passwordField1->getTemplateVars($values, $fieldNamePostfix);
        $ret['items'][] = $this->_passwordField2->getTemplateVars($values, $fieldNamePostfix);
        return $ret;
    }

}

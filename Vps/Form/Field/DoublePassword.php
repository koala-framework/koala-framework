<?php
class Vps_Form_Field_DoublePassword extends Vps_Form_Field_Abstract
{
    protected $_passwordField1;
    protected $_passwordField2;
    public function __construct($fieldName = null, $fieldLabel = null)
    {
        $this->_passwordField1 = new Vps_Form_Field_Password($fieldName, $fieldLabel);
        $this->_passwordField2 = new Vps_Form_Field_Password($fieldName.'_repeat', trlVps('repeat %1', $fieldLabel));
        $this->_passwordField2->setSave(false);
        parent::__construct(null, null);
    }

    public function hasChildren()
    {
        return true;
    }
    public function getChildren()
    {
        return array($this->_passwordField1, $this->_passwordField2);
    }
    public function validate($postData)
    {
        $ret = parent::validate($postData);
        if ($postData[$this->_passwordField1->getFieldName()] !=
                            $postData[$this->_passwordField2->getFieldName()])
        {
            $name = $this->_passwordField1->getFieldLabel();
            if (!$name) $name = $this->_passwordField1->getName();
            $ret[] = $name.': '.trlVps("Passwords are different. Please try again.");
        }
        return $ret;
    }
}

<?php
/**
 * @package Form
 */
class Kwf_Form_Field_DoublePassword extends Kwf_Form_Field_Abstract
{
    protected $_passwordField1;
    protected $_passwordField2;
    public function __construct($fieldName = null, $fieldLabel = null)
    {
        $this->_passwordField1 = new Kwf_Form_Field_Password($fieldName, $fieldLabel);
        $this->_passwordField1->setAllowBlank(false);
        $this->_passwordField2 = new Kwf_Form_Field_Password($fieldName.'_repeat', trlKwfStatic('Repeat {0}'));
        $this->_passwordField2->setSave(false);
        parent::__construct(null, null);
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Kwf_Form_Field_DoublePasswordValidator(), 'samePassword');
    }

    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);
        $label1 = $this->_passwordField1->getFieldLabel();
        $label2 = $this->_passwordField2->getFieldLabel();
        $this->_passwordField2->setFieldLabel(str_replace('{0}', $label1, $label2));
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
        $ret = new Kwf_Collection_FormFields();
        $ret[] = $this->_passwordField1;
        $ret[] = $this->_passwordField2;
        return $ret;
    }
    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);
        if (isset($this->_validators['samePassword'])) {
            $password1 = $postData[$this->_passwordField1->getFieldName()];
            $password2 = $postData[$this->_passwordField2->getFieldName()];
            $validator = $this->_validators['samePassword'];
            if (!$validator->isValid(array($password1, $password2))) {
                $ret[] = array(
                    'messages' => $validator->getMessages(),
                    'field' => $this->_passwordField1
                );
            }
        }
        return $ret;
    }
    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = array();
        $ret['items'] = array();
        $ret['items'][] = $this->_passwordField1->getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $ret['items'][] = $this->_passwordField2->getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        return $ret;
    }

}

class Kwf_Form_Field_DoublePasswordValidator extends Zend_Validate_Abstract
{
    public function __construct($options = array())
    {
        $this->_messageTemplates['invalid'] = trlKwfStatic("Passwords are different. Please try again.");
    }

    public function isValid($passwords)
    {
        if ($passwords[0] != $passwords[1]) {
            $this->_error('invalid');
            return false;
        }
        return true;
    }
}


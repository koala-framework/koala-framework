<?php
/**
 * Base class for form fields that represent a textual value
 * @package Form
 */
class Kwf_Form_Field_SimpleAbstract extends Kwf_Form_Field_Abstract
{
    public function load($row, $postData = array())
    {
        $ret = array();
        if (array_key_exists($this->getFieldName(), $postData)) {
            $ret[$this->getFieldName()] = $postData[$this->getFieldName()];
        } else {
            if ($this->getSave() !== false && $row) {
                $ret[$this->getFieldName()] = $this->getData()->load($row);
            }
        }
        if (!isset($ret[$this->getFieldName()]) || is_null($ret[$this->getFieldName()])) {
            $ret[$this->getFieldName()] = $this->getDefaultValue();
        }
        $ret[$this->getFieldName()] = $this->_processLoaded($ret[$this->getFieldName()]);
        return array_merge($ret, parent::load($row, $postData));
    }

    protected function _processLoaded($value)
    {
        return $value;
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'blankText';
        $ret[] = 'comment';
        $ret[] = 'defaultValue';
        return $ret;
    }

    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getAllowBlank() === false
            || $this->getAllowBlank() === 0
            || $this->getAllowBlank() === '0'
        ) {
            $v = new Kwf_Validate_NotEmpty();
            if ($this->getBlankText()) {
                $v->setMessage(Kwf_Validate_NotEmpty::IS_EMPTY, $this->getBlankText());
            }
            $this->addValidator($v, 'notEmpty');
        }
        $this->addValidator(new Kwf_Validate_NoNewline(), 'noNewline');
        $this->addValidator(new Kwf_Validate_NoTags(), 'noTags');
    }

    public function validate($row, $postData)
    {
        $ret = parent::validate($row, $postData);

        $data = $this->_getValueFromPostData($postData);

        foreach ($this->getValidators() as $v) {
            // folgende if ist, weils es zB bei einem Date Validator keinen
            // sinn macht zu validieren wenn kein wert da ist. da macht dann
            // nur mehr der NotEmpty sinn

            if ((is_null($data) || $data === '') && !($v instanceof Zend_Validate_NotEmpty)) {
                continue;
            }

            if ($v instanceof Kwf_Validate_Row_Abstract) {
                $v->setField($this->getName());
                $isValid = $v->isValidRow($data, $row);
            } else {
                $isValid = $v->isValid($data);
            }
            if (!$isValid) {
                $ret[] = array(
                    'messages' => $v->getMessages(),
                    'field' => $this
                );
            }
        }

        return $ret;
    }

    public function prepareSave(Kwf_Model_Row_Interface $row, $postData)
    {
        parent::prepareSave($row, $postData);
        if ($this->getSave() !== false) {
            $save = false;
            if (array_key_exists($this->getFieldName(), $postData)) {
                //wenn postData gesetzt, speichern
                $save = true;
            } else {
                $pk = $row->getModel()->getPrimaryKey();
                if (!$row->$pk) {
                    //wenn postData nicht gesetzt, aber die row neu eingefügt wird,
                    //auch speichern - mit DefaultValue (kommt von _getValueFromPostData)
                    $save = true;
                }
            }
            if ($save) {
                $this->getData()->save($row, $this->_getValueToSaveFromPostData($postData));
            }
        }
    }

    protected function _getValueToSaveFromPostData($postData)
    {
        return $this->_getValueFromPostData($postData);
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = $this->getDefaultValue();
        return (string)$postData[$fieldName];
    }

    public final function getValueFromPostData($postData)
    {
        return $this->_getValueFromPostData($postData);
    }

    /**
     * Set weather this field is allowed to be blank when submitting the form
     *
     * defaults to true
     *
     * @param bool
     */
    public function setAllowBlank($value)
    {
        return $this->setProperty('allowBlank', $value);
    }

    /**
     * Error text to display if the allow blank validation fails (defaults to "This field is required")
     *
     * @param string
     * @see setAllowBlank
     */
    public function setBlankText($value)
    {
        return $this->setProperty('blankText', $value);
    }

    /**
     * @internal
     * @deprecated
     */
    public function setEmptyMessage($value)
    {
        return $this->setBlankText($value);
    }

    /**
     * Set a comment displayed right to the form field
     *
     * @param string
     */
    public function setComment($value)
    {
        return $this->setProperty('comment', $value);
    }

    /**
     * Set the default value for this field used when adding a new entry
     */
    public function setDefaultValue($value)
    {
        return $this->setProperty('defaultValue', $value);
    }
}

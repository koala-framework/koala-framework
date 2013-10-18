<?php
/**
 * A standard textarea field
 *
 * @package Form
 */
class Kwf_Form_Field_TextArea extends Kwf_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('textarea');
        $this->setWidth(100);
        $this->setHeight(60);
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        unset($this->_validators['noNewline']);
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $name = $this->getFieldName();
        $value = isset($values[$name]) ? (string)$values[$name] : $this->getDefaultValue();
        $ret = Kwf_Form_Field_SimpleAbstract::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        //todo: escapen
        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<textarea id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" ";
        $width = $this->getWidth();
        if (is_numeric($width)) {
            $width .= 'px';
        }
        $ret['html'] .= "style=\"width: $width; height: {$this->getHeight()}px\"";

        $cls = $this->getCls();
        if ($this->getClearOnFocus() && $value == $this->getDefaultValue()) {
            $cls .= ' kwfClearOnFocus';
        }
        if ($cls) $ret['html'] .= ' class="'.trim($cls).'"';
        if ($this->getEmptyText()) {
            $ret['html'] .= ' placeholder="'.htmlspecialchars($this->getEmptyText()).'"';
        }

        $ret['html'] .= '>';
        $ret['html'] .= htmlspecialchars($value);
        $ret['html'] .= "</textarea>";
        return $ret;
    }
}

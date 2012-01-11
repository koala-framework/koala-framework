<?php
class Kwf_Form_Field_Static extends Kwf_Form_Field_Abstract
{
    //setText

    public function __construct($text, $fieldLabel = null)
    {
        parent::__construct(null, $fieldLabel);
        $this->setXtype('staticfield');
        $this->setText($text);
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'text';
        return $ret;
    }

    public function setFieldLabel($v)
    {
        $this->setLabelSeparator($v ? ':' : '');
        return $this->setProperty('fieldLabel', $v);
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        $ret['id'] = $idPrefix.$this->getFieldName().$fieldNamePostfix;
        $cls = $this->getCls();
        $ret['html'] = "<div class=\"$cls\">" . $this->getText() . "</div>";
        return $ret;
    }

}
